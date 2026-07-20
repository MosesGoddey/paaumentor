<?php

namespace App\Http\Controllers;

use App\Mail\AppNotificationMail;
use App\Models\{User, Mentorship, Skill, Notification, Conversation, Rating};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Mail};
use Illuminate\Validation\Rule;
use App\Services\AiService;
use Illuminate\Support\Str;

class MentorController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = User::where(fn($q) => $q->where('role', 'mentor')->orWhere('role', 'alumni'))
                     ->where('is_active', true)
                     ->where('is_verified', true)
                     ->where('mentor_status', 'active')
                     ->whereDoesntHave('mentorMentorships', fn($q) =>
                         $q->where('mentee_id', $user->id)->whereIn('status', ['active', 'pending'])
                     )
                     ->with(['hasSkills', 'ratings', 'mentorMentorships']);

        if ($request->filled('skill')) {
            $query->whereHas('hasSkills', fn($q) => $q->where('name', $request->skill));
        }
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(fn($qb) =>
                $qb->where('first_name', 'like', "%$q%")
                   ->orWhere('last_name', 'like', "%$q%")
                   ->orWhere('bio', 'like', "%$q%")
            );
        }

        $tierOrder = ['lead' => 3, 'senior' => 2, 'junior' => 1];

        $mentors = $query->get()->map(fn($m) => [
            'mentor' => $m,
            'score'  => $m->matchScore($user),
            'rating' => $m->average_rating,
            'tier'   => $tierOrder[$m->mentor_tier] ?? 1,
        ])->sortByDesc(fn($m) => [$m['tier'], $m['score']])->values();

        $skills = Skill::orderBy('name')->get();

        return view('mentors.index', compact('mentors', 'skills', 'user'));
    }

    public function myMentors()
    {
        $user = Auth::user();
        $mentorships = $user->menteeMentorships()
            ->with(['mentor.hasSkills', 'mentor.ratings', 'conversation',
                    'ratings' => fn($q) => $q->where('rater_id', $user->id)])
            ->orderByRaw("FIELD(status, 'active', 'pending', 'rejected')")
            ->get();

        return view('mentors.my', compact('mentorships'));
    }

    public function myMentees()
    {
        $user = Auth::user();
        $mentorships = $user->mentorMentorships()
            ->with(['mentee.hasSkills', 'conversation'])
            ->orderByRaw("FIELD(status, 'active', 'pending', 'rejected')")
            ->get();

        return view('mentors.mentees', compact('mentorships'));
    }

    public function show(User $mentor)
    {
        $mentor->load(['hasSkills', 'ratings.rater', 'certificates']);
        $reviews = $mentor->ratings()->with('rater')->latest()->take(5)->get();

        $ratingBreakdown = [];
        for ($i = 5; $i >= 1; $i--) {
            $ratingBreakdown[$i] = $mentor->ratings()->where('score', $i)->count();
        }

        // Existing relationship (if any) so the page can show the correct
        // state instead of always offering the request form.
        $existingMentorship = Mentorship::where('mentor_id', $mentor->id)
                                        ->where('mentee_id', Auth::id())
                                        ->whereIn('status', ['pending', 'active'])
                                        ->first();

        return view('mentors.show', compact('mentor', 'reviews', 'ratingBreakdown', 'existingMentorship'));
    }

    public function requestMentorship(Request $request, User $mentor)
    {
        $mentee = Auth::user();

        $request->validate([
            'topic'        => 'required|string|max:255',
            'goal'         => 'nullable|string|max:1000',
        ]);

        $exists = Mentorship::where('mentor_id', $mentor->id)
                            ->where('mentee_id', $mentee->id)
                            ->whereIn('status', ['pending', 'active'])
                            ->exists();

        if ($exists) {
            return back()->with('error', 'You already have an active or pending request with this mentor.');
        }

        $mentorship = Mentorship::create([
            'mentor_id'    => $mentor->id,
            'mentee_id'    => $mentee->id,
            'topic'        => $request->topic,
            'goal'         => $request->goal,
            'status'       => 'pending',
        ]);

        Notification::create([
            'user_id' => $mentor->id,
            'type'    => 'mentorship_request',
            'title'   => 'New mentorship request',
            'body'    => "{$mentee->full_name} wants to be your mentee — Topic: {$request->topic}",
            'data'    => ['mentorship_id' => $mentorship->id],
        ]);

        try {
            Mail::to($mentor->email)->send(new AppNotificationMail(
                title: 'New Mentorship Request — PAAUMENTOR',
                body:  "Hi {$mentor->first_name},\n\n{$mentee->full_name} has sent you a mentorship request.\n\nTopic: {$request->topic}" . ($request->goal ? "\nGoal: {$request->goal}" : "") . "\n\nLog in to your dashboard to accept or decline.",
                actionText: 'View Request',
                actionUrl:  url('/dashboard'),
            ));
        } catch (\Exception) {}

        // Stay on the mentor's profile so the success toast is shown and the
        // button flips to the "pending" state. A pending request has no chat
        // yet, so redirecting to chat.index was wrong (and dropped the flash
        // message on chat.index's follow-up redirect to the first conversation).
        return redirect()->route('mentors.show', $mentor)
                         ->with('success', 'Mentorship request sent! You will be notified when they respond.');
    }

    public function respond(Request $request, Mentorship $mentorship)
    {
        abort_if(Auth::id() !== $mentorship->mentor_id, 403);

        $request->validate(['action' => Rule::in(['accept', 'reject'])]);

        $mentorship->load(['mentor', 'mentee']);

        if ($request->action === 'accept') {
            $mentorship->update(['status' => 'active', 'started_at' => now()]);

            Conversation::firstOrCreate(['mentorship_id' => $mentorship->id]);

            Notification::create([
                'user_id' => $mentorship->mentee_id,
                'type'    => 'mentorship_accepted',
                'title'   => 'Mentorship request accepted!',
                'body'    => "{$mentorship->mentor->full_name} accepted your mentorship request.",
                'data'    => ['mentorship_id' => $mentorship->id],
            ]);

            try {
                Mail::to($mentorship->mentee->email)->send(new AppNotificationMail(
                    title: 'Your Mentorship Request Was Accepted! ',
                    body:  "Hi {$mentorship->mentee->first_name},\n\nGreat news! {$mentorship->mentor->full_name} has accepted your mentorship request.\n\nTopic: {$mentorship->topic}\n\nYou can now message your mentor directly through the platform.",
                    actionText: 'Start Messaging',
                    actionUrl:  url('/chat'),
                ));
            } catch (\Exception) {}
        } else {
            $mentorship->update(['status' => 'rejected']);

            Notification::create([
                'user_id' => $mentorship->mentee_id,
                'type'    => 'mentorship_rejected',
                'title'   => 'Mentorship request declined',
                'body'    => "{$mentorship->mentor->full_name} could not take on your mentorship request at this time.",
                'data'    => ['mentorship_id' => $mentorship->id],
            ]);

            try {
                Mail::to($mentorship->mentee->email)->send(new AppNotificationMail(
                    title: 'Mentorship Request Update — PAAUMENTOR',
                    body:  "Hi {$mentorship->mentee->first_name},\n\n{$mentorship->mentor->full_name} was unable to accept your mentorship request at this time.\n\nDon't worry — there are many other mentors available. Browse and send another request!",
                    actionText: 'Find Another Mentor',
                    actionUrl:  url('/mentors'),
                ));
            } catch (\Exception) {}
        }

        return back()->with('success', 'Response recorded.');
    }

    public function rate(Request $request, Mentorship $mentorship)
    {
        abort_unless(Auth::id() === $mentorship->mentee_id, 403);
        abort_unless($mentorship->status === 'active', 403, 'You can only rate active mentorships.');

        $request->validate([
            'score'  => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:500',
        ]);

        Rating::updateOrCreate(
            ['mentorship_id' => $mentorship->id, 'rater_id' => Auth::id()],
            ['ratee_id' => $mentorship->mentor_id, 'score' => $request->score, 'review' => $request->review]
        );

        return back()->with('success', 'Rating submitted. Thank you!');
    }

    public function aiMatch(Request $request)
    {
        $request->validate(['goals' => 'required|string|max:1000']);

        $mentors = User::where(fn($q) => $q->where('role', 'mentor')->orWhere('role', 'alumni'))
            ->where('is_active', true)
            ->where('is_verified', true)
            ->where('mentor_status', 'active')
            ->with('hasSkills')
            ->get()
            ->map(fn($m) => [
                'id'         => $m->id,
                'name'       => $m->full_name,
                'department' => $m->department,
                'level'      => $m->level,
                'skills'     => $m->hasSkills->pluck('name')->join(', '),
                'bio'        => Str::limit($m->bio ?? '', 150),
            ])->toArray();

        try {
            $matches = app(AiService::class)->matchMentors($request->goals, $mentors);
            return response()->json(['matches' => $matches]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'AI is temporarily unavailable.'], 503);
        }
    }
}
