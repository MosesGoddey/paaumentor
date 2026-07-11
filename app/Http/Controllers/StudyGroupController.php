<?php

namespace App\Http\Controllers;

use App\Models\{StudyGroup, StudyGroupMember, StudyGroupMessage, User, Notification};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudyGroupController extends Controller
{
    public function index()
    {
        $user   = Auth::user();
        $myGroups = StudyGroup::whereHas('members', fn($q) => $q->where('user_id', $user->id))
                              ->withCount('members')->with('creator')->latest()->get();

        $otherGroups = StudyGroup::where('is_open', true)
                                 ->whereDoesntHave('members', fn($q) => $q->where('user_id', $user->id))
                                 ->withCount('members')->with('creator')->latest()->get();

        return view('study-groups.index', compact('user', 'myGroups', 'otherGroups'));
    }

    public function show(StudyGroup $studyGroup)
    {
        $user = Auth::user();
        abort_unless($studyGroup->isMember($user) || $studyGroup->is_open, 403);

        $studyGroup->load(['creator', 'members.user']);
        $messages = $studyGroup->messages()->with('sender')->orderBy('created_at')->get();

        // Group admins can add members directly — list users not yet in the group
        $addableUsers = collect();
        if ($studyGroup->isAdmin($user)) {
            $addableUsers = User::where('is_active', true)
                ->whereNotIn('role', ['admin', 'verifier'])
                ->whereNotIn('id', $studyGroup->members->pluck('user_id'))
                ->orderBy('first_name')
                ->get(['id', 'first_name', 'last_name', 'level']);
        }

        return view('study-groups.show', compact('user', 'studyGroup', 'messages', 'addableUsers'));
    }

    public function addMember(Request $request, StudyGroup $studyGroup)
    {
        $user = Auth::user();
        abort_unless($studyGroup->isAdmin($user), 403);

        $data = $request->validate(['user_id' => 'required|exists:users,id']);

        abort_if($studyGroup->members()->where('user_id', $data['user_id'])->exists(), 400);
        abort_if($studyGroup->members()->count() >= $studyGroup->max_members, 422);

        StudyGroupMember::create([
            'study_group_id' => $studyGroup->id,
            'user_id'        => $data['user_id'],
            'role'           => 'member',
        ]);

        $newMember = User::find($data['user_id']);
        Notification::create([
            'user_id' => $newMember->id,
            'type'    => 'group',
            'title'   => 'Added to study group: ' . $studyGroup->name,
            'body'    => $user->full_name . ' added you to the "' . $studyGroup->name . '" study group (' . $studyGroup->topic . ').',
        ]);

        return back()->with('success', $newMember->full_name . ' has been added to the group.');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'topic'       => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'max_members' => 'nullable|integer|min:2|max:100',
        ]);

        $group = StudyGroup::create([
            ...$data,
            'created_by'  => Auth::id(),
            'max_members' => $data['max_members'] ?? 20,
        ]);

        StudyGroupMember::create([
            'study_group_id' => $group->id,
            'user_id'        => Auth::id(),
            'role'           => 'admin',
        ]);

        return redirect()->route('study-groups.show', $group)->with('success', 'Study group created!');
    }

    public function join(StudyGroup $studyGroup)
    {
        $user = Auth::user();
        abort_if($studyGroup->isMember($user), 400);
        abort_unless($studyGroup->is_open, 403);
        abort_if($studyGroup->members()->count() >= $studyGroup->max_members, 422);

        StudyGroupMember::create([
            'study_group_id' => $studyGroup->id,
            'user_id'        => $user->id,
            'role'           => 'member',
        ]);

        return redirect()->route('study-groups.show', $studyGroup)->with('success', 'You joined the group!');
    }

    public function leave(StudyGroup $studyGroup)
    {
        $user = Auth::user();
        abort_unless($studyGroup->isMember($user), 400);
        abort_if($studyGroup->isAdmin($user) && $studyGroup->members()->count() === 1, 422);

        $studyGroup->members()->where('user_id', $user->id)->delete();

        // Transfer admin if leaving admin was the only admin
        if ($studyGroup->isAdmin($user) && !$studyGroup->members()->where('role', 'admin')->exists()) {
            $studyGroup->members()->first()?->update(['role' => 'admin']);
        }

        return redirect()->route('study-groups.index')->with('success', 'You left the group.');
    }

    public function notifyCall(Request $request, StudyGroup $studyGroup)
    {
        $user = Auth::user();
        abort_unless($studyGroup->isMember($user), 403);

        $request->validate(['type' => 'required|in:video,voice,screen']);

        $labels = ['video' => 'video call', 'voice' => 'voice call', 'screen' => 'screen share'];
        $label  = $labels[$request->type];

        $otherMemberIds = $studyGroup->members()
                                     ->where('user_id', '!=', $user->id)
                                     ->pluck('user_id');

        foreach ($otherMemberIds as $memberId) {
            \App\Models\Notification::create([
                'user_id' => $memberId,
                'type'    => 'call',
                'title'   => $user->full_name . ' started a ' . $label . ' in ' . $studyGroup->name,
                'body'    => 'Click Join to enter the ' . $label . '.',
                'data'    => [
                    'call_type'     => $request->type,
                    'room'          => $studyGroup->roomName(),
                    'group_id'      => $studyGroup->id,
                    'caller_id'     => $user->id,
                    'caller_name'   => $user->full_name,
                ],
            ]);
        }

        return response()->json(['ok' => true]);
    }

    public function sendMessage(Request $request, StudyGroup $studyGroup)
    {
        $user = Auth::user();
        abort_unless($studyGroup->isMember($user), 403);

        $request->validate([
            'body' => 'required_without:file|nullable|string|max:5000',
            'file' => 'nullable|file|max:20480',
        ]);

        $filePath = $fileName = null;
        $type = 'text';

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('group-files', 'public');
            $fileName = $request->file('file')->getClientOriginalName();
            $type = 'file';
        }

        StudyGroupMessage::create([
            'study_group_id' => $studyGroup->id,
            'sender_id'      => $user->id,
            'body'           => $request->body,
            'file_path'      => $filePath,
            'file_name'      => $fileName,
            'type'           => $type,
        ]);

        return back();
    }
}
