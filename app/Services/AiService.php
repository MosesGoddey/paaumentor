<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AiService
{
    private string $model   = 'gemini-2.5-flash';
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';

    public function generateLearningPath(string $topic, string $level, int $weeks): array
    {
        $prompt = "You are an educational curriculum designer for PAAUMENTOR, a peer mentorship platform at Prince Abubakar Audu University (PAAU), Anyigba, Nigeria.\n\n"
            . "Generate a structured learning path for:\n"
            . "- Topic: {$topic}\n"
            . "- Learner level: {$level}\n"
            . "- Duration: {$weeks} weeks\n\n"
            . "Return ONLY valid JSON, no markdown, no explanation:\n"
            . '{"modules":[{"title":"...","tasks":[{"title":"...","description":"...","max_score":100}]}]}' . "\n\n"
            . "Rules: 3-5 modules, 2-4 tasks each. Tasks must be practical and assessable by a mentor.";

        $json = $this->call($prompt);
        $data = json_decode($this->extractJson($json), true);
        return is_array($data) ? $data : ['modules' => []];
    }

    public function matchMentors(string $goals, array $mentors): array
    {
        if (empty($mentors)) return [];

        $list = collect($mentors)->map(
            fn($m) => "ID:{$m['id']} Name:{$m['name']} Dept:{$m['department']} Level:{$m['level']} Skills:{$m['skills']} Bio:{$m['bio']}"
        )->implode("\n");

        $prompt = "You are a mentor-matching assistant for PAAUMENTOR, a university peer mentorship platform.\n\n"
            . "Mentee goals and interests:\n{$goals}\n\n"
            . "Available mentors:\n{$list}\n\n"
            . "Pick the top 5 most suitable mentors. Give a one-sentence reason for each.\n"
            . "Respond with ONLY a raw JSON array — no markdown fences, no explanation, no extra text:\n"
            . '[{"mentor_id":1,"reason":"..."},{"mentor_id":2,"reason":"..."}]';

        $json = $this->call($prompt);
        $data = json_decode($this->extractJson($json), true);
        return is_array($data) ? $data : [];
    }

    public function studyBuddy(string $message, array $history = []): string
    {
        $system = "You are a helpful academic study assistant for students at Prince Abubakar Audu University (PAAU), Anyigba, Nigeria. "
            . "Help students understand academic concepts, answer subject questions, and guide their learning. "
            . "Be clear, concise, and educational. Format responses with markdown where helpful. "
            . "If asked something entirely unrelated to academics or personal development, politely redirect.";

        $contents = array_map(
            fn($h) => [
                'role'  => $h['role'] === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => $h['content']]],
            ],
            $history
        );
        $contents[] = ['role' => 'user', 'parts' => [['text' => $message]]];

        return $this->callWithContents($contents, $system);
    }

    private function extractJson(string $text): string
    {
        // Strip markdown fences Gemini adds despite being told not to
        $text = preg_replace('/```json\s*/i', '', $text);
        $text = preg_replace('/```\s*/i', '', $text);
        $text = trim($text);

        // Pull out the first JSON object or array even if surrounded by prose
        if (preg_match('/(\{.*\}|\[.*\])/s', $text, $m)) {
            return $m[1];
        }

        return $text;
    }

    private function call(string $prompt): string
    {
        return $this->callWithContents([
            ['role' => 'user', 'parts' => [['text' => $prompt]]],
        ]);
    }

    private function callWithContents(array $contents, string $system = ''): string
    {
        $body = [
            'contents'         => $contents,
            'generationConfig' => ['maxOutputTokens' => 2048],
        ];

        if ($system) {
            $body['systemInstruction'] = ['parts' => [['text' => $system]]];
        }

        $url = $this->baseUrl . $this->model . ':generateContent?key=' . config('services.gemini.key');

        $response = Http::timeout(60)->post($url, $body);

        if ($response->failed()) {
            throw new \RuntimeException('AI service unavailable: ' . $response->status());
        }

        return $response->json('candidates.0.content.parts.0.text', '');
    }
}
