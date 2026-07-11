<?php

namespace App\Http\Controllers;

use App\Models\{GroupSession, Mentorship, Notification, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GroupSessionController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $hosted = GroupSession::where('host_id', $user->id)
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->with('members')
            ->orderBy('scheduled_at')
            ->get();

        $invited = GroupSession::whereHas('members', fn($q) => $q->where('user_id', $user->id))
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->with('host', 'members')
            ->orderBy('scheduled_at')
            ->get();

        $past = GroupSession::where(function ($q) use ($user) {
            $q->where('host_id', $user->id)
              ->orWhereHas('members', fn($q2) => $q2->where('user_id', $user->id));
        })->whereIn('status', ['completed', 'cancelled'])
          ->with('host', 'members')
          ->orderByDesc('ended_at')
          ->get();

        // People the current user can invite (mentors/mentees + study group peers)
        $connections = $this->connections($user);

        return view('sessions.group', compact('hosted', 'invited', 'past', 'connections'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'title'           => 'required|string|max:150',
            'description'     => 'nullable|string|max:500',
            'type'            => 'required|in:video,voice',
            'scheduled_at'    => 'required|date|after:1 minute ago',
            'invitees'        => 'nullable|array',
            'invitees.*'      => 'exists:users,id',
            'max_participants' => 'nullable|integer|min:2|max:100',
        ]);

        $session = GroupSession::create([
            'host_id'          => $user->id,
            'title'            => $data['title'],
            'description'      => $data['description'] ?? null,
            'type'             => $data['type'],
            'room'             => 'paau-grp-' . strtolower(Str::random(8)),
            'status'           => 'scheduled',
            'max_participants' => $data['max_participants'] ?? 50,
            'scheduled_at'     => $data['scheduled_at'],
        ]);

        // Add host as member with host role
        $session->members()->attach($user->id, ['role' => 'host']);

        // Attach invitees + send notifications
        $invitees = collect($data['invitees'] ?? []);
        foreach ($invitees as $inviteeId) {
            $session->members()->attach($inviteeId, ['role' => 'member']);

            Notification::create([
                'user_id' => $inviteeId,
                'type'    => 'session_scheduled',
                'title'   => $user->full_name . ' invited you to a group session',
                'body'    => '"' . $data['title'] . '" on ' . \Carbon\Carbon::parse($data['scheduled_at'])->format('D, M j \a\t g:i A'),
                'data'    => ['group_session_id' => $session->id],
            ]);
        }

        return redirect()->route('group-sessions.index')->with('success', 'Group session created! Invitees have been notified.');
    }

    public function room(GroupSession $groupSession)
    {
        $user = Auth::user();

        abort_unless($groupSession->isParticipant($user->id), 403);
        abort_unless(in_array($groupSession->status, ['scheduled', 'in_progress']), 404);

        if ($groupSession->status === 'scheduled') {
            $groupSession->update(['status' => 'in_progress', 'started_at' => now()]);
        }

        // Mark joined_at for this member
        $groupSession->members()->updateExistingPivot($user->id, ['joined_at' => now()]);

        $groupSession->load('host', 'members');
        $isHost = $groupSession->host_id === $user->id;

        return view('sessions.group-room', compact('groupSession', 'user', 'isHost'));
    }

    public function complete(GroupSession $groupSession)
    {
        abort_unless($groupSession->isParticipant(Auth::id()), 403);
        abort_unless(in_array($groupSession->status, ['scheduled', 'in_progress']), 422);

        $now = now();
        $duration = $groupSession->started_at
            ? (int) $groupSession->started_at->diffInMinutes($now)
            : null;

        $groupSession->update([
            'status'           => 'completed',
            'ended_at'         => $now,
            'duration_minutes' => $duration,
        ]);

        return response()->json(['ok' => true]);
    }

    private function connections(User $user): \Illuminate\Support\Collection
    {
        $mentorshipUserIds = Mentorship::where('mentor_id', $user->id)
            ->orWhere('mentee_id', $user->id)
            ->where('status', 'active')
            ->get()
            ->flatMap(fn($m) => [$m->mentor_id, $m->mentee_id])
            ->unique()
            ->reject(fn($id) => $id === $user->id);

        return User::whereIn('id', $mentorshipUserIds)->get(['id', 'first_name', 'last_name', 'role']);
    }
}
