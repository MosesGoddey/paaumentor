<?php

namespace App\Http\Controllers;

use App\Services\AiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiAssistantController extends Controller
{
    public function index()
    {
        $history = session('study_buddy_history', []);
        return view('ai.assistant', ['history' => $history, 'user' => Auth::user()]);
    }

    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string|max:2000']);

        $history = session('study_buddy_history', []);

        try {
            $reply = app(AiService::class)->studyBuddy($request->message, $history);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'AI is temporarily unavailable. Please try again later.'], 503);
        }

        $history[] = ['role' => 'user',      'content' => $request->message];
        $history[] = ['role' => 'assistant', 'content' => $reply];

        if (count($history) > 20) {
            $history = array_slice($history, -20);
        }

        session(['study_buddy_history' => $history]);

        return response()->json(['reply' => $reply]);
    }

    public function clear()
    {
        session()->forget('study_buddy_history');
        return response()->json(['ok' => true]);
    }

    public function widgetChat(Request $request)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $history = session('mentor_ai_widget_history', []);

        try {
            $result = app(AiService::class)->mentorAiWidget($request->message, $history);
        } catch (\Throwable) {
            return response()->json(['error' => 'Mentor AI is temporarily unavailable. Please try again shortly.'], 503);
        }

        $history[] = ['role' => 'user',      'content' => $request->message];
        $history[] = ['role' => 'assistant', 'content' => $result['reply']];

        if (count($history) > 20) {
            $history = array_slice($history, -20);
        }

        session(['mentor_ai_widget_history' => $history]);

        return response()->json([
            'reply'       => $result['reply'],
            'suggestions' => $result['suggestions'],
        ]);
    }
}
