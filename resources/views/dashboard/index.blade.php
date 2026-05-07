@extends('layouts.sidebar')
@section('title', 'Dashboard')

@section('page-content')
<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;flex-wrap:wrap;gap:16px">
  <div>
    <h1 style="font-size:1.6rem;font-weight:800;margin-bottom:4px">Good {{ now()->format('H') < 12 ? 'morning' : (now()->format('H') < 17 ? 'afternoon' : 'evening') }}, {{ $user->first_name }}</h1>
    <p style="color:var(--text-3);font-size:0.9rem;display:flex;align-items:center;gap:8px;margin-bottom:6px">
      <span style="background:var(--blue-500);color:#fff;border-radius:6px;padding:2px 8px;font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em">{{ $user->role }}</span>
      {{ $user->level }} · {{ $user->department }} · Student ID: {{ $user->student_id }}
    </p>
    <p style="font-size:0.88rem;color:var(--blue-500);font-weight:600;min-height:1.4em;margin:0">
      <span id="typewriter"></span><span id="typeCursor" style="display:inline-block;width:2px;height:0.9em;background:var(--blue-500);margin-left:1px;vertical-align:middle;animation:cursorBlink 0.7s step-end infinite"></span>
    </p>
  </div>
  <div style="display:flex;gap:10px">
    @if($user->isMentee())
    <a href="{{ route('mentors.index') }}" class="btn btn-outline btn-sm">Find Mentor</a>
    @endif
    <a href="{{ route('chat.index') }}"    class="btn btn-primary btn-sm">Messages</a>
  </div>
</div>

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:28px">
  <div class="stat-card">
    <div class="stat-value count-up" data-target="{{ $mentorships->count() }}">0</div>
    <div class="stat-label">Active Mentors</div>
  </div>
  <div class="stat-card">
    <div class="stat-value count-up" data-target="{{ $sessionCount }}">0</div>
    <div class="stat-label">Sessions Completed</div>
  </div>
  <div class="stat-card">
    <div class="stat-value count-up" data-target="{{ count($learningPaths) }}">0</div>
    <div class="stat-label">Learning Paths</div>
  </div>
  <div class="stat-card">
    <div class="stat-value count-up" data-target="{{ $certificates->count() }}">0</div>
    <div class="stat-label">Certificates</div>
  </div>
</div>

@if($user->isMentor() && $pendingRequests->isNotEmpty())
<div class="card" style="margin-bottom:24px;border-left:4px solid var(--blue-500)">
  <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1rem;margin-bottom:16px;display:flex;align-items:center;gap:8px">
    Mentorship Requests
    <span style="background:var(--blue-500);color:#fff;border-radius:999px;padding:2px 9px;font-size:0.75rem;font-weight:700">{{ $pendingRequests->count() }}</span>
  </div>
  @foreach($pendingRequests as $req)
  <div style="display:flex;align-items:center;gap:14px;padding:14px;border-radius:14px;border:1px solid var(--border);margin-bottom:10px">
    @if($req->mentee->avatar_url)
    <img src="{{ $req->mentee->avatar_url }}" alt="" style="width:44px;height:44px;border-radius:50%;object-fit:cover;flex-shrink:0">
    @else
    <div class="avatar avatar-md" style="flex-shrink:0">{{ $req->mentee->initials }}</div>
    @endif
    <div style="flex:1;min-width:0">
      <div style="font-weight:700;font-size:0.92rem">{{ $req->mentee->full_name }}</div>
      <div style="font-size:0.78rem;color:var(--text-3)">{{ $req->mentee->level }} · {{ $req->mentee->department }}</div>
      <div style="font-size:0.82rem;margin-top:4px"><span style="color:var(--text-3)">Topic:</span> {{ $req->topic }}</div>
      @if($req->goal)<div style="font-size:0.78rem;color:var(--text-3);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $req->goal }}</div>@endif
    </div>
    <div style="display:flex;flex-direction:column;gap:6px;flex-shrink:0">
      <form method="POST" action="{{ route('mentors.respond', $req) }}">
        @csrf @method('PATCH')
        <input type="hidden" name="action" value="accept">
        <button type="submit" class="btn btn-primary btn-sm">✓ Accept</button>
      </form>
      <form method="POST" action="{{ route('mentors.respond', $req) }}">
        @csrf @method('PATCH')
        <input type="hidden" name="action" value="reject">
        <button type="submit" class="btn btn-outline btn-sm" style="color:#dc2626;border-color:#dc2626">✗ Decline</button>
      </form>
    </div>
  </div>
  @endforeach
</div>
@endif

<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;margin-bottom:24px">

  <div class="card">
    <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1rem;margin-bottom:16px;display:flex;justify-content:space-between">
      Smart Mentor Matches
      <a href="{{ route('mentors.index') }}" style="font-size:0.8rem;font-weight:500;color:var(--blue-500)">View all →</a>
    </div>
    @forelse($matches as $m)
    <div style="display:flex;align-items:center;gap:14px;padding:16px;border-radius:14px;border:1px solid var(--border);margin-bottom:12px">
      <div class="avatar avatar-md">{{ $m['user']->initials }}</div>
      <div style="flex:1">
        <div style="font-weight:700;font-size:0.92rem">{{ $m['user']->full_name }}</div>
        <div style="font-size:0.78rem;color:var(--text-3)">{{ $m['user']->level }} · {{ implode(', ', $m['user']->hasSkills->take(3)->pluck('name')->toArray()) }}</div>
      </div>
      <div style="text-align:right">
        <div style="background:linear-gradient(135deg,var(--blue-500),var(--blue-700));color:#fff;border-radius:10px;padding:4px 10px;font-family:'Sora',sans-serif;font-weight:800;font-size:0.82rem">{{ $m['score'] }}%</div>
        <div style="font-size:0.7rem;color:var(--text-3);margin-top:2px">match</div>
      </div>
    </div>
    @empty
    <div style="text-align:center;padding:32px 12px">
      <div style="font-size:2.5rem;margin-bottom:8px">🔍</div>
      <div style="font-weight:700;font-size:0.88rem;margin-bottom:4px">No matches yet</div>
      <div style="font-size:0.78rem;color:var(--text-3);margin-bottom:12px">Complete your profile to get matched with a mentor</div>
      <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm">Complete Profile</a>
    </div>
    @endforelse
  </div>

  <div class="card">
    <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1rem;margin-bottom:16px">Upcoming Sessions</div>
    @forelse($upcomingSessions as $s)
    <div style="background:var(--surface-2);border-radius:14px;padding:16px;margin-bottom:12px;display:flex;align-items:center;gap:14px">
      <div style="background:var(--blue-500);color:#fff;border-radius:10px;padding:8px 12px;text-align:center;flex-shrink:0">
        <div style="font-size:0.7rem;font-weight:600;letter-spacing:0.06em">{{ strtoupper($s->scheduled_at->format('D')) }}</div>
        <div style="font-family:'Sora',sans-serif;font-weight:800;font-size:1.4rem;line-height:1">{{ $s->scheduled_at->format('d') }}</div>
      </div>
      <div style="flex:1;min-width:0">
        <div style="font-weight:700;font-size:0.9rem">{{ $s->title }}</div>
        <div style="font-size:0.78rem;color:var(--text-3)">{{ $s->scheduled_at->format('g:i A') }}</div>
        <div class="session-countdown" data-time="{{ $s->scheduled_at->toISOString() }}"
             style="font-size:0.72rem;font-weight:600;color:var(--blue-500);margin-top:4px"></div>
      </div>
    </div>
    @empty
    <div style="text-align:center;padding:28px 12px">
      <div style="font-size:2.5rem;margin-bottom:8px">📅</div>
      <div style="font-weight:700;font-size:0.88rem;margin-bottom:4px">No upcoming sessions</div>
      <div style="font-size:0.78rem;color:var(--text-3);margin-bottom:12px">Schedule a session with your mentor</div>
      <a href="{{ route('sessions.index') }}" class="btn btn-primary btn-sm">Schedule Now</a>
    </div>
    @endforelse
    <a href="{{ route('chat.index') }}" class="btn btn-outline btn-sm" style="width:100%;justify-content:center;margin-top:8px">+ Book New Session</a>
  </div>
</div>

<div class="card" style="margin-bottom:24px">
  <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1rem;margin-bottom:16px;display:flex;justify-content:space-between">
    My Learning Paths
    <a href="{{ route('learning.index') }}" style="font-size:0.8rem;font-weight:500;color:var(--blue-500)">All →</a>
  </div>
  @forelse($learningPaths as $lp)
  <div style="padding:14px 0;border-bottom:1px solid var(--border)">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
      <span style="font-weight:700;font-size:0.9rem">{{ $lp['path']->title }}</span>
      <span style="font-family:'Sora',sans-serif;font-weight:800;font-size:0.88rem;color:{{ $lp['progress'] == 100 ? 'var(--success)' : 'var(--blue-500)' }}">{{ $lp['progress'] }}%</span>
    </div>
    <div class="progress-bar"><div class="progress-fill {{ $lp['progress'] == 100 ? 'green' : '' }}" style="width:{{ $lp['progress'] }}%"></div></div>
    @if($lp['progress'] == 100)
      <div style="font-size:0.75rem;color:var(--success);margin-top:4px">✓ Completed — Certificate issued!</div>
    @else
      <div style="font-size:0.75rem;color:var(--text-3);margin-top:4px">Due: {{ $lp['path']->due_date?->format('M d, Y') ?? 'No deadline' }}</div>
    @endif
  </div>
  @empty
  <div style="text-align:center;padding:32px 12px">
    <div style="font-size:2.5rem;margin-bottom:8px">🗺️</div>
    <div style="font-weight:700;font-size:0.88rem;margin-bottom:4px">No learning paths yet</div>
    <div style="font-size:0.78rem;color:var(--text-3);margin-bottom:12px">Your mentor will create a learning path for you</div>
    <a href="{{ route('mentors.index') }}" class="btn btn-primary btn-sm">Find a Mentor</a>
  </div>
  @endforelse
</div>

<div class="card" style="margin-bottom:24px">
  <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1rem;margin-bottom:16px">My Engagement — Last 6 Months</div>
  <canvas id="engagementChart" width="900" height="200" style="max-width:100%"></canvas>
</div>

@php
  $pathCompleted   = collect($learningPaths)->filter(fn($lp) => $lp['progress'] == 100)->count();
  $pathInProgress  = collect($learningPaths)->filter(fn($lp) => $lp['progress'] > 0 && $lp['progress'] < 100)->count();
  $pathNotStarted  = collect($learningPaths)->filter(fn($lp) => $lp['progress'] == 0)->count();
@endphp

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
  <div class="card">
    <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1rem;margin-bottom:16px">📚 Learning Path Progress</div>
    <div style="display:flex;align-items:center;gap:24px">
      <canvas id="pathDonut" width="160" height="160" style="flex-shrink:0"></canvas>
      <div style="display:flex;flex-direction:column;gap:10px;font-size:0.83rem">
        <div style="display:flex;align-items:center;gap:8px">
          <span style="width:12px;height:12px;border-radius:3px;background:#10b981;display:inline-block;flex-shrink:0"></span>
          <span>Completed — <strong>{{ $pathCompleted }}</strong></span>
        </div>
        <div style="display:flex;align-items:center;gap:8px">
          <span style="width:12px;height:12px;border-radius:3px;background:#2563eb;display:inline-block;flex-shrink:0"></span>
          <span>In Progress — <strong>{{ $pathInProgress }}</strong></span>
        </div>
        <div style="display:flex;align-items:center;gap:8px">
          <span style="width:12px;height:12px;border-radius:3px;background:#cbd5e1;display:inline-block;flex-shrink:0"></span>
          <span>Not Started — <strong>{{ $pathNotStarted }}</strong></span>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1rem;margin-bottom:16px">📡 Sessions by Type</div>
    <div style="display:flex;align-items:center;gap:24px">
      <canvas id="typeDonut" width="160" height="160" style="flex-shrink:0"></canvas>
      <div style="display:flex;flex-direction:column;gap:10px;font-size:0.83rem">
        <div style="display:flex;align-items:center;gap:8px">
          <span style="width:12px;height:12px;border-radius:3px;background:#2563eb;display:inline-block;flex-shrink:0"></span>
          <span>Video — <strong>{{ $sessionsByType['video'] ?? 0 }}</strong></span>
        </div>
        <div style="display:flex;align-items:center;gap:8px">
          <span style="width:12px;height:12px;border-radius:3px;background:#f59e0b;display:inline-block;flex-shrink:0"></span>
          <span>Voice — <strong>{{ $sessionsByType['voice'] ?? 0 }}</strong></span>
        </div>
        <div style="display:flex;align-items:center;gap:8px">
          <span style="width:12px;height:12px;border-radius:3px;background:#10b981;display:inline-block;flex-shrink:0"></span>
          <span>Chat — <strong>{{ $sessionsByType['chat'] ?? 0 }}</strong></span>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
@keyframes cursorBlink { 0%,100%{opacity:1} 50%{opacity:0} }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // ── Typewriter ────────────────────────────────────────────────
  const isMentor = {{ $user->isMentor() ? 'true' : 'false' }};
  const phrases  = isMentor ? [
    "Your guidance is shaping someone's future today.",
    "Great mentors inspire greatness in others.",
    "Your mentees are counting on you — keep going.",
    "Every lesson you share leaves a lasting impact.",
    "You were once where they are — lead the way.",
  ] : [
    "Keep pushing — your mentor is here to guide you.",
    "Every session brings you one step closer to your goals.",
    "Learning is a journey, not a destination.",
    "Your next breakthrough is just a session away.",
    "Stay consistent — great things take time.",
  ];

  const el      = document.getElementById('typewriter');
  let pi = 0, ci = 0, deleting = false;

  function type() {
    const phrase = phrases[pi];
    if (!deleting) {
      el.textContent = phrase.slice(0, ++ci);
      if (ci === phrase.length) { deleting = true; setTimeout(type, 2200); return; }
      setTimeout(type, 45);
    } else {
      el.textContent = phrase.slice(0, --ci);
      if (ci === 0) { deleting = false; pi = (pi + 1) % phrases.length; setTimeout(type, 400); return; }
      setTimeout(type, 22);
    }
  }
  setTimeout(type, 800);

  // ── Engagement chart ──────────────────────────────────────────
  const labels = @json($engagement->keys());
  const data   = @json($engagement->values());
  if (labels.length) drawBarChart('engagementChart', labels, data, '#2563eb');
  else drawBarChart('engagementChart', ['Aug','Sep','Oct','Nov','Dec','Jan'], [0,0,0,0,0,0], '#2563eb');

  // Learning path donut
  drawDonutChart('pathDonut', [
    { value: {{ $pathCompleted }},  color: '#10b981' },
    { value: {{ $pathInProgress }}, color: '#2563eb' },
    { value: {{ $pathNotStarted }}, color: '#cbd5e1' },
  ]);

  // Session type donut
  const sessionTypes = @json($sessionsByType);
  const typeColors = { video: '#2563eb', voice: '#f59e0b', chat: '#10b981' };
  const typeSegments = Object.entries(sessionTypes).map(([t, v]) => ({ value: v, color: typeColors[t] || '#94a3b8' }));
  drawDonutChart('typeDonut', typeSegments.length ? typeSegments : [{ value: 1, color: '#e2e8f0' }]);

  // ── Count-up animation ────────────────────────────────────────
  document.querySelectorAll('.count-up').forEach(el => {
    const target = parseInt(el.dataset.target, 10);
    if (target === 0) { el.textContent = '0'; return; }
    const duration = 1200;
    const start = performance.now();
    function step(now) {
      const progress = Math.min((now - start) / duration, 1);
      const ease = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.floor(ease * target);
      if (progress < 1) requestAnimationFrame(step);
      else el.textContent = target;
    }
    requestAnimationFrame(step);
  });

  // ── Session countdown ─────────────────────────────────────────
  function updateCountdowns() {
    document.querySelectorAll('.session-countdown').forEach(el => {
      const diff = new Date(el.dataset.time) - new Date();
      if (diff <= 0) { el.textContent = '🔴 Starting now'; el.style.color = 'var(--danger)'; return; }
      const h = Math.floor(diff / 3600000);
      const m = Math.floor((diff % 3600000) / 60000);
      const d = Math.floor(diff / 86400000);
      if (d > 0)      el.textContent = `⏰ Starts in ${d}d ${h % 24}h`;
      else if (h > 0) el.textContent = `⏰ Starts in ${h}h ${m}m`;
      else            { el.textContent = `⏰ Starts in ${m}m`; el.style.color = 'var(--danger)'; }
    });
  }
  updateCountdowns();
  setInterval(updateCountdowns, 30000);
});
</script>
@endpush
