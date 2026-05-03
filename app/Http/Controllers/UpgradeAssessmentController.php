<?php

namespace App\Http\Controllers;

use App\Models\{MentorUpgradeRequest, UpgradeAssessment, UpgradeAssessmentAttempt, Notification, User};
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpgradeAssessmentController extends Controller
{
    public function show(MentorUpgradeRequest $upgradeRequest)
    {
        abort_unless(Auth::id() === $upgradeRequest->mentee_id, 403);
        abort_unless($upgradeRequest->isPendingAssessment(), 422);

        $assessment = UpgradeAssessment::where('upgrade_request_id', $upgradeRequest->id)->first();

        $attemptCount = UpgradeAssessmentAttempt::where('upgrade_request_id', $upgradeRequest->id)
            ->whereNotNull('completed_at')->count();

        $lastFailed = UpgradeAssessmentAttempt::where('upgrade_request_id', $upgradeRequest->id)
            ->whereNotNull('completed_at')->where('passed', false)
            ->latest('completed_at')->first();

        $cooldownActive = $lastFailed && $lastFailed->completed_at->diffInHours(now()) < 24;
        $cooldownEndsAt = $cooldownActive ? $lastFailed->completed_at->addHours(24) : null;

        return view('upgrade.assessment-show', compact(
            'upgradeRequest', 'assessment', 'attemptCount', 'cooldownActive', 'cooldownEndsAt'
        ));
    }

    public function start(MentorUpgradeRequest $upgradeRequest)
    {
        abort_unless(Auth::id() === $upgradeRequest->mentee_id, 403);
        abort_unless($upgradeRequest->isPendingAssessment(), 422);

        $assessment = UpgradeAssessment::where('upgrade_request_id', $upgradeRequest->id)->first();
        abort_if(!$assessment || !$assessment->questions_ready, 422, 'Questions are not ready yet. Please try again shortly.');

        $attemptCount = UpgradeAssessmentAttempt::where('upgrade_request_id', $upgradeRequest->id)
            ->whereNotNull('completed_at')->count();
        abort_if($attemptCount >= 3, 422, 'Maximum attempts (3) reached.');

        $lastFailed = UpgradeAssessmentAttempt::where('upgrade_request_id', $upgradeRequest->id)
            ->whereNotNull('completed_at')->where('passed', false)
            ->latest('completed_at')->first();
        if ($lastFailed && $lastFailed->completed_at->diffInHours(now()) < 24) {
            return redirect()->route('upgrade-assessment.show', $upgradeRequest)
                ->with('error', 'Please wait 24 hours before your next attempt.');
        }

        // Cancel any unfinished attempt
        UpgradeAssessmentAttempt::where('upgrade_request_id', $upgradeRequest->id)
            ->whereNull('completed_at')->delete();

        $questionIds = $assessment->questions()
            ->inRandomOrder()
            ->limit($assessment->questions_per_attempt)
            ->pluck('id')
            ->toArray();

        $attempt = UpgradeAssessmentAttempt::create([
            'upgrade_assessment_id' => $assessment->id,
            'user_id'               => Auth::id(),
            'upgrade_request_id'    => $upgradeRequest->id,
            'question_ids'          => $questionIds,
            'started_at'            => now(),
            'max_score'             => count($questionIds),
        ]);

        $questions = $assessment->questions()->whereIn('id', $questionIds)->get()->keyBy('id');
        $orderedQuestions = collect($questionIds)->map(fn($id) => $questions[$id]);

        return view('upgrade.assessment-take', compact('attempt', 'upgradeRequest', 'orderedQuestions', 'assessment'));
    }

    public function tabSwitch(UpgradeAssessmentAttempt $attempt)
    {
        abort_unless($attempt->user_id === Auth::id(), 403);
        abort_if($attempt->isCompleted(), 422);

        $attempt->increment('tab_switches');

        if ($attempt->tab_switches >= 2) {
            return response()->json(['action' => 'submit', 'message' => 'Auto-submitting: two tab switches detected.']);
        }

        return response()->json(['action' => 'warn', 'switches' => $attempt->tab_switches]);
    }

    public function submit(Request $request, UpgradeAssessmentAttempt $attempt)
    {
        abort_unless($attempt->user_id === Auth::id(), 403);
        abort_if($attempt->isCompleted(), 422);

        $upgradeRequest = $attempt->upgradeRequest;
        $assessment     = $attempt->upgradeAssessment;

        $questions = $assessment->questions()
            ->whereIn('id', $attempt->question_ids)
            ->get()->keyBy('id');

        $raw     = $request->input('answers', []);
        $answers = [];
        $correct = 0;

        foreach ($attempt->question_ids as $qid) {
            $chosen        = isset($raw[$qid]) ? (int) $raw[$qid] : -1;
            $answers[$qid] = $chosen;
            if (isset($questions[$qid]) && $chosen === $questions[$qid]->correct_answer) {
                $correct++;
            }
        }

        $maxScore     = count($attempt->question_ids);
        $scorePercent = $maxScore > 0 ? (int) round(($correct / $maxScore) * 100) : 0;
        $passed       = $scorePercent >= $assessment->passing_score;

        $attempt->update([
            'answers'      => $answers,
            'score'        => $scorePercent,
            'passed'       => $passed,
            'completed_at' => now(),
        ]);

        if ($passed) {
            $upgradeRequest->update(['status' => 'pending']);

            // Notify mentor to write recommendation
            Notification::create([
                'user_id' => $upgradeRequest->mentor_id,
                'type'    => 'upgrade_recommendation_request',
                'title'   => 'Recommendation Request',
                'body'    => "{$upgradeRequest->mentee->full_name} passed the mentor upgrade assessment ({$scorePercent}%) and needs your recommendation.",
                'data'    => ['upgrade_request_id' => $upgradeRequest->id],
            ]);

            // Notify mentee
            Notification::create([
                'user_id' => $upgradeRequest->mentee_id,
                'type'    => 'upgrade_assessment_passed',
                'title'   => 'Assessment Passed! 🎉',
                'body'    => "You scored {$scorePercent}% on the mentor upgrade assessment. Your mentor {$upgradeRequest->mentor->full_name} has been notified to write your recommendation.",
                'data'    => ['upgrade_request_id' => $upgradeRequest->id],
            ]);
        } else {
            $attemptsUsed = UpgradeAssessmentAttempt::where('upgrade_request_id', $upgradeRequest->id)
                ->whereNotNull('completed_at')->count();
            $remaining = max(0, 3 - $attemptsUsed);

            Notification::create([
                'user_id' => $upgradeRequest->mentee_id,
                'type'    => 'upgrade_assessment_failed',
                'title'   => 'Upgrade Assessment Not Passed',
                'body'    => "You scored {$scorePercent}% (required: {$assessment->passing_score}%). " .
                             ($remaining > 0
                                ? "You have {$remaining} attempt(s) remaining. Please wait 24 hours before retrying."
                                : "You have used all 3 attempts. Please speak with your mentor."),
                'data'    => ['upgrade_request_id' => $upgradeRequest->id],
            ]);
        }

        return redirect()->route('upgrade-assessment.result', $attempt);
    }

    public function retryGenerate(MentorUpgradeRequest $upgradeRequest)
    {
        abort_unless(Auth::id() === $upgradeRequest->mentee_id, 403);
        abort_unless($upgradeRequest->isPendingAssessment(), 422);

        $assessment = UpgradeAssessment::firstOrCreate(
            ['upgrade_request_id' => $upgradeRequest->id],
            ['questions_ready' => false]
        );

        if ($assessment->questions_ready) {
            return redirect()->route('upgrade-assessment.show', $upgradeRequest)
                ->with('info', 'Questions are already ready!');
        }

        $ok = (new GeminiService())->generateUpgradeQuestions($assessment);

        if ($ok) {
            return redirect()->route('upgrade-assessment.show', $upgradeRequest)
                ->with('success', 'Questions generated! You can now start the assessment.');
        }

        return redirect()->route('upgrade-assessment.show', $upgradeRequest)
            ->with('error', 'Generation failed. Please try again in a moment.');
    }

    public function result(UpgradeAssessmentAttempt $attempt)
    {
        abort_unless($attempt->user_id === Auth::id(), 403);
        abort_unless($attempt->isCompleted(), 404);

        $upgradeRequest = $attempt->upgradeRequest;
        $assessment     = $attempt->upgradeAssessment;

        $attemptsUsed = UpgradeAssessmentAttempt::where('upgrade_request_id', $upgradeRequest->id)
            ->whereNotNull('completed_at')->count();

        return view('upgrade.assessment-result', compact('attempt', 'upgradeRequest', 'assessment', 'attemptsUsed'));
    }
}
