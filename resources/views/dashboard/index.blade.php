@extends('layouts.sidebar')
@section('title', 'Dashboard')

@section('page-content')
<div class="dash">

  {{-- ============ Page header ============ --}}
  <div class="dash-header">
    <div>
      <div class="dash-breadcrumb">Overview</div>
      <h1 class="dash-title">Good {{ now()->format('H') < 12 ? 'morning' : (now()->format('H') < 17 ? 'afternoon' : 'evening') }}, {{ $user->first_name }}</h1>
      <p class="dash-meta">
        <span class="dash-role-chip">{{ ucfirst($user->role) }}</span>
        <span>{{ $user->level }}</span>
        <span class="dash-meta-sep">·</span>
        <span>{{ $user->department }}</span>
        @if($user->student_id)
        <span class="dash-meta-sep">·</span>
        <span>{{ $user->student_id }}</span>
        @endif
      </p>
    </div>
    <div class="dash-header-actions">
      @if($user->isMentee())
      <a href="{{ route('mentors.index') }}" class="dash-btn dash-btn-secondary">Find Mentor</a>
      @endif
      <a href="{{ route('chat.index') }}" class="dash-btn dash-btn-primary">Messages</a>
    </div>
  </div>

  {{-- ============ KPI row ============ --}}
  <div class="dash-kpis">
    <div class="dash-kpi">
      <div class="dash-kpi-label">{{ $user->isMentor() ? 'Active Mentees' : 'Active Mentors' }}</div>
      <div class="dash-kpi-value">{{ $mentorships->count() }}</div>
      <div class="dash-kpi-trend {{ $kpiTrends['mentorships'] ? 'is-up' : '' }}">{{ $kpiTrends['mentorships'] ? '+' . $kpiTrends['mentorships'] . ' this month' : 'No change this month' }}</div>
    </div>
    <div class="dash-kpi">
      <div class="dash-kpi-label">Sessions Completed</div>
      <div class="dash-kpi-value">{{ $sessionCount }}</div>
      <div class="dash-kpi-trend {{ $kpiTrends['sessions'] ? 'is-up' : '' }}">{{ $kpiTrends['sessions'] ? '+' . $kpiTrends['sessions'] . ' this month' : 'None this month' }}</div>
    </div>
    <div class="dash-kpi">
      <div class="dash-kpi-label">{{ $user->isMentor() ? 'Paths Mentored' : 'Learning Paths' }}</div>
      <div class="dash-kpi-value">{{ count($learningPaths) }}</div>
      <div class="dash-kpi-trend {{ $kpiTrends['paths'] ? 'is-up' : '' }}">{{ $kpiTrends['paths'] ? '+' . $kpiTrends['paths'] . ' this month' : 'No new paths' }}</div>
    </div>
    <div class="dash-kpi">
      <div class="dash-kpi-label">Certificates</div>
      <div class="dash-kpi-value">{{ $certificates->count() }}</div>
      <div class="dash-kpi-trend {{ $kpiTrends['certificates'] ? 'is-up' : '' }}">{{ $kpiTrends['certificates'] ? '+' . $kpiTrends['certificates'] . ' this month' : 'None this month' }}</div>
    </div>
  </div>

  {{-- ============ Pending mentorship requests (mentor only) ============ --}}
  @if($user->isMentor() && $pendingRequests->isNotEmpty())
  <div class="dash-panel">
    <div class="dash-panel-head">
      <h2 class="dash-panel-title">Mentorship Requests</h2>
      <span class="dash-count-chip">{{ $pendingRequests->count() }} pending</span>
    </div>
    <div class="dash-panel-body">
      @foreach($pendingRequests as $req)
      <div class="dash-row">
        @if($req->mentee->avatar_url)
        <img src="{{ $req->mentee->avatar_url }}" alt="" class="dash-avatar-img">
        @else
        <div class="dash-avatar">{{ $req->mentee->initials }}</div>
        @endif
        <div class="dash-row-main">
          <div class="dash-row-title">{{ $req->mentee->full_name }}</div>
          <div class="dash-row-sub">{{ $req->mentee->level }} · {{ $req->mentee->department }}</div>
          <div class="dash-row-detail"><span>Topic:</span> {{ $req->topic }}</div>
          @if($req->goal)<div class="dash-row-sub dash-truncate">{{ $req->goal }}</div>@endif
        </div>
        <div class="dash-row-actions">
          <form method="POST" action="{{ route('mentors.respond', $req) }}">
            @csrf @method('PATCH')
            <input type="hidden" name="action" value="accept">
            <button type="submit" class="dash-btn dash-btn-primary dash-btn-sm">Accept</button>
          </form>
          <form method="POST" action="{{ route('mentors.respond', $req) }}">
            @csrf @method('PATCH')
            <input type="hidden" name="action" value="reject">
            <button type="submit" class="dash-btn dash-btn-ghost-danger dash-btn-sm">Decline</button>
          </form>
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endif

  {{-- ============ Two-column: people + sessions ============ --}}
  <div class="dash-grid-2-1">

    {{-- Left: mentees (mentor) or smart matches (mentee) --}}
    <div class="dash-panel">
      <div class="dash-panel-head">
        <h2 class="dash-panel-title">{{ $user->isMentor() ? 'Your Mentees' : 'Suggested Mentors' }}</h2>
        <a href="{{ $user->isMentor() ? route('mentors.mentees') : route('mentors.index') }}" class="dash-link">View all</a>
      </div>
      <div class="dash-panel-body">
        @if($user->isMentor())
          @forelse($mentorships as $ms)
          @php
            $lastSession  = $ms->sessions->first();
            $menteeProgress = $pathProgressByMentee[$ms->mentee_id] ?? null;
          @endphp
          <div class="dash-row">
            @if($ms->mentee->avatar_url)
            <img src="{{ $ms->mentee->avatar_url }}" alt="" class="dash-avatar-img">
            @else
            <div class="dash-avatar">{{ $ms->mentee->initials }}</div>
            @endif
            <div class="dash-row-main">
              <div class="dash-row-title">{{ $ms->mentee->full_name }}</div>
              <div class="dash-row-sub">{{ $ms->mentee->level }} · {{ $ms->topic ?: $ms->mentee->department }}</div>
              <div class="dash-row-detail">
                {{ $ms->sessions->count() }} {{ $ms->sessions->count() === 1 ? 'session' : 'sessions' }}
                <span>·</span>
                {{ $lastSession ? 'Last: ' . $lastSession->scheduled_at->format('M j') : 'No sessions yet' }}
              </div>
              @if($menteeProgress !== null)
              <div class="dash-mentee-progress">
                <div class="dash-progress"><div class="dash-progress-fill {{ $menteeProgress == 100 ? 'is-complete' : '' }}" style="width:{{ $menteeProgress }}%"></div></div>
                <span class="dash-mentee-progress-pct">{{ $menteeProgress }}%</span>
              </div>
              @endif
            </div>
            <a href="{{ route('profile.show', $ms->mentee) }}" class="dash-btn dash-btn-secondary dash-btn-sm">Profile</a>
          </div>
          @empty
          <div class="dash-empty">
            <div class="dash-empty-title">No active mentees</div>
            <div class="dash-empty-sub">Accepted mentorship requests will appear here.</div>
          </div>
          @endforelse
        @else
          @forelse($matches as $m)
          <div class="dash-row">
            <div class="dash-avatar">{{ $m['user']->initials }}</div>
            <div class="dash-row-main">
              <div class="dash-row-title">{{ $m['user']->full_name }}</div>
              <div class="dash-row-sub">{{ $m['user']->level }} · {{ implode(', ', $m['user']->hasSkills->take(3)->pluck('name')->toArray()) }}</div>
            </div>
            <div class="dash-match">
              <div class="dash-match-score">{{ $m['score'] }}%</div>
              <div class="dash-match-label">match</div>
            </div>
          </div>
          @empty
          <div class="dash-empty">
            <div class="dash-empty-title">No matches yet</div>
            <div class="dash-empty-sub">Complete your profile to get matched with a mentor.</div>
            <a href="{{ route('profile.edit') }}" class="dash-btn dash-btn-primary dash-btn-sm">Complete Profile</a>
          </div>
          @endforelse
        @endif
      </div>
    </div>

    {{-- Right: upcoming sessions --}}
    <div class="dash-panel">
      <div class="dash-panel-head">
        <h2 class="dash-panel-title">Upcoming Sessions</h2>
        <a href="{{ route('sessions.index') }}" class="dash-link">View all</a>
      </div>
      <div class="dash-panel-body">
        @forelse($upcomingSessions as $s)
        <div class="dash-session">
          <div class="dash-date-block">
            <div class="dash-date-day">{{ strtoupper($s->scheduled_at->format('D')) }}</div>
            <div class="dash-date-num">{{ $s->scheduled_at->format('d') }}</div>
          </div>
          <div class="dash-row-main">
            <div class="dash-row-title">{{ $s->title }}</div>
            <div class="dash-row-sub">{{ $s->scheduled_at->format('g:i A') }}</div>
            <div class="session-countdown dash-countdown" data-time="{{ $s->scheduled_at->toISOString() }}"></div>
          </div>
        </div>
        @empty
        <div class="dash-empty">
          <div class="dash-empty-title">No upcoming sessions</div>
          <div class="dash-empty-sub">Schedule a session to keep momentum going.</div>
          <a href="{{ route('sessions.index') }}" class="dash-btn dash-btn-primary dash-btn-sm">Schedule</a>
        </div>
        @endforelse
        <a href="{{ route('chat.index') }}" class="dash-btn dash-btn-secondary dash-btn-block">Book New Session</a>
      </div>
    </div>
  </div>

  {{-- ============ Learning paths ============ --}}
  <div class="dash-panel">
    <div class="dash-panel-head">
      <h2 class="dash-panel-title">{{ $user->isMentor() ? 'Learning Paths You Mentor' : 'My Learning Paths' }}</h2>
      <a href="{{ route('learning.index') }}" class="dash-link">View all</a>
    </div>
    <div class="dash-panel-body">
      @forelse($learningPaths as $lp)
      <div class="dash-path">
        <div class="dash-path-head">
          <span class="dash-row-title">{{ $lp['path']->title }}@if($user->isMentor() && $lp['path']->mentee) <span class="dash-path-mentee">— {{ $lp['path']->mentee->full_name }}</span>@endif</span>
          <span class="dash-path-pct {{ $lp['progress'] == 100 ? 'is-complete' : '' }}">{{ $lp['progress'] }}%</span>
        </div>
        <div class="dash-progress"><div class="dash-progress-fill {{ $lp['progress'] == 100 ? 'is-complete' : '' }}" style="width:{{ $lp['progress'] }}%"></div></div>
        @if($lp['progress'] == 100)
          <div class="dash-path-note is-complete">Completed — certificate issued</div>
        @else
          <div class="dash-path-note">Due {{ $lp['path']->due_date?->format('M d, Y') ?? '— no deadline' }}</div>
        @endif
      </div>
      @empty
      <div class="dash-empty">
        <div class="dash-empty-title">No learning paths yet</div>
        <div class="dash-empty-sub">{{ $user->isMentor() ? 'Create a learning path for one of your mentees.' : 'Your mentor will create a learning path for you.' }}</div>
        <a href="{{ $user->isMentor() ? route('learning.create') : route('mentors.index') }}" class="dash-btn dash-btn-primary dash-btn-sm">{{ $user->isMentor() ? 'Create Path' : 'Find a Mentor' }}</a>
      </div>
      @endforelse
    </div>
  </div>

  {{-- ============ Engagement chart ============ --}}
  <div class="dash-panel">
    <div class="dash-panel-head">
      <h2 class="dash-panel-title">Engagement — Last 6 Months</h2>
    </div>
    <div class="dash-panel-body">
      <canvas id="engagementChart" width="900" height="200" style="max-width:100%"></canvas>
    </div>
  </div>

  @php
    $pathCompleted   = collect($learningPaths)->filter(fn($lp) => $lp['progress'] == 100)->count();
    $pathInProgress  = collect($learningPaths)->filter(fn($lp) => $lp['progress'] > 0 && $lp['progress'] < 100)->count();
    $pathNotStarted  = collect($learningPaths)->filter(fn($lp) => $lp['progress'] == 0)->count();
  @endphp

  {{-- ============ Donut charts ============ --}}
  <div class="dash-grid-1-1">
    <div class="dash-panel">
      <div class="dash-panel-head">
        <h2 class="dash-panel-title">Learning Path Progress</h2>
      </div>
      <div class="dash-panel-body dash-chart-flex">
        <canvas id="pathDonut" width="160" height="160"></canvas>
        <div class="dash-legend">
          <div class="dash-legend-item"><span class="dash-legend-dot" style="background:#0f766e"></span>Completed — <strong>{{ $pathCompleted }}</strong></div>
          <div class="dash-legend-item"><span class="dash-legend-dot" style="background:#1e3a8a"></span>In Progress — <strong>{{ $pathInProgress }}</strong></div>
          <div class="dash-legend-item"><span class="dash-legend-dot" style="background:#cbd5e1"></span>Not Started — <strong>{{ $pathNotStarted }}</strong></div>
        </div>
      </div>
    </div>

    <div class="dash-panel">
      <div class="dash-panel-head">
        <h2 class="dash-panel-title">Sessions by Type</h2>
      </div>
      <div class="dash-panel-body dash-chart-flex">
        <canvas id="typeDonut" width="160" height="160"></canvas>
        <div class="dash-legend">
          <div class="dash-legend-item"><span class="dash-legend-dot" style="background:#1e3a8a"></span>Video — <strong>{{ $sessionsByType['video'] ?? 0 }}</strong></div>
          <div class="dash-legend-item"><span class="dash-legend-dot" style="background:#b45309"></span>Voice — <strong>{{ $sessionsByType['voice'] ?? 0 }}</strong></div>
          <div class="dash-legend-item"><span class="dash-legend-dot" style="background:#0f766e"></span>Chat — <strong>{{ $sessionsByType['chat'] ?? 0 }}</strong></div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@push('styles')
<style>
/* ============================================================
   Dashboard — corporate/enterprise restyle (page-scoped)
   All rules are prefixed .dash so nothing leaks to other pages.
   ============================================================ */
.dash { --d-navy:#1e3a8a; --d-navy-dark:#172554; --d-ink:#0f172a; --d-ink-2:#475569; --d-ink-3:#64748b;
        --d-line:#e2e8f0; --d-panel:#ffffff; --d-panel-2:#f8fafc; --d-teal:#0f766e; --d-red:#b91c1c;
        font-family:'Inter',sans-serif; }
[data-theme="dark"] .dash { --d-ink:#f1f5f9; --d-ink-2:#cbd5e1; --d-ink-3:#94a3b8;
        --d-line:#334155; --d-panel:#1e293b; --d-panel-2:#253044; --d-navy:#3b5bdb; }

/* ---- Header ---- */
.dash-header { display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:16px; margin-bottom:28px; }
.dash-breadcrumb { font-size:0.72rem; font-weight:600; letter-spacing:0.1em; text-transform:uppercase; color:var(--d-ink-3); margin-bottom:6px; }
.dash-title { font-size:1.45rem; font-weight:700; color:var(--d-ink); letter-spacing:-0.01em; margin:0 0 6px; }
.dash-meta { display:flex; align-items:center; gap:8px; font-size:0.84rem; color:var(--d-ink-2); margin:0; flex-wrap:wrap; }
.dash-meta-sep { color:var(--d-line); }
.dash-role-chip { border:1px solid var(--d-line); background:var(--d-panel); color:var(--d-ink-2);
                  border-radius:4px; padding:2px 8px; font-size:0.72rem; font-weight:600; }
.dash-header-actions { display:flex; gap:10px; }

/* ---- Buttons ---- */
.dash-btn { display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:9px 16px;
            border-radius:6px; font-family:'Inter',sans-serif; font-weight:600; font-size:0.84rem;
            cursor:pointer; border:1px solid transparent; text-decoration:none; transition:background 0.15s, border-color 0.15s; }
.dash-btn-primary { background:var(--d-navy); color:#fff; }
.dash-btn-primary:hover { background:var(--d-navy-dark); }
.dash-btn-secondary { background:var(--d-panel); color:var(--d-ink-2); border-color:var(--d-line); }
.dash-btn-secondary:hover { border-color:var(--d-ink-3); color:var(--d-ink); }
.dash-btn-ghost-danger { background:transparent; color:var(--d-red); border-color:var(--d-line); }
.dash-btn-ghost-danger:hover { border-color:var(--d-red); }
.dash-btn-sm { padding:6px 12px; font-size:0.78rem; }
.dash-btn-block { width:100%; margin-top:12px; }

/* ---- KPI row ---- */
.dash-kpis { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px; }
.dash-kpi { background:var(--d-panel); border:1px solid var(--d-line); border-radius:8px; padding:18px 20px; }
.dash-kpi-label { font-size:0.76rem; font-weight:500; color:var(--d-ink-3); margin-bottom:8px; }
.dash-kpi-value { font-size:1.7rem; font-weight:700; color:var(--d-ink); line-height:1; letter-spacing:-0.02em; }
.dash-kpi-trend { font-size:0.73rem; font-weight:500; color:var(--d-ink-3); margin-top:8px; }
.dash-kpi-trend.is-up { color:var(--d-teal); font-weight:600; }

/* ---- Panels ---- */
.dash-panel { background:var(--d-panel); border:1px solid var(--d-line); border-radius:8px; margin-bottom:24px; }
.dash-panel-head { display:flex; justify-content:space-between; align-items:center; padding:16px 20px; border-bottom:1px solid var(--d-line); }
.dash-panel-title { font-size:0.92rem; font-weight:600; color:var(--d-ink); margin:0; }
.dash-panel-body { padding:16px 20px; }
.dash-link { font-size:0.8rem; font-weight:500; color:var(--d-navy); text-decoration:none; }
.dash-link:hover { text-decoration:underline; }
.dash-count-chip { font-size:0.74rem; font-weight:600; color:var(--d-ink-2); background:var(--d-panel-2);
                   border:1px solid var(--d-line); border-radius:4px; padding:3px 9px; }

/* ---- Rows (people / requests) ---- */
.dash-row { display:flex; align-items:center; gap:14px; padding:12px 0; border-bottom:1px solid var(--d-line); }
.dash-row:last-child { border-bottom:none; }
.dash-row-main { flex:1; min-width:0; }
.dash-row-title { font-weight:600; font-size:0.88rem; color:var(--d-ink); }
.dash-row-sub { font-size:0.78rem; color:var(--d-ink-3); }
.dash-row-detail { font-size:0.8rem; color:var(--d-ink-2); margin-top:2px; }
.dash-row-detail span { color:var(--d-ink-3); }
.dash-row-actions { display:flex; gap:8px; flex-shrink:0; }
.dash-truncate { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

/* ---- Avatars: flat, no gradients ---- */
.dash-avatar { width:40px; height:40px; border-radius:6px; background:var(--d-panel-2); border:1px solid var(--d-line);
               color:var(--d-ink-2); font-weight:600; font-size:0.8rem;
               display:inline-flex; align-items:center; justify-content:center; flex-shrink:0; }
.dash-avatar-img { width:40px; height:40px; border-radius:6px; object-fit:cover; flex-shrink:0; }

/* ---- Match score ---- */
.dash-match { text-align:right; flex-shrink:0; }
.dash-match-score { font-weight:700; font-size:0.95rem; color:var(--d-navy); }
.dash-match-label { font-size:0.7rem; color:var(--d-ink-3); }

/* ---- Sessions ---- */
.dash-session { display:flex; align-items:center; gap:14px; padding:12px 0; border-bottom:1px solid var(--d-line); }
.dash-session:last-of-type { border-bottom:none; }
.dash-date-block { border:1px solid var(--d-line); background:var(--d-panel-2); border-radius:6px;
                   padding:8px 12px; text-align:center; flex-shrink:0; min-width:56px; }
.dash-date-day { font-size:0.66rem; font-weight:600; letter-spacing:0.08em; color:var(--d-ink-3); }
.dash-date-num { font-weight:700; font-size:1.25rem; color:var(--d-ink); line-height:1.1; }
.dash-countdown { font-size:0.74rem; font-weight:600; color:var(--d-navy); margin-top:3px; }

/* ---- Learning paths ---- */
.dash-path { padding:13px 0; border-bottom:1px solid var(--d-line); }
.dash-path:last-child { border-bottom:none; }
.dash-path-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; }
.dash-path-pct { font-weight:700; font-size:0.85rem; color:var(--d-navy); }
.dash-path-pct.is-complete { color:var(--d-teal); }
.dash-progress { width:100%; height:5px; background:var(--d-panel-2); border-radius:3px; overflow:hidden; }
.dash-progress-fill { height:100%; background:var(--d-navy); border-radius:3px; transition:width 0.5s ease; }
.dash-progress-fill.is-complete { background:var(--d-teal); }
.dash-path-note { font-size:0.75rem; color:var(--d-ink-3); margin-top:5px; }
.dash-path-note.is-complete { color:var(--d-teal); }
.dash-path-mentee { font-weight:500; color:var(--d-ink-3); font-size:0.82rem; }

/* ---- Mentee row mini progress ---- */
.dash-mentee-progress { display:flex; align-items:center; gap:8px; margin-top:6px; max-width:220px; }
.dash-mentee-progress .dash-progress { flex:1; }
.dash-mentee-progress-pct { font-size:0.72rem; font-weight:600; color:var(--d-ink-2); flex-shrink:0; }

/* ---- Empty states ---- */
.dash-empty { text-align:center; padding:28px 12px; }
.dash-empty-title { font-weight:600; font-size:0.88rem; color:var(--d-ink); margin-bottom:4px; }
.dash-empty-sub { font-size:0.79rem; color:var(--d-ink-3); margin-bottom:14px; }

/* ---- Grids / charts ---- */
.dash-grid-2-1 { display:grid; grid-template-columns:2fr 1fr; gap:20px; }
.dash-grid-1-1 { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
.dash-chart-flex { display:flex; align-items:center; gap:24px; }
.dash-legend { display:flex; flex-direction:column; gap:10px; font-size:0.82rem; color:var(--d-ink-2); }
.dash-legend-item { display:flex; align-items:center; gap:8px; }
.dash-legend-dot { width:10px; height:10px; border-radius:2px; display:inline-block; flex-shrink:0; }

@media (max-width: 900px) {
  .dash-kpis { grid-template-columns:repeat(2,1fr); }
  .dash-grid-2-1, .dash-grid-1-1 { grid-template-columns:1fr; }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  //  Engagement chart
  const labels = @json($engagement->keys());
  const data   = @json($engagement->values());
  if (labels.length) drawBarChart('engagementChart', labels, data, '#1e3a8a');
  else drawBarChart('engagementChart', ['Aug','Sep','Oct','Nov','Dec','Jan'], [0,0,0,0,0,0], '#1e3a8a');

  // Learning path donut
  drawDonutChart('pathDonut', [
    { value: {{ $pathCompleted }},  color: '#0f766e' },
    { value: {{ $pathInProgress }}, color: '#1e3a8a' },
    { value: {{ $pathNotStarted }}, color: '#cbd5e1' },
  ]);

  // Session type donut
  const sessionTypes = @json($sessionsByType);
  const typeColors = { video: '#1e3a8a', voice: '#b45309', chat: '#0f766e' };
  const typeSegments = Object.entries(sessionTypes).map(([t, v]) => ({ value: v, color: typeColors[t] || '#94a3b8' }));
  drawDonutChart('typeDonut', typeSegments.length ? typeSegments : [{ value: 1, color: '#e2e8f0' }]);

  //  Session countdown
  function updateCountdowns() {
    document.querySelectorAll('.session-countdown').forEach(el => {
      const diff = new Date(el.dataset.time) - new Date();
      if (diff <= 0) { el.textContent = 'Starting now'; return; }
      const h = Math.floor(diff / 3600000);
      const m = Math.floor((diff % 3600000) / 60000);
      const d = Math.floor(diff / 86400000);
      if (d > 0)      el.textContent = `Starts in ${d}d ${h % 24}h`;
      else if (h > 0) el.textContent = `Starts in ${h}h ${m}m`;
      else            el.textContent = `Starts in ${m}m`;
    });
  }
  updateCountdowns();
  setInterval(updateCountdowns, 30000);
});
</script>
@endpush
