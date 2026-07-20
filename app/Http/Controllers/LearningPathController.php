<?php

namespace App\Http\Controllers;

use App\Models\{LearningPath, LearningTask, TaskSubmission, Certificate, Notification, CertificateRequest, Assessment};
use App\Services\{AiService, GeminiService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LearningPathController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isMentor()) {
            $paths = $user->learningPathsAsmentor()
                          ->with(['mentee', 'modules.tasks.submissions'])
                          ->where('status', '!=', 'archived')
                          ->latest()
                          ->get();
            return view('learning.mentor-index', compact('user', 'paths'));
        }

        $paths = $user->learningPathsAsMentee()
                      ->with(['modules.tasks.submissions' => fn($q) => $q->where('user_id', $user->id),
                              'mentor', 'certificate'])
                      ->where('status', '!=', 'archived')
                      ->get()
                      ->map(fn($lp) => ['path' => $lp, 'progress' => $lp->progress]);

        return view('learning.index', compact('user', 'paths'));
    }

    public function show(LearningPath $learningPath)
    {
        $this->authorize('view', $learningPath);

        $user = Auth::user();
        $learningPath->load([
            'modules.tasks.submissions' => fn($q) => $q->where('user_id', $user->id),
            'mentor',
            'certificate',
        ]);

        $certRequest = CertificateRequest::where('learning_path_id', $learningPath->id)->first();

        return view('learning.show', [
            'path'        => $learningPath,
            'user'        => $user,
            'progress'    => $learningPath->progress,
            'certRequest' => $certRequest,
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        abort_unless($user->isMentor(), 403);

        $mentees = $user->mentorMentorships()
                        ->where('status', 'active')
                        ->with('mentee')
                        ->get()
                        ->pluck('mentee');

        return view('learning.create', compact('user', 'mentees'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->isMentor(), 403);

        $data = $request->validate([
            'mentee_ids'                    => 'required|array|min:1',
            'mentee_ids.*'                  => 'required|exists:users,id',
            'title'                         => 'required|string|max:200',
            'description'                   => 'nullable|string|max:2000',
            'due_date'                      => 'nullable|date',
            'modules'                       => 'required|array|min:1',
            'modules.*.title'               => 'required|string|max:200',
            'modules.*.tasks'               => 'nullable|array',
            'modules.*.tasks.*.title'       => 'required|string|max:200',
            'modules.*.tasks.*.description' => 'nullable|string|max:1000',
            'modules.*.tasks.*.max_score'   => 'nullable|integer|min:1|max:1000',
            'modules.*.tasks.*.is_locked'   => 'nullable',
        ]);

        // Every selected mentee must be an active mentee of this mentor.
        $menteeIds = array_values(array_unique($data['mentee_ids']));
        $validMentees = $user->mentorMentorships()
                             ->where('status', 'active')
                             ->whereIn('mentee_id', $menteeIds)
                             ->pluck('mentee_id')
                             ->all();

        abort_unless(
            count($validMentees) === count($menteeIds),
            403, 'One or more selected users are not your active mentees.'
        );

        // Create an independent copy of the path (with its own modules, tasks
        // and progress) for each mentee, so grading and certificates stay
        // per-mentee.
        foreach ($menteeIds as $menteeId) {
            $path = LearningPath::create([
                'mentor_id'   => $user->id,
                'mentee_id'   => $menteeId,
                'title'       => $data['title'],
                'description' => $data['description'] ?? null,
                'due_date'    => $data['due_date'] ?? null,
                'status'      => 'active',
            ]);

            foreach ($data['modules'] as $mOrder => $moduleData) {
                $module = $path->modules()->create(['title' => $moduleData['title'], 'order' => $mOrder]);
                foreach (($moduleData['tasks'] ?? []) as $tOrder => $taskData) {
                    $module->tasks()->create([
                        'title'       => $taskData['title'],
                        'description' => $taskData['description'] ?? null,
                        'max_score'   => $taskData['max_score'] ?? 100,
                        'is_locked'   => isset($taskData['is_locked']),
                        'order'       => $tOrder,
                    ]);
                }
            }

            Notification::create([
                'user_id' => $menteeId,
                'type'    => 'learning_path_created',
                'title'   => 'New Learning Path Assigned!',
                'body'    => "{$user->full_name} created a new learning path \"{$path->title}\" for you.",
                'data'    => ['learning_path_id' => $path->id],
            ]);
        }

        $count = count($menteeIds);
        $msg = $count === 1
            ? 'Learning path created successfully!'
            : "Learning path created for {$count} mentees successfully!";

        return redirect()->route('learning.index')->with('success', $msg);
    }

    public function edit(LearningPath $learningPath)
    {
        $this->authorize('manage', $learningPath);
        $learningPath->load('modules.tasks');

        $hasSubmissions = TaskSubmission::whereHas('task.module', fn($q) =>
            $q->where('learning_path_id', $learningPath->id)
        )->exists();

        return view('learning.edit', [
            'path'           => $learningPath,
            'user'           => Auth::user(),
            'hasSubmissions' => $hasSubmissions,
        ]);
    }

    public function update(Request $request, LearningPath $learningPath)
    {
        $this->authorize('manage', $learningPath);

        $hasSubmissions = TaskSubmission::whereHas('task.module', fn($q) =>
            $q->where('learning_path_id', $learningPath->id)
        )->exists();

        $rules = [
            'title'       => 'required|string|max:200',
            'description' => 'nullable|string|max:2000',
            'due_date'    => 'nullable|date',
        ];

        if (!$hasSubmissions) {
            $rules = array_merge($rules, [
                'modules'                       => 'required|array|min:1',
                'modules.*.title'               => 'required|string|max:200',
                'modules.*.tasks'               => 'nullable|array',
                'modules.*.tasks.*.title'       => 'required|string|max:200',
                'modules.*.tasks.*.description' => 'nullable|string|max:1000',
                'modules.*.tasks.*.max_score'   => 'nullable|integer|min:1|max:1000',
                'modules.*.tasks.*.is_locked'   => 'nullable',
            ]);
        }

        $data = $request->validate($rules);

        $learningPath->update([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'due_date'    => $data['due_date'] ?? null,
        ]);

        if (!$hasSubmissions && isset($data['modules'])) {
            $learningPath->modules()->each(fn($m) => $m->tasks()->delete());
            $learningPath->modules()->delete();

            foreach ($data['modules'] as $mOrder => $moduleData) {
                $module = $learningPath->modules()->create(['title' => $moduleData['title'], 'order' => $mOrder]);
                foreach (($moduleData['tasks'] ?? []) as $tOrder => $taskData) {
                    $module->tasks()->create([
                        'title'       => $taskData['title'],
                        'description' => $taskData['description'] ?? null,
                        'max_score'   => $taskData['max_score'] ?? 100,
                        'is_locked'   => isset($taskData['is_locked']),
                        'order'       => $tOrder,
                    ]);
                }
            }
        }

        return redirect()->route('learning.show', $learningPath)->with('success', 'Learning path updated.');
    }

    public function destroy(LearningPath $learningPath)
    {
        $this->authorize('manage', $learningPath);
        $learningPath->delete();
        return redirect()->route('learning.index')->with('success', 'Learning path deleted.');
    }

    public function aiGenerate(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:200',
            'level' => 'required|in:Beginner,Intermediate,Advanced',
            'weeks' => 'required|integer|min:1|max:24',
        ]);
        try {
            $result = app(AiService::class)->generateLearningPath(
                $request->topic, $request->level, (int) $request->weeks
            );
            return response()->json($result);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'AI is temporarily unavailable.'], 503);
        }
    }

    public function grade(LearningPath $learningPath)
    {
        $this->authorize('manage', $learningPath);
        $learningPath->load([
            'mentee',
            'modules.tasks.submissions' => fn($q) => $q->where('user_id', $learningPath->mentee_id),
        ]);

        return view('learning.grade', [
            'path' => $learningPath,
            'user' => Auth::user(),
        ]);
    }

    public function gradeSubmission(Request $request, TaskSubmission $submission)
    {
        $submission->load('task.module.learningPath');
        $path = $submission->task->module->learningPath;
        $this->authorize('manage', $path);

        $data = $request->validate([
            'score'    => ['nullable', 'integer', 'min:0', 'max:' . $submission->task->max_score],
            'feedback' => 'nullable|string|max:2000',
            'status'   => 'required|in:graded,rejected',
        ]);

        $submission->update([
            'score'    => $data['score'] ?? null,
            'feedback' => $data['feedback'] ?? null,
            'status'   => $data['status'],
        ]);

        Notification::create([
            'user_id' => $submission->user_id,
            'type'    => 'task_graded',
            'title'   => $data['status'] === 'graded' ? 'Task Graded!' : 'Task Returned',
            'body'    => $data['status'] === 'graded'
                ? "Your submission for \"{$submission->task->title}\" has been graded."
                : "Your submission for \"{$submission->task->title}\" was returned. Please review and resubmit.",
            'data'    => ['learning_path_id' => $path->id],
        ]);

        if ($data['status'] === 'graded' && $path->fresh()->isComplete()) {
            $this->createCertificateRequest($path, $submission->user);
        }

        return back()->with('success', 'Submission graded.');
    }

    public function submitTask(Request $request, LearningTask $task)
    {
        $user = Auth::user();
        $request->validate([
            'notes' => 'nullable|string|max:2000',
            'file'  => 'nullable|file|max:10240',
        ]);

        $update = ['notes' => $request->notes, 'status' => 'submitted'];

        if ($request->hasFile('file')) {
            $update['file_path'] = $request->file('file')->store('submissions', 'public');
        }

        TaskSubmission::updateOrCreate(
            ['learning_task_id' => $task->id, 'user_id' => $user->id],
            $update
        );

        return back()->with('success', 'Task submitted! Your mentor will review it shortly.');
    }

    private function createCertificateRequest(LearningPath $path, $mentee): void
    {
        // Prevent duplicate requests
        if (CertificateRequest::where('learning_path_id', $path->id)->exists()) return;

        $certRequest = CertificateRequest::create([
            'learning_path_id' => $path->id,
            'mentee_id'        => $mentee->id,
            'mentor_id'        => $path->mentor_id,
            'status'           => 'pending_assessment',
        ]);

        // Create the assessment record and trigger Gemini question generation
        $assessment = Assessment::firstOrCreate(['learning_path_id' => $path->id]);
        if (!$assessment->questions_ready) {
            $service = new GeminiService();
            $service->generateQuestions($assessment);
        }

        // Notify the mentee to take the assessment
        Notification::create([
            'user_id' => $mentee->id,
            'type'    => 'assessment_ready',
            'title'   => 'Assessment Ready — Final Step for Your Certificate!',
            'body'    => "You've completed all tasks in \"{$path->title}\"! Take the end-of-path assessment to proceed to certificate verification.",
            'data'    => ['certificate_request_id' => $certRequest->id],
        ]);
    }
}
