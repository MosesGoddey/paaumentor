@extends('layouts.sidebar')
@section('title', 'Skill Exchange')

@section('page-content')
<div style="max-width:900px;margin:0 auto">

  {{-- Header --}}
  <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px">
    <div>
      <h1 style="font-family:'Sora',sans-serif;font-size:1.4rem;font-weight:800;margin:0">Skill Exchange</h1>
      <p style="color:var(--text-3);font-size:0.85rem;margin:4px 0 0">Teach what you know. Learn what you don't.</p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
      <a href="{{ route('skill-exchange.my') }}" class="btn btn-sm" style="background:var(--surface-2);color:var(--text);border:1px solid var(--border)">My Listings</a>
      <a href="{{ route('skill-exchange.create') }}" class="btn btn-primary btn-sm">+ Post a Listing</a>
    </div>
  </div>

  {{-- Flash messages --}}
  @if(session('success'))
  <div style="background:#d1fae5;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:0.88rem;color:#065f46">{{ session('success') }}</div>
  @endif
  @if(session('error'))
  <div style="background:#fee2e2;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:0.88rem;color:#991b1b">{{ session('error') }}</div>
  @endif

  {{-- Search --}}
  <form method="GET" style="margin-bottom:24px">
    <div style="display:flex;gap:8px">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by skill (e.g. UI/UX, Python, Graphics…)"
             class="form-input" style="flex:1;font-size:0.88rem">
      <button type="submit" class="btn btn-primary btn-sm">Search</button>
      @if(request('search'))
      <a href="{{ route('skill-exchange.index') }}" class="btn btn-sm" style="background:var(--surface-2);color:var(--text);border:1px solid var(--border)">Clear</a>
      @endif
    </div>
  </form>

  {{-- Mutual Matches --}}
  @if($mutualMatches->isNotEmpty())
  <div style="margin-bottom:28px">
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
      <span style="font-size:1rem"></span>
      <h2 style="font-family:'Sora',sans-serif;font-size:1rem;font-weight:800;margin:0">Perfect Matches</h2>
      <span style="background:linear-gradient(135deg,#c9a227,#f5c842);color:#fff;border-radius:20px;padding:2px 10px;font-size:0.7rem;font-weight:700">{{ $mutualMatches->count() }}</span>
    </div>
    <p style="font-size:0.8rem;color:var(--text-3);margin-bottom:14px">They offer what you want to learn, and want to learn what you offer.</p>
    <div style="display:flex;flex-direction:column;gap:12px">
      @foreach($mutualMatches as $ex)
        @include('skill-exchange._card', ['ex' => $ex, 'highlight' => true])
      @endforeach
    </div>
  </div>
  @endif

  {{-- All Listings --}}
  <div>
    @if($mutualMatches->isNotEmpty())
    <h2 style="font-family:'Sora',sans-serif;font-size:1rem;font-weight:800;margin-bottom:12px">Other Listings</h2>
    @endif

    @if($otherListings->isNotEmpty())
    <div style="display:flex;flex-direction:column;gap:12px">
      @foreach($otherListings as $ex)
        @include('skill-exchange._card', ['ex' => $ex, 'highlight' => false])
      @endforeach
    </div>
    @elseif($mutualMatches->isEmpty())
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:20px;padding:56px;text-align:center;color:var(--text-3)">
      <div style="font-size:2rem;margin-bottom:12px"></div>
      <div style="font-weight:700;font-size:1rem;margin-bottom:8px;color:var(--text)">No listings yet</div>
      <p style="font-size:0.88rem;max-width:340px;margin:0 auto 20px">Be the first to post a skill exchange listing and connect with other learners.</p>
      <a href="{{ route('skill-exchange.create') }}" class="btn btn-primary">Post a Listing</a>
    </div>
    @endif
  </div>

</div>
@endsection
