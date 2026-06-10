<?php

namespace App\Http\Controllers;

use App\Models\{MentorSession, Mentorship, Notification, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SessionController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $mentorshipIds = Mentorship::where('mentor_id', $user->id)
                                   ->orWhere('mentee_id', $user->id)
                                   ->pluck('id');

        $upcoming = MentorSession::whereIn('mentorship_id', $mentorshipIds)
                                 ->whereIn('status', ['scheduled', 'in_progress'])
                                 ->with('mentorship.mentor', 'mentorship.mentee')
                                 ->orderBy('scheduled_at')
                                 ->get();

        $past = MentorSession::whereIn('mentorship_id', $mentorshipIds)
                             ->whereIn('status', ['completed', 'cancelled'])
                             ->with('mentorship.mentor', 'mentorship.mentee')
                             ->orderByDesc('started_at')
                             ->get();

        $activeMentorships = Mentorship::where(function ($q) use ($user) {
            $q->where('mentor_id', $user->id)->orWhere('mentee_id', $user->id);
        })->where('status', 'active')->with('mentor', 'mentee')->get();

        return view('sessions.index', compact('user', 'upcoming', 'past', 'activeMentorships'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'mentorship_id' => 'required|exists:mentorships,id',
            'title'         => 'required|string|max:150',
            'description'   => 'nullable|string|max:500',
            'type'          => 'required|in:video,voice,chat',
            'scheduled_at'  => 'required|date|after:1 minute ago',
        ]);

        $mentorship = Mentorship::findOrFail($data['mentorship_id']);
        abort_unless(
            $mentorship->mentor_id === $user->id || $mentorship->mentee_id === $user->id,
            403
        );

        $room = in_array($data['type'], ['video', 'voice'])
            ? 'paau-' . strtolower(Str::random(10))
            : null;

        $session = MentorSession::create([
            ...$data,
            'status'       => 'scheduled',
            'scheduled_at' => $data['scheduled_at'],
            'room'         => $room,
        ]);

        // Notify the other participant
        $otherId = $mentorship->mentor_id === $user->id
            ? $mentorship->mentee_id
            : $mentorship->mentor_id;

        Notification::create([
            'user_id' => $otherId,
            'type'    => 'session_scheduled',
            'title'   => $user->full_name . ' scheduled a session',
            'body'    => '"' . $data['title'] . '" on ' . \Carbon\Carbon::parse($data['scheduled_at'])->format('D, M j \a\t g:i A'),
            'data'    => ['session_id' => $session->id],
        ]);

        return redirect()->route('sessions.index')->with('success', 'Session scheduled! The other participant has been notified.');
    }

    public function room(MentorSession $session)
    {
        $user = Auth::user();
        $session->load(['mentorship', 'skillExchangeRequest.exchange']);

        abort_unless(in_array($user->id, $session->participantIds()), 403);
        abort_unless($session->room, 404);

        if ($session->status === 'scheduled') {
            $session->update(['status' => 'in_progress', 'started_at' => now()]);
        }

        $otherParticipantId = collect($session->participantIds())
            ->first(fn($id) => $id !== $user->id);
        $otherParticipant = User::find($otherParticipantId);

        return view('sessions.room', compact('session', 'user', 'otherParticipant'));
    }

    public function complete(MentorSession $session)
    {
        $session->load(['mentorship', 'skillExchangeRequest.exchange']);

        abort_unless(in_array(Auth::id(), $session->participantIds()), 403);
        abort_unless(in_array($session->status, ['scheduled', 'in_progress']), 422);

        $now = now();
        $duration = $session->started_at
            ? (int) $session->started_at->diffInMinutes($now)
            : null;

        $session->update([
            'status'           => 'completed',
            'ended_at'         => $now,
            'duration_minutes' => $duration,
        ]);

        return back()->with('success', 'Session marked as completed.');
    }
}
