<?php

namespace App\Http\Controllers;

use App\Models\{CertificateRequest, Assessment, AssessmentAttempt, Notification, Certificate, User};
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssessmentController extends Controller
{
    // Show the assessment lobby / take page
    public function show(CertificateRequest $certRequest)
    {
        $user = Auth::user();
        abort_unless($certRequest->mentee_id === $user->id, 403);
        abort_unless($certRequest->isPendingAssessment(), 422);

        $assessment = Assessment::where('learning_path_id', $certRequest->learning_path_id)->first();

        // Count completed attempts for this certificate request
        $attemptCount = AssessmentAttempt::where('certificate_request_id', $certRequest->id)
            ->whereNotNull('completed_at')
            ->count();

        // Check cooldown: last failed attempt must be > 24h ago
        $lastAttempt = AssessmentAttempt::where('certificate_request_id', $certRequest->id)
            ->whereNotNull('completed_at')
            ->where('passed', false)
            ->latest('completed_at')
            ->first();

        $cooldownActive = $lastAttempt && $lastAttempt->completed_at->diffInHours(now()) < 24;
        $cooldownEndsAt = $cooldownActive ? $lastAttempt->completed_at->addHours(24) : null;

        return view('assessments.show', compact(
            'certRequest', 'assessment', 'attemptCount', 'cooldownActive', 'cooldownEndsAt'
        ));
    }

    // Start a new attempt — returns the test page
    public function start(CertificateRequest $certRequest)
    {
        $user = Auth::user();
        abort_unless($certRequest->mentee_id === $user->id, 403);
        abort_unless($certRequest->isPendingAssessment(), 422);

        $assessment = Assessment::where('learning_path_id', $certRequest->learning_path_id)->first();
        abort_if(!$assessment || !$assessment->questions_ready, 422, 'Questions are not ready yet. Please try again shortly.');

        // Enforce max attempts
        $attemptCount = AssessmentAttempt::where('certificate_request_id', $certRequest->id)
            ->whereNotNull('completed_at')->count();
        abort_if($attemptCount >= 3, 422, 'Maximum attempts (3) reached for this assessment.');

        // Enforce 24-hour cooldown after a failed attempt
        $lastFailed = AssessmentAttempt::where('certificate_request_id', $certRequest->id)
            ->whereNotNull('completed_at')->where('passed', false)
            ->latest('completed_at')->first();
        if ($lastFailed && $lastFailed->completed_at->diffInHours(now()) < 24) {
            return redirect()->route('assessment.show', $certRequest)
                ->with('error', 'Please wait 24 hours before your next attempt.');
        }

        // Cancel any unfinished in-progress attempt
        AssessmentAttempt::where('certificate_request_id', $certRequest->id)
            ->whereNull('completed_at')->delete();

        // Pick random question IDs for this attempt
        $questionIds = $assessment->questions()
            ->inRandomOrder()
            ->limit($assessment->questions_per_attempt)
            ->pluck('id')
            ->toArray();

        $attempt = AssessmentAttempt::create([
            'assessment_id'          => $assessment->id,
            'user_id'                => $user->id,
            'certificate_request_id' => $certRequest->id,
            'question_ids'           => $questionIds,
            'started_at'             => now(),
            'max_score'              => count($questionIds),
        ]);

        $questions = $assessment->questions()->whereIn('id', $questionIds)->get()->keyBy('id');
        // Preserve the random order
        $orderedQuestions = collect($questionIds)->map(fn($id) => $questions[$id]);

        return view('assessments.take', compact('attempt', 'certRequest', 'orderedQuestions', 'assessment'));
    }

    // Record a tab switch (AJAX)
    public function tabSwitch(AssessmentAttempt $attempt)
    {
        abort_unless($attempt->user_id === Auth::id(), 403);
        abort_if($attempt->isCompleted(), 422);

        $attempt->increment('tab_switches');

        if ($attempt->tab_switches >= 2) {
            return response()->json(['action' => 'submit', 'message' => 'Auto-submitting: two tab switches detected.']);
        }

        return response()->json(['action' => 'warn', 'switches' => $attempt->tab_switches]);
    }

    // Submit all answers and calculate score
    public function submit(Request $request, AssessmentAttempt $attempt)
    {
        abort_unless($attempt->user_id === Auth::id(), 403);
        abort_if($attempt->isCompleted(), 422);

        $certRequest = $attempt->certificateRequest;
        $assessment  = $attempt->assessment;

        // Load the correct questions for this attempt
        $questions = $assessment->questions()
            ->whereIn('id', $attempt->question_ids)
            ->get()
            ->keyBy('id');

        $raw      = $request->input('answers', []);
        $answers  = [];
        $correct  = 0;

        foreach ($attempt->question_ids as $qid) {
            $chosen = isset($raw[$qid]) ? (int) $raw[$qid] : -1;
            $answers[$qid] = $chosen;
            if (isset($questions[$qid]) && $chosen === $questions[$qid]->correct_answer) {
                $correct++;
            }
        }

        $maxScore  = count($attempt->question_ids);
        $scorePercent = $maxScore > 0 ? (int) round(($correct / $maxScore) * 100) : 0;
        $passed    = $scorePercent >= $assessment->passing_score;

        $attempt->update([
            'answers'      => $answers,
            'score'        => $scorePercent,
            'passed'       => $passed,
            'completed_at' => now(),
        ]);

        if ($passed) {
            // Next gate: mentor must submit a reflection before verifier sees it
            $certRequest->update([
                'status'               => 'pending_mentor_reflection',
                'assessment_score'     => $scorePercent,
                'assessment_passed_at' => now(),
            ]);

            // Notify the mentor to write their reflection
            Notification::create([
                'user_id' => $certRequest->mentor_id,
                'type'    => 'mentor_reflection_required',
                'title'   => 'Reflection Required — Your Mentee Passed!',
                'body'    => "{$certRequest->mentee->full_name} scored {$scorePercent}% on the \"{$certRequest->learningPath->title}\" assessment. Please submit your mentor reflection to proceed to certificate verification.",
                'data'    => ['certificate_request_id' => $certRequest->id],
            ]);

            // Notify the mentee
            Notification::create([
                'user_id' => $certRequest->mentee_id,
                'type'    => 'assessment_passed',
                'title'   => 'Assessment Passed! ',
                'body'    => "You scored {$scorePercent}% on the \"{$certRequest->learningPath->title}\" assessment. Your mentor has been asked to submit their reflection before the verifier reviews your certificate.",
                'data'    => ['certificate_request_id' => $certRequest->id],
            ]);
        } else {
            $attemptsUsed = AssessmentAttempt::where('certificate_request_id', $certRequest->id)
                ->whereNotNull('completed_at')->count();
            $remaining = max(0, 3 - $attemptsUsed);

            Notification::create([
                'user_id' => $certRequest->mentee_id,
                'type'    => 'assessment_failed',
                'title'   => 'Assessment Not Passed',
                'body'    => "You scored {$scorePercent}% (required: {$assessment->passing_score}%). " .
                             ($remaining > 0
                                ? "You have {$remaining} attempt(s) remaining. Please wait 24 hours before retrying."
                                : "You have used all 3 attempts. Please contact your mentor for guidance."),
                'data'    => ['certificate_request_id' => $certRequest->id],
            ]);
        }

        return redirect()->route('assessment.result', $attempt);
    }

    // Show the result page
    public function result(AssessmentAttempt $attempt)
    {
        abort_unless($attempt->user_id === Auth::id(), 403);
        abort_unless($attempt->isCompleted(), 404);

        $certRequest = $attempt->certificateRequest;
        $assessment  = $attempt->assessment;

        $attemptsUsed = AssessmentAttempt::where('certificate_request_id', $certRequest->id)
            ->whereNotNull('completed_at')->count();

        return view('assessments.result', compact('attempt', 'certRequest', 'assessment', 'attemptsUsed'));
    }

    // Retry: regenerate questions if they failed to generate
    public function regenerate(CertificateRequest $certRequest)
    {
        abort_unless(Auth::user()->isAdmin() || Auth::user()->isVerifier(), 403);

        $assessment = Assessment::where('learning_path_id', $certRequest->learning_path_id)->first();
        abort_unless($assessment, 404);

        $service = new GeminiService();
        $ok = $service->generateQuestions($assessment);

        return back()->with($ok ? 'success' : 'error',
            $ok ? 'Questions regenerated successfully.' : 'Gemini API call failed. Check your API key and try again.');
    }
}
