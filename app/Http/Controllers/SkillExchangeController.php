<?php

namespace App\Http\Controllers;

use App\Models\{SkillExchange, SkillExchangeRequest, Notification};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SkillExchangeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user()->load('hasSkills', 'wantedSkills');

        $query = SkillExchange::where('user_id', '!=', $user->id)
            ->where('is_active', true)
            ->with('user')
            ->withCount(['requests as pending_count' => fn($q) => $q->where('status', 'pending')])
            ->withCount(['requests as my_request' => fn($q) => $q->where('requester_id', $user->id)->whereIn('status', ['pending', 'accepted'])]);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('offering', 'like', "%$s%")->orWhere('seeking', 'like', "%$s%"));
        }

        $exchanges = $query->latest()->get();

        // Tag mutual matches: they offer what I want AND seek what I offer
        $mySkills  = $user->hasSkills->pluck('name')->map(fn($n) => strtolower(trim($n)));
        $myWanted  = $user->wantedSkills->pluck('name')->map(fn($n) => strtolower(trim($n)));

        $exchanges = $exchanges->map(function ($ex) use ($mySkills, $myWanted) {
            $theyOffer = strtolower(trim($ex->offering));
            $theySeek  = strtolower(trim($ex->seeking));
            $ex->is_mutual = $myWanted->contains(fn($w) => str_contains($theyOffer, $w) || str_contains($w, $theyOffer))
                          && $mySkills->contains(fn($s) => str_contains($theySeek, $s) || str_contains($s, $theySeek));
            return $ex;
        });

        $mutualMatches = $exchanges->where('is_mutual', true);
        $otherListings = $exchanges->where('is_mutual', false);

        return view('skill-exchange.index', compact('mutualMatches', 'otherListings', 'user'));
    }

    public function create()
    {
        return view('skill-exchange.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'offering'    => 'required|string|max:100',
            'seeking'     => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        SkillExchange::create([
            'user_id'     => Auth::id(),
            'offering'    => $request->offering,
            'seeking'     => $request->seeking,
            'description' => $request->description,
        ]);

        return redirect()->route('skill-exchange.index')
            ->with('success', 'Your listing has been posted!');
    }

    public function my()
    {
        $user = Auth::user();

        $myListings = SkillExchange::where('user_id', $user->id)
            ->with(['requests' => fn($q) => $q->with(['requester', 'conversation'])->orderByRaw("FIELD(status,'pending','accepted','rejected')")])
            ->latest()
            ->get();

        $myRequests = SkillExchangeRequest::where('requester_id', $user->id)
            ->with(['exchange.user', 'conversation'])
            ->latest()
            ->get();

        return view('skill-exchange.my', compact('myListings', 'myRequests'));
    }

    public function sendRequest(Request $request, SkillExchange $exchange)
    {
        abort_if($exchange->user_id === Auth::id(), 403);
        abort_unless($exchange->is_active, 403);

        $request->validate(['message' => 'nullable|string|max:300']);

        $exists = SkillExchangeRequest::where('exchange_id', $exchange->id)
            ->where('requester_id', Auth::id())
            ->whereIn('status', ['pending', 'accepted'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'You already have an active request for this listing.');
        }

        $req = SkillExchangeRequest::create([
            'exchange_id'  => $exchange->id,
            'requester_id' => Auth::id(),
            'message'      => $request->message,
        ]);

        Notification::create([
            'user_id' => $exchange->user_id,
            'type'    => 'skill_exchange_request',
            'title'   => 'New skill exchange request',
            'body'    => Auth::user()->full_name . ' wants to exchange: ' . $exchange->offering . ' ↔ ' . $exchange->seeking,
            'data'    => ['exchange_request_id' => $req->id],
        ]);

        return back()->with('success', 'Exchange request sent!');
    }

    public function respond(Request $request, SkillExchangeRequest $exchangeRequest)
    {
        abort_unless($exchangeRequest->exchange->user_id === Auth::id(), 403);
        $request->validate(['action' => Rule::in(['accept', 'reject'])]);

        $status = $request->action === 'accept' ? 'accepted' : 'rejected';
        $exchangeRequest->update(['status' => $status]);

        if ($request->action === 'accept') {
            \App\Models\Conversation::firstOrCreate(
                ['skill_exchange_request_id' => $exchangeRequest->id],
                ['last_message_at' => now()]
            );
        }

        Notification::create([
            'user_id' => $exchangeRequest->requester_id,
            'type'    => 'skill_exchange_response',
            'title'   => $request->action === 'accept' ? 'Skill exchange accepted!' : 'Skill exchange declined',
            'body'    => Auth::user()->full_name . ($request->action === 'accept'
                ? ' accepted your skill exchange request. Head to Messages to start chatting!'
                : ' declined your skill exchange request.'),
            'data'    => ['exchange_request_id' => $exchangeRequest->id],
        ]);

        return back()->with('success', 'Response recorded.');
    }

    public function openChat(SkillExchangeRequest $exchangeRequest)
    {
        abort_unless($exchangeRequest->status === 'accepted', 403);
        abort_unless(
            $exchangeRequest->requester_id === Auth::id() ||
            $exchangeRequest->exchange->user_id === Auth::id(),
            403
        );

        $conversation = \App\Models\Conversation::firstOrCreate(
            ['skill_exchange_request_id' => $exchangeRequest->id],
            ['last_message_at' => now()]
        );

        return redirect()->route('chat.show', $conversation);
    }

    public function toggleActive(SkillExchange $exchange)
    {
        abort_unless($exchange->user_id === Auth::id(), 403);
        $exchange->update(['is_active' => !$exchange->is_active]);
        return back()->with('success', $exchange->is_active ? 'Listing is now active.' : 'Listing is now hidden.');
    }

    public function destroy(SkillExchange $exchange)
    {
        abort_unless($exchange->user_id === Auth::id(), 403);
        $exchange->delete();
        return back()->with('success', 'Listing deleted.');
    }
}
