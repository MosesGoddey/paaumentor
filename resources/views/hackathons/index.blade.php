@extends('layouts.sidebar')
@section('title', 'Hackathons')

@section('page-content')
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px">
  <div>
    <h1 class="section-title">Hackathons</h1>
    <p class="section-sub">Join a challenge, form a team, and build something amazing.</p>
  </div>
  @if($user->isAdmin() || $user->isVerifier())
  <a href="{{ route('hackathons.create') }}" class="btn btn-primary">+ New Hackathon</a>
  @endif
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif

@forelse($hackathons as $h)
@php $hackathon = $h['hackathon']; $myTeam = $h['myTeam']; @endphp
<div class="card" style="margin-bottom:16px;padding:0;overflow:hidden">
  <div style="height:6px;background:{{ $hackathon->status_color }}"></div>
  <div style="padding:20px 24px">
    <div style="display:flex;align-items:flex-start;gap:16px;flex-wrap:wrap">
      <div style="flex:1;min-width:0">
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:6px">
          <h2 style="font-size:1.1rem;font-weight:800;margin:0">{{ $hackathon->title }}</h2>
          <span style="background:{{ $hackathon->status_color }}22;color:{{ $hackathon->status_color }};border-radius:6px;padding:2px 10px;font-size:0.72rem;font-weight:700">
            {{ $hackathon->status_label }}
          </span>
          @if($myTeam)
          <span class="badge badge-green"> Registered</span>
          @endif
        </div>
        @if($hackathon->theme)
        <div style="font-size:0.8rem;color:var(--text-3);margin-bottom:6px">Theme: <strong>{{ $hackathon->theme }}</strong></div>
        @endif
        @if($hackathon->description)
        <p style="font-size:0.88rem;color:var(--text-2);line-height:1.6;margin-bottom:10px">{{ Str::limit($hackathon->description, 160) }}</p>
        @endif
        <div style="display:flex;gap:16px;flex-wrap:wrap;font-size:0.78rem;color:var(--text-3)">
          @if($hackathon->registration_deadline)
          <span> Reg. deadline: <strong>{{ $hackathon->registration_deadline->format('M j, Y') }}</strong></span>
          @endif
          @if($hackathon->start_date)
          <span> Starts: <strong>{{ $hackathon->start_date->format('M j, Y') }}</strong></span>
          @endif
          @if($hackathon->end_date)
          <span> Ends: <strong>{{ $hackathon->end_date->format('M j, Y') }}</strong></span>
          @endif
          <span> <strong>{{ $hackathon->teams_count }}</strong> teams</span>
        </div>
      </div>
      <div style="display:flex;flex-direction:column;gap:8px;flex-shrink:0">
        @if($myTeam)
          <a href="{{ route('hackathons.team', $hackathon) }}" class="btn btn-primary btn-sm">My Team Workspace</a>
        @endif
        <a href="{{ route('hackathons.show', $hackathon) }}" class="btn btn-outline btn-sm">View Details</a>
        @if($hackathon->status === 'completed')
        <a href="{{ route('hackathons.leaderboard', $hackathon) }}" class="btn btn-outline btn-sm"> Results</a>
        @endif
        @if(($hackathon->isJudge($user) || $user->isAdmin() || $user->isVerifier()) && in_array($hackathon->status, ['judging', 'completed']))
        <a href="{{ route('hackathons.judge', $hackathon) }}" class="btn btn-outline btn-sm">Judge Panel</a>
        @endif
      </div>
    </div>
    @if($hackathon->prizes)
    <div style="margin-top:12px;padding:10px 14px;background:linear-gradient(135deg,#fef3c7,#fde68a);border-radius:10px;font-size:0.82rem;color:#92400e">
       <strong>Prizes:</strong> {{ $hackathon->prizes }}
    </div>
    @endif
  </div>
</div>
@empty
<div class="card" style="text-align:center;padding:60px;color:var(--text-3)">
  <div style="font-size:2.5rem;margin-bottom:12px"></div>
  <p>No hackathons available right now. Check back soon!</p>
</div>
@endforelse
@endsection
