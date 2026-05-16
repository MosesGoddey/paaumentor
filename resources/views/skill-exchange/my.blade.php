@extends('layouts.sidebar')
@section('title', 'My Skill Exchanges')

@section('page-content')
<div style="max-width:900px;margin:0 auto">

  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px">
    <div>
      <h1 style="font-family:'Sora',sans-serif;font-size:1.4rem;font-weight:800;margin:0">My Skill Exchanges</h1>
      <p style="color:var(--text-3);font-size:0.85rem;margin:4px 0 0">Manage your listings and track your requests.</p>
    </div>
    <a href="{{ route('skill-exchange.create') }}" class="btn btn-primary btn-sm">+ New Listing</a>
  </div>

  @if(session('success'))
  <div style="background:#d1fae5;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:0.88rem;color:#065f46">{{ session('success') }}</div>
  @endif

  {{-- My Listings --}}
  <h2 style="font-family:'Sora',sans-serif;font-size:1rem;font-weight:800;margin-bottom:12px">My Listings</h2>

  @forelse($myListings as $listing)
  <div style="background:var(--surface);border:1px solid var(--border);border-radius:16px;margin-bottom:16px;overflow:hidden">

    {{-- Listing header --}}
    <div style="padding:16px 20px;display:flex;align-items:center;gap:12px;flex-wrap:wrap;border-bottom:1px solid var(--border)">
      <div style="flex:1;min-width:0">
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:4px">
          <span style="background:#d1fae5;color:#065f46;border-radius:6px;padding:3px 10px;font-size:0.75rem;font-weight:700">Offering: {{ $listing->offering }}</span>
          <span style="color:var(--text-3);font-size:0.8rem"></span>
          <span style="background:#ede9fe;color:#5b21b6;border-radius:6px;padding:3px 10px;font-size:0.75rem;font-weight:700">Seeking: {{ $listing->seeking }}</span>
          @if(!$listing->is_active)
          <span style="background:var(--surface-2);color:var(--text-3);border-radius:6px;padding:3px 10px;font-size:0.72rem;font-weight:700">Hidden</span>
          @endif
        </div>
        @if($listing->description)
        <div style="font-size:0.8rem;color:var(--text-3)">{{ $listing->description }}</div>
        @endif
      </div>
      <div style="display:flex;gap:8px;flex-shrink:0">
        <form method="POST" action="{{ route('skill-exchange.toggle', $listing) }}">
          @csrf
          <button type="submit" class="btn btn-sm" style="background:var(--surface-2);color:var(--text);border:1px solid var(--border);font-size:0.75rem">
            {{ $listing->is_active ? 'Hide' : 'Activate' }}
          </button>
        </form>
        <form method="POST" action="{{ route('skill-exchange.destroy', $listing) }}"
              onsubmit="return confirm('Delete this listing?')">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-sm" style="background:#fee2e2;color:#991b1b;border:1px solid #fecaca;font-size:0.75rem">Delete</button>
        </form>
      </div>
    </div>

    {{-- Requests on this listing --}}
    @if($listing->requests->isNotEmpty())
    <div style="padding:12px 20px">
      <div style="font-size:0.78rem;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:10px">
        Requests ({{ $listing->requests->count() }})
      </div>
      @foreach($listing->requests as $req)
      <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border);flex-wrap:wrap">

        @if($req->requester->avatar_url)
        <img src="{{ $req->requester->avatar_url }}" style="width:36px;height:36px;border-radius:50%;object-fit:cover;flex-shrink:0">
        @else
        <div class="avatar avatar-sm" style="flex-shrink:0">{{ $req->requester->initials }}</div>
        @endif

        <div style="flex:1;min-width:0">
          <div style="font-weight:700;font-size:0.88rem">{{ $req->requester->full_name }}</div>
          @if($req->message)
          <div style="font-size:0.8rem;color:var(--text-3);margin-top:2px">"{{ $req->message }}"</div>
          @endif
          <div style="font-size:0.75rem;color:var(--text-3);margin-top:2px">{{ $req->created_at->diffForHumans() }}</div>
        </div>

        <div style="flex-shrink:0">
          @if($req->status === 'pending')
          <form method="POST" action="{{ route('skill-exchange.respond', $req) }}" style="display:flex;gap:6px">
            @csrf
            <button name="action" value="accept" type="submit"
                    class="btn btn-primary btn-sm" style="font-size:0.75rem">Accept</button>
            <button name="action" value="reject" type="submit"
                    class="btn btn-sm" style="background:#fee2e2;color:#991b1b;border:1px solid #fecaca;font-size:0.75rem">Decline</button>
          </form>
          @elseif($req->status === 'accepted')
          <div style="display:flex;gap:6px;flex-wrap:wrap;justify-content:flex-end">
            <a href="{{ route('profile.show', $req->requester) }}"
               class="btn btn-sm" style="background:var(--surface-2);color:var(--text);border:1px solid var(--border);font-size:0.75rem">View Profile</a>
            <a href="{{ route('skill-exchange.chat', $req) }}"
               class="btn btn-primary btn-sm" style="font-size:0.75rem">Open Chat</a>
          </div>
          @else
          <span style="background:#fee2e2;color:#991b1b;border-radius:6px;padding:3px 10px;font-size:0.75rem;font-weight:700">Declined</span>
          @endif
        </div>

      </div>
      @endforeach
    </div>
    @else
    <div style="padding:14px 20px;font-size:0.82rem;color:var(--text-3)">No requests yet.</div>
    @endif

  </div>
  @empty
  <div style="background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:36px;text-align:center;color:var(--text-3);margin-bottom:28px">
    <div style="font-weight:700;margin-bottom:6px;color:var(--text)">No listings yet</div>
    <p style="font-size:0.85rem;margin:0 0 16px">Post a listing so others can find and connect with you.</p>
    <a href="{{ route('skill-exchange.create') }}" class="btn btn-primary btn-sm">Post a Listing</a>
  </div>
  @endforelse

  {{-- Requests I Sent --}}
  <h2 style="font-family:'Sora',sans-serif;font-size:1rem;font-weight:800;margin:28px 0 12px">Requests I Sent</h2>

  @forelse($myRequests as $req)
  <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:16px 20px;margin-bottom:12px;display:flex;align-items:center;gap:14px;flex-wrap:wrap">

    @if($req->exchange->user->avatar_url)
    <img src="{{ $req->exchange->user->avatar_url }}" style="width:40px;height:40px;border-radius:50%;object-fit:cover;flex-shrink:0">
    @else
    <div class="avatar avatar-sm" style="flex-shrink:0">{{ $req->exchange->user->initials }}</div>
    @endif

    <div style="flex:1;min-width:0">
      <div style="font-weight:700;font-size:0.88rem;margin-bottom:4px">{{ $req->exchange->user->full_name }}</div>
      <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap">
        <span style="background:#d1fae5;color:#065f46;border-radius:6px;padding:2px 8px;font-size:0.72rem;font-weight:700">{{ $req->exchange->offering }}</span>
        <span style="color:var(--text-3);font-size:0.75rem"></span>
        <span style="background:#ede9fe;color:#5b21b6;border-radius:6px;padding:2px 8px;font-size:0.72rem;font-weight:700">{{ $req->exchange->seeking }}</span>
      </div>
      @if($req->message)
      <div style="font-size:0.78rem;color:var(--text-3);margin-top:4px">"{{ $req->message }}"</div>
      @endif
      <div style="font-size:0.72rem;color:var(--text-3);margin-top:4px">{{ $req->created_at->diffForHumans() }}</div>
    </div>

    <div style="flex-shrink:0">
      @if($req->status === 'pending')
      <span style="background:#fef3c7;color:#92400e;border-radius:6px;padding:4px 12px;font-size:0.75rem;font-weight:700">Pending</span>
      @elseif($req->status === 'accepted')
      <div style="display:flex;flex-direction:column;gap:6px;align-items:flex-end">
        <span style="background:#d1fae5;color:#065f46;border-radius:6px;padding:4px 12px;font-size:0.75rem;font-weight:700">Accepted </span>
        <div style="display:flex;gap:6px">
          <a href="{{ route('profile.show', $req->exchange->user) }}"
             class="btn btn-sm" style="background:var(--surface-2);color:var(--text);border:1px solid var(--border);font-size:0.72rem">View Profile</a>
          <a href="{{ route('skill-exchange.chat', $req) }}"
             class="btn btn-primary btn-sm" style="font-size:0.72rem">Open Chat</a>
        </div>
      </div>
      @else
      <span style="background:#fee2e2;color:#991b1b;border-radius:6px;padding:4px 12px;font-size:0.75rem;font-weight:700">Declined</span>
      @endif
    </div>

  </div>
  @empty
  <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:28px;text-align:center;color:var(--text-3)">
    <div style="font-size:0.85rem">You haven't sent any exchange requests yet.</div>
    <a href="{{ route('skill-exchange.index') }}" style="font-size:0.85rem;color:var(--blue-500);display:inline-block;margin-top:8px">Browse listings </a>
  </div>
  @endforelse

</div>
@endsection
