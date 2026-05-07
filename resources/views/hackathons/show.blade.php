@extends('layouts.sidebar')
@section('title', $hackathon->title)

@section('breadcrumbs')
<span style="color:var(--text-3)">/</span>
<a href="{{ route('hackathons.index') }}" style="color:var(--text-3);text-decoration:none">Hackathons</a>
<span style="color:var(--text-3)">/</span>
<span>{{ $hackathon->title }}</span>
@endsection

@section('page-content')
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif

{{-- Hero --}}
<div class="card" style="padding:0;overflow:hidden;margin-bottom:20px">
  <div style="height:90px;background:linear-gradient(135deg,#1e3a8a,#2563eb);position:relative;display:flex;align-items:flex-end;padding:16px 24px">
    <span style="background:{{ $hackathon->status_color }}22;color:#fff;border:1px solid rgba(255,255,255,0.4);border-radius:6px;padding:3px 12px;font-size:0.75rem;font-weight:700">
      {{ $hackathon->status_label }}
    </span>
  </div>
  <div style="padding:20px 24px">
    <h1 style="font-size:1.5rem;font-weight:800;margin-bottom:6px">{{ $hackathon->title }}</h1>
    @if($hackathon->theme)<p style="color:var(--text-3);font-size:0.85rem;margin-bottom:12px">Theme: <strong>{{ $hackathon->theme }}</strong></p>@endif
    @if($hackathon->description)<p style="font-size:0.9rem;color:var(--text-2);line-height:1.7;margin-bottom:16px">{{ $hackathon->description }}</p>@endif

    {{-- Dates + meta --}}
    <div style="display:flex;gap:20px;flex-wrap:wrap;margin-bottom:16px">
      @if($hackathon->registration_deadline)
      <div style="text-align:center"><div style="font-weight:800;font-size:0.9rem">{{ $hackathon->registration_deadline->format('M j') }}</div><div style="font-size:0.72rem;color:var(--text-3)">Reg. Deadline</div></div>
      @endif
      @if($hackathon->start_date)
      <div style="text-align:center"><div style="font-weight:800;font-size:0.9rem">{{ $hackathon->start_date->format('M j') }}</div><div style="font-size:0.72rem;color:var(--text-3)">Start Date</div></div>
      @endif
      @if($hackathon->end_date)
      <div style="text-align:center"><div style="font-weight:800;font-size:0.9rem">{{ $hackathon->end_date->format('M j') }}</div><div style="font-size:0.72rem;color:var(--text-3)">End Date</div></div>
      @endif
      <div style="text-align:center"><div style="font-weight:800;font-size:0.9rem">{{ $hackathon->teams->count() }}</div><div style="font-size:0.72rem;color:var(--text-3)">Teams</div></div>
      <div style="text-align:center"><div style="font-weight:800;font-size:0.9rem">{{ $hackathon->max_team_size }}</div><div style="font-size:0.72rem;color:var(--text-3)">Max/Team</div></div>
    </div>

    @if($hackathon->prizes)
    <div style="padding:12px 16px;background:linear-gradient(135deg,#fef3c7,#fde68a);border-radius:10px;font-size:0.85rem;color:#92400e;margin-bottom:16px">
      🏅 <strong>Prizes:</strong> {{ $hackathon->prizes }}
    </div>
    @endif

    @if($hackathon->tracks)
    <div style="margin-bottom:12px">
      <div style="font-size:0.78rem;font-weight:700;color:var(--text-3);margin-bottom:6px">TRACKS</div>
      <div style="display:flex;flex-wrap:wrap;gap:6px">
        @foreach($hackathon->tracks as $track)
        <span class="badge badge-blue">{{ $track }}</span>
        @endforeach
      </div>
    </div>
    @endif

    {{-- Admin controls --}}
    @if($isAdmin)
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:16px;padding-top:16px;border-top:1px solid var(--border)">
      @if($hackathon->status === 'draft')
      <form method="POST" action="{{ route('hackathons.status', $hackathon) }}">@csrf<input type="hidden" name="status" value="open"><button class="btn btn-primary btn-sm">Publish (Open Registration)</button></form>
      @elseif($hackathon->status === 'open')
      <form method="POST" action="{{ route('hackathons.status', $hackathon) }}">@csrf<input type="hidden" name="status" value="ongoing"><button class="btn btn-primary btn-sm">Start Hackathon</button></form>
      @elseif($hackathon->status === 'ongoing')
      <form method="POST" action="{{ route('hackathons.status', $hackathon) }}">@csrf<input type="hidden" name="status" value="judging"><button class="btn btn-primary btn-sm">Close Submissions → Judging</button></form>
      @elseif($hackathon->status === 'judging')
      <form method="POST" action="{{ route('hackathons.publishResults', $hackathon) }}">@csrf<button class="btn btn-primary btn-sm" onclick="return confirm('Publish results and issue all certificates?')">🏆 Publish Results + Issue Certs</button></form>
      @endif
      <a href="{{ route('hackathons.judge', $hackathon) }}" class="btn btn-outline btn-sm">Judge Panel</a>
      <a href="{{ route('hackathons.leaderboard', $hackathon) }}" class="btn btn-outline btn-sm">Leaderboard</a>
    </div>
    @endif

    {{-- Judge actions --}}
    @if($isJudge && in_array($hackathon->status, ['judging', 'completed']))
    <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border)">
      <a href="{{ route('hackathons.judge', $hackathon) }}" class="btn btn-primary btn-sm">Open Judge Panel</a>
    </div>
    @endif
  </div>
</div>

{{-- Rules --}}
@if($hackathon->rules)
<div class="card" style="margin-bottom:20px">
  <div style="font-family:'Sora',sans-serif;font-weight:700;margin-bottom:12px">📋 Rules & Guidelines</div>
  <div style="font-size:0.88rem;color:var(--text-2);line-height:1.8;white-space:pre-line">{{ $hackathon->rules }}</div>
</div>
@endif

{{-- Judge Assignment (admin only) --}}
@if($isAdmin)
<div class="card" style="margin-bottom:20px">
  <div style="font-family:'Sora',sans-serif;font-weight:700;margin-bottom:12px">Assign Judges</div>
  @php $currentJudgeIds = $hackathon->judge_ids ?? []; @endphp
  @if($currentJudgeIds)
  <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:12px">
    @foreach($potentialJudges->whereIn('id', $currentJudgeIds) as $j)
    <span class="badge badge-blue">{{ $j->full_name }}</span>
    @endforeach
  </div>
  @endif
  <form method="POST" action="{{ route('hackathons.assignJudge', $hackathon) }}" style="display:flex;gap:8px">
    @csrf
    <select name="user_id" class="form-select" style="flex:1">
      <option value="">Select mentor / alumni to assign as judge…</option>
      @foreach($potentialJudges->whereNotIn('id', $currentJudgeIds) as $j)
      <option value="{{ $j->id }}">{{ $j->full_name }} ({{ ucfirst($j->role) }})</option>
      @endforeach
    </select>
    <button class="btn btn-outline btn-sm">Assign</button>
  </form>
</div>
@endif

{{-- Team Section --}}
@if(in_array($hackathon->status, ['open','ongoing','judging','completed']))
<div class="card" style="margin-bottom:20px">
  <div style="font-family:'Sora',sans-serif;font-weight:700;margin-bottom:16px">🧑‍🤝‍🧑 Your Team</div>

  @if($myTeam)
    {{-- Already in a team --}}
    <div style="display:flex;align-items:center;gap:12px;padding:14px;background:var(--surface-2);border-radius:12px;margin-bottom:14px">
      <div style="font-size:1.8rem">🏷️</div>
      <div>
        <div style="font-weight:800;font-size:1rem">{{ $myTeam->name }}</div>
        @if($myTeam->track)<div style="font-size:0.8rem;color:var(--text-3)">Track: {{ $myTeam->track }}</div>@endif
      </div>
    </div>
    <a href="{{ route('hackathons.team', $hackathon) }}" class="btn btn-primary" style="display:inline-block">Open Team Workspace →</a>

  @elseif(in_array($hackathon->status, ['open', 'ongoing']))
    <p style="font-size:0.88rem;color:var(--text-3);margin-bottom:16px">You're not in a team yet. Create a new team or join one with a code.</p>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
      {{-- Create team --}}
      <div style="background:var(--surface-2);border-radius:14px;padding:18px">
        <div style="font-weight:700;margin-bottom:12px">Create a Team</div>
        <form method="POST" action="{{ route('hackathons.team.create', $hackathon) }}">
          @csrf
          <div class="form-group">
            <label class="form-label">Team Name</label>
            <input type="text" name="name" class="form-input" placeholder="e.g. Team Hydra" required>
          </div>
          @if($hackathon->tracks)
          <div class="form-group">
            <label class="form-label">Track</label>
            <select name="track" class="form-select">
              <option value="">No specific track</option>
              @foreach($hackathon->tracks as $t)
              <option>{{ $t }}</option>
              @endforeach
            </select>
          </div>
          @else
          <div class="form-group">
            <label class="form-label">Track (optional)</label>
            <input type="text" name="track" class="form-input" placeholder="e.g. AI & ML">
          </div>
          @endif
          <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">Create Team</button>
        </form>
      </div>

      {{-- Join team --}}
      <div style="background:var(--surface-2);border-radius:14px;padding:18px">
        <div style="font-weight:700;margin-bottom:12px">Join a Team</div>
        <p style="font-size:0.82rem;color:var(--text-3);margin-bottom:12px">Ask your teammate for their 6-character join code.</p>
        <form method="POST" action="{{ route('hackathons.team.join', $hackathon) }}">
          @csrf
          <div class="form-group">
            <label class="form-label">Join Code</label>
            <input type="text" name="join_code" class="form-input" placeholder="e.g. XK9FT2" maxlength="8" style="letter-spacing:3px;text-transform:uppercase;font-weight:700;font-size:1.1rem" required>
          </div>
          <button type="submit" class="btn btn-outline" style="width:100%;justify-content:center">Join Team</button>
        </form>
      </div>
    </div>

  @else
    <p style="font-size:0.88rem;color:var(--text-3)">Team registration is now closed for this hackathon.</p>
  @endif
</div>
@endif

{{-- Mentor: Teams needing a coach --}}
@if(count($teamsNeedingCoach) > 0)
<div class="card" style="margin-bottom:20px">
  <div style="font-family:'Sora',sans-serif;font-weight:700;margin-bottom:12px">Teams Looking for a Coach</div>
  <p style="font-size:0.82rem;color:var(--text-3);margin-bottom:14px">As a mentor, you can volunteer to coach a team during this hackathon.</p>
  <div style="display:flex;flex-direction:column;gap:10px">
    @foreach($teamsNeedingCoach as $team)
    <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:var(--surface-2);border-radius:10px">
      <div>
        <div style="font-weight:700;font-size:0.9rem">{{ $team->name }}</div>
        @if($team->track)<div style="font-size:0.78rem;color:var(--text-3)">{{ $team->track }}</div>@endif
        <div style="font-size:0.78rem;color:var(--text-3)">{{ $team->users->count() }} member(s)</div>
      </div>
      <form method="POST" action="{{ route('hackathons.volunteerCoach', $team) }}">@csrf
        <button class="btn btn-outline btn-sm">Volunteer as Coach</button>
      </form>
    </div>
    @endforeach
  </div>
</div>
@endif

{{-- All Teams (public list) --}}
<div class="card">
  <div style="font-family:'Sora',sans-serif;font-weight:700;margin-bottom:14px">All Teams ({{ $hackathon->teams->count() }})</div>
  @forelse($hackathon->teams as $team)
  <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border)">
    <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--blue-700),var(--blue-500));display:flex;align-items:center;justify-content:center;color:#fff;font-size:0.75rem;font-weight:700;flex-shrink:0">
      {{ strtoupper(substr($team->name,0,2)) }}
    </div>
    <div style="flex:1;min-width:0">
      <div style="font-weight:700;font-size:0.9rem">{{ $team->name }}</div>
      <div style="font-size:0.75rem;color:var(--text-3)">
        {{ $team->users->count() }} member(s)
        @if($team->track) · {{ $team->track }} @endif
        @if($team->coach) · 🎓 Coach: {{ $team->coach->full_name }} @endif
      </div>
    </div>
    @if($team->submission && $team->submission->status === 'submitted')
    <span class="badge badge-green" style="flex-shrink:0">Submitted</span>
    @endif
  </div>
  @empty
  <p style="font-size:0.85rem;color:var(--text-3)">No teams yet. Be the first to register!</p>
  @endforelse
</div>
@endsection
