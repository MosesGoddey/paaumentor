<?php

namespace App\Services;

use App\Models\{Assessment, AssessmentQuestion, LearningPath, UpgradeAssessment, UpgradeAssessmentQuestion, MentorUpgradeRequest};
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    private string $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key', '');
    }

    /**
     * Generate 30 MCQ questions for the given learning path and store them on the assessment.
     * Returns true on success, false on failure.
     */
    public function generateQuestions(Assessment $assessment): bool
    {
        if (empty($this->apiKey)) {
            Log::warning('GeminiService: GEMINI_API_KEY is not set — skipping question generation.');
            return false;
        }

        $path = $assessment->learningPath()->with('modules.tasks')->first();
        $prompt = $this->buildPrompt($path);

        try {
            $response = Http::timeout(90)->post("{$this->endpoint}?key={$this->apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'temperature'     => 0.7,
                    // gemini-2.5-flash is a reasoning model — its "thinking" tokens are
                    // billed against maxOutputTokens. 4096 is too small for 30 full MCQs
                    // plus reasoning, which truncates the JSON to nothing. Give ample room.
                    'maxOutputTokens' => 20000,
                ],
            ]);

            if (!$response->successful()) {
                Log::error('GeminiService: API error', ['status' => $response->status(), 'body' => $response->body()]);
                return false;
            }

            $text = $response->json('candidates.0.content.parts.0.text', '');
            $questions = $this->parseQuestions($text);

            if (count($questions) < 10) {
                Log::warning('GeminiService: Too few questions parsed', ['count' => count($questions)]);
                return false;
            }

            $this->storeQuestions($assessment, $questions);
            return true;

        } catch (\Throwable $e) {
            Log::error('GeminiService: Exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Generate 30 MCQ questions for a mentor upgrade assessment based on the mentee's completed paths.
     */
    public function generateUpgradeQuestions(UpgradeAssessment $assessment): bool
    {
        if (empty($this->apiKey)) {
            Log::warning('GeminiService: GEMINI_API_KEY is not set — skipping upgrade question generation.');
            return false;
        }

        $upgradeRequest = $assessment->upgradeRequest()->with('mentee')->first();
        $completedPaths = $upgradeRequest->mentee
            ->learningPathsAsMentee()
            ->with('modules.tasks')
            ->get()
            ->filter(fn($p) => $p->isComplete());

        $prompt = $this->buildUpgradePrompt($upgradeRequest->mentee->full_name, $completedPaths);

        try {
            $response = Http::timeout(90)->post("{$this->endpoint}?key={$this->apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'temperature'     => 0.7,
                    // gemini-2.5-flash reasoning tokens count against maxOutputTokens;
                    // 4096 truncates a 30-question payload to empty. Give ample room.
                    'maxOutputTokens' => 20000,
                ],
            ]);

            if (!$response->successful()) {
                Log::error('GeminiService: Upgrade API error', ['status' => $response->status(), 'body' => $response->body()]);
                return false;
            }

            $text      = $response->json('candidates.0.content.parts.0.text', '');
            $questions = $this->parseQuestions($text);

            if (count($questions) < 10) {
                Log::warning('GeminiService: Too few upgrade questions parsed', ['count' => count($questions)]);
                return false;
            }

            $this->storeUpgradeQuestions($assessment, $questions);
            return true;

        } catch (\Throwable $e) {
            Log::error('GeminiService: Upgrade exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function buildUpgradePrompt(string $menteeName, $completedPaths): string
    {
        $pathSummaries = $completedPaths->map(function ($path, $i) {
            $modules = $path->modules->map(fn($m, $j) =>
                "  Module " . ($j + 1) . ": {$m->title}"
            )->implode("\n");
            return "Learning Path " . ($i + 1) . ": \"{$path->title}\"\n{$modules}";
        })->implode("\n\n");

        if (empty(trim($pathSummaries))) {
            $pathSummaries = "General Computer Science and software development topics relevant to a university-level mentorship program.";
        }

        return <<<PROMPT
You are creating a mentor qualification assessment for PAAUMENTOR at Prince Abubakar Audu University, Anyigba, Nigeria.

A student named {$menteeName} has applied to become a peer mentor. They completed the following learning paths:

{$pathSummaries}

Generate exactly 30 multiple-choice questions that test whether this student has TEACHING-LEVEL understanding of these topics — deep enough to explain, debug, and guide another student through the material.

Rules:
- Each question must have exactly 4 options
- Exactly one option is correct
- Questions should test ability to EXPLAIN and APPLY concepts, not just recall definitions
- Vary difficulty: 8 easy, 14 medium, 8 challenging
- No repeated or trivially similar questions
- Focus on concepts a mentor would need to teach clearly to a junior student

Respond ONLY with a valid JSON array. No markdown fences, no extra text — just the raw JSON:
[
  {
    "question": "...",
    "options": ["Option A text", "Option B text", "Option C text", "Option D text"],
    "correct": 0
  }
]
Where "correct" is the 0-based index of the correct option (0=A, 1=B, 2=C, 3=D).
PROMPT;
    }

    private function storeUpgradeQuestions(UpgradeAssessment $assessment, array $questions): void
    {
        $assessment->questions()->delete();

        $rows = [];
        foreach (array_values($questions) as $i => $q) {
            $rows[] = [
                'upgrade_assessment_id' => $assessment->id,
                'question'              => $q['question'],
                'options'               => json_encode(array_values($q['options'])),
                'correct_answer'        => (int) $q['correct'],
                'points'                => 1,
                'order'                 => $i,
                'created_at'            => now(),
                'updated_at'            => now(),
            ];
        }

        UpgradeAssessmentQuestion::insert($rows);

        $assessment->update([
            'questions_ready'        => true,
            'questions_generated_at' => now(),
        ]);
    }

    private function buildPrompt(LearningPath $path): string
    {
        $modules = $path->modules->map(function ($module, $i) {
            $tasks = $module->tasks->pluck('title')->implode(', ');
            return "  Module " . ($i + 1) . ": {$module->title}" . ($tasks ? "\n    Tasks: {$tasks}" : '');
        })->implode("\n");

        return <<<PROMPT
You are creating a university-level academic assessment for a peer mentorship platform called PAAUMENTOR at Prince Abubakar Audu University, Anyigba, Nigeria.

Learning Path: "{$path->title}"
Description: "{$path->description}"
Course Structure:
{$modules}

Generate exactly 30 multiple-choice questions to assess a student's mastery of this learning path before a certificate is issued.

Rules:
- Each question must have exactly 4 options
- Exactly one option is correct
- Test deep understanding, not just surface recall
- Vary difficulty: 10 easy, 12 medium, 8 challenging
- No repeated or trivially similar questions

Respond ONLY with a valid JSON array. No markdown fences, no extra text, no explanation — just the raw JSON:
[
  {
    "question": "...",
    "options": ["Option A text", "Option B text", "Option C text", "Option D text"],
    "correct": 0
  }
]
Where "correct" is the 0-based index of the correct option (0=A, 1=B, 2=C, 3=D).
PROMPT;
    }

    private function parseQuestions(string $text): array
    {
        // Strip markdown code fences if Gemini adds them anyway
        $text = preg_replace('/```json\s*/i', '', $text);
        $text = preg_replace('/```\s*/i', '', $text);
        $text = trim($text);

        // Find the JSON array even if there is surrounding text
        if (preg_match('/\[.*\]/s', $text, $matches)) {
            $text = $matches[0];
        }

        $data = json_decode($text, true);
        if (!is_array($data)) return [];

        return array_filter($data, fn($q) =>
            isset($q['question'], $q['options'], $q['correct']) &&
            is_array($q['options']) && count($q['options']) === 4 &&
            is_int($q['correct']) && $q['correct'] >= 0 && $q['correct'] <= 3
        );
    }

    private function storeQuestions(Assessment $assessment, array $questions): void
    {
        $assessment->questions()->delete();

        $rows = [];
        foreach (array_values($questions) as $i => $q) {
            $rows[] = [
                'assessment_id'  => $assessment->id,
                'question'       => $q['question'],
                'options'        => json_encode(array_values($q['options'])),
                'correct_answer' => (int) $q['correct'],
                'points'         => 1,
                'order'          => $i,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }

        AssessmentQuestion::insert($rows);

        $assessment->update([
            'questions_ready'        => true,
            'questions_generated_at' => now(),
        ]);
    }
}
