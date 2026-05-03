@extends('layouts.sidebar')
@section('title', 'My Mentees')

@section('page-content')
<h1 style="font-size:1.4rem;font-weight:800;margin-bottom:20px">My Mentees</h1>

@if(session('success'))
<div style="background:#d1fae5;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:0.88rem;color:#065f46">{{ session('success') }}</div>
@endif

@forelse($mentorships as $m)
@php $mentee = $m->mentee; @endphp
<div class="card" style="margin-bottom:16px">
  <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap">
    @if($mentee->avatar_url)
    <img src="{{ $mentee->avatar_url }}" alt="" style="width:56px;height:56px;border-radius:50%;object-fit:cover;flex-shrink:0">
    @else
    <div class="avatar" style="width:56px;height:56px;font-size:1.1rem;flex-shrink:0">{{ $mentee->initials }}</div>
    @endif

    <div style="flex:1;min-width:0">
      <div style="font-weight:800;font-size:1rem">{{ $mentee->full_name }}</div>
      <div style="font-size:0.8rem;color:var(--text-3);margin-top:2px">{{ ucfirst($mentee->role) }} · {{ $mentee->level }} · {{ $mentee->department }}</div>
      <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:8px">
        @foreach($mentee->hasSkills->take(4) as $s)
        <span class="badge badge-blue">{{ $s->name }}</span>
        @endforeach
      </div>
    </div>

    <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;flex-shrink:0">
      @if($m->status === 'active')
        <span style="background:#d1fae5;color:#065f46;border-radius:6px;padding:3px 10px;font-size:0.75rem;font-weight:700">Active</span>
        @if($m->conversation)
        <a href="{{ route('chat.show', $m->conversation) }}" class="btn btn-primary btn-sm">Message</a>
        @endif
      @elseif($m->status === 'pending')
        <span style="background:#fef3c7;color:#92400e;border-radius:6px;padding:3px 10px;font-size:0.75rem;font-weight:700">Pending</span>
        <div style="display:flex;gap:6px">
          <form method="POST" action="{{ route('mentors.respond', $m) }}">
            @csrf @method('PATCH')
            <input type="hidden" name="action" value="accept">
            <button type="submit" class="btn btn-primary btn-sm">✓ Accept</button>
          </form>
          <form method="POST" action="{{ route('mentors.respond', $m) }}">
            @csrf @method('PATCH')
            <input type="hidden" name="action" value="reject">
            <button type="submit" class="btn btn-outline btn-sm" style="color:#dc2626;border-color:#dc2626">✗ Decline</button>
          </form>
        </div>
      @elseif($m->status === 'rejected')
        <span style="background:#fee2e2;color:#991b1b;border-radius:6px;padding:3px 10px;font-size:0.75rem;font-weight:700">Declined</span>
      @else
        <span style="background:var(--surface-2);color:var(--text-3);border-radius:6px;padding:3px 10px;font-size:0.75rem;font-weight:700">{{ ucfirst($m->status) }}</span>
      @endif
    </div>
  </div>

  <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--border);display:flex;gap:24px;font-size:0.8rem;color:var(--text-3)">
    <span>Topic: <strong style="color:var(--text-1)">{{ $m->topic }}</strong></span>
    @if($m->started_at)<span>Started: <strong style="color:var(--text-1)">{{ $m->started_at->format('M d, Y') }}</strong></span>@endif
  </div>
</div>
@empty
<div class="card" style="text-align:center;padding:40px 20px;color:var(--text-3)">
  <div style="font-weight:700;margin-bottom:6px">No mentees yet</div>
  <p style="font-size:0.88rem">When mentees send you requests, they will appear here.</p>
</div>
@endforelse
@endsection
