<?php

namespace App\Http\Controllers;

use App\Models\{Conversation, Message};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Cache};

class ChatController extends Controller
{
    private function userConversations(int $userId)
    {
        return Conversation::where(function ($q) use ($userId) {
            $q->whereHas('mentorship', fn($q2) => $q2->where('mentor_id', $userId)->orWhere('mentee_id', $userId))
              ->orWhereHas('skillExchangeRequest', fn($q2) =>
                  $q2->where('requester_id', $userId)
                     ->orWhereHas('exchange', fn($q3) => $q3->where('user_id', $userId)));
        })
        ->with([
            'mentorship.mentor',
            'mentorship.mentee',
            'skillExchangeRequest.exchange.user',
            'skillExchangeRequest.requester',
            'messages' => fn($q) => $q->latest()->take(1),
        ])
        ->latest('last_message_at');
    }

    private function authorizeConversation(Conversation $conversation): void
    {
        $conversation->load(['mentorship', 'skillExchangeRequest.exchange']);
        abort_unless(in_array(Auth::id(), $conversation->participantIds()) || Auth::user()->isAdmin(), 403);
    }

    public function index()
    {
        $user          = Auth::user();
        $conversations = $this->userConversations($user->id)->get();

        $activeConversation = $conversations->first();
        $messages = $activeConversation
            ? $activeConversation->messages()->with('sender')->orderBy('created_at')->get()
            : collect();

        if ($activeConversation) {
            $activeConversation->messages()
                               ->where('sender_id', '!=', $user->id)
                               ->whereNull('read_at')
                               ->update(['read_at' => now()]);
        }

        return view('chat.index', compact('user', 'conversations', 'activeConversation', 'messages'));
    }

    public function show(Conversation $conversation)
    {
        $this->authorizeConversation($conversation);
        $user     = Auth::user();
        $messages = $conversation->messages()->with('sender')->orderBy('created_at')->get();

        $conversation->messages()
                     ->where('sender_id', '!=', $user->id)
                     ->whereNull('read_at')
                     ->update(['read_at' => now()]);

        $conversations = $this->userConversations($user->id)->get();

        return view('chat.index', compact('user', 'conversations', 'messages'))
               ->with('activeConversation', $conversation);
    }

    public function notifyCall(Request $request, Conversation $conversation)
    {
        $this->authorizeConversation($conversation);
        $request->validate(['type' => 'required|in:video,voice,screen']);

        $caller  = Auth::user();
        $ids     = $conversation->participantIds();
        $otherId = collect($ids)->first(fn($id) => $id !== $caller->id);
        abort_unless($otherId, 403);

        $roomName = 'paaumentor-conv-' . $conversation->id . '-' . substr(md5(config('app.key')), 0, 12);
        $labels   = ['video' => 'video call', 'voice' => 'voice call', 'screen' => 'screen share'];
        $label    = $labels[$request->type];
        $typeMap  = ['screen' => 'video'];
        $sessionType = $typeMap[$request->type] ?? $request->type;

        $sessionData = [
            'title'        => ucfirst($label) . ' with ' . $caller->full_name,
            'type'         => $sessionType,
            'room'         => $roomName,
            'status'       => 'in_progress',
            'started_at'   => now(),
            'scheduled_at' => now(),
        ];

        if ($conversation->mentorship_id) {
            $sessionData['mentorship_id'] = $conversation->mentorship_id;
        } else {
            $sessionData['skill_exchange_request_id'] = $conversation->skill_exchange_request_id;
        }

        $session = \App\Models\MentorSession::create($sessionData);

        \App\Models\Notification::create([
            'user_id' => $otherId,
            'type'    => 'call',
            'title'   => $caller->full_name . ' is starting a ' . $label,
            'body'    => 'Click Join to enter the ' . $label . '.',
            'data'    => [
                'call_type'       => $request->type,
                'room'            => $roomName,
                'conversation_id' => $conversation->id,
                'caller_id'       => $caller->id,
                'caller_name'     => $caller->full_name,
                'session_id'      => $session->id,
            ],
        ]);

        return response()->json(['room' => $roomName, 'session_id' => $session->id]);
    }

    public function typing(Conversation $conversation)
    {
        $this->authorizeConversation($conversation);
        Cache::put('typing.' . $conversation->id . '.' . Auth::id(), true, now()->addSeconds(4));
        return response()->json(['ok' => true]);
    }

    public function isTyping(Conversation $conversation)
    {
        $this->authorizeConversation($conversation);
        $ids      = $conversation->participantIds();
        $otherId  = collect($ids)->first(fn($id) => $id !== Auth::id());
        $typing   = $otherId && Cache::get('typing.' . $conversation->id . '.' . $otherId);
        $name     = $typing ? \App\Models\User::find($otherId)?->first_name : null;
        return response()->json(['typing' => (bool) $typing, 'name' => $name]);
    }

    public function readStatus(Conversation $conversation)
    {
        $this->authorizeConversation($conversation);
        $ids = Message::where('conversation_id', $conversation->id)
            ->where('sender_id', Auth::id())
            ->whereNotNull('read_at')
            ->pluck('id');
        return response()->json(['read_ids' => $ids]);
    }

    public function sendMessage(Request $request, Conversation $conversation)
    {
        $this->authorizeConversation($conversation);

        $request->validate([
            'body' => 'required_without:file|nullable|string|max:5000',
            'file' => 'nullable|file|max:20480',
        ]);

        $filePath = $fileName = null;
        $type = 'text';

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('chat-files', 'public');
            $fileName = $request->file('file')->getClientOriginalName();
            $type = 'file';
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => Auth::id(),
            'body'            => $request->body,
            'file_path'       => $filePath,
            'file_name'       => $fileName,
            'type'            => $type,
        ]);

        $conversation->update(['last_message_at' => now()]);

        if ($request->expectsJson()) {
            return response()->json(['message' => $message->load('sender')]);
        }

        return back();
    }
}
