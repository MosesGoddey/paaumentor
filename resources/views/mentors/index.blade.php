@extends('layouts.sidebar')
@section('title', 'Find a Mentor')

@section('page-content')
<div class="md">

  <div class="md-header">
    <div>
      <div class="md-breadcrumb">Mentorship</div>
      <h1 class="md-title">Find a Mentor</h1>
      <p class="md-sub">Browse verified PAAU mentors or let smart matching find the best fit for your goals.</p>
    </div>
  </div>

  {{-- Search + filters --}}
  <form method="GET" action="{{ route('mentors.index') }}" class="md-panel md-filterbar">
    <input type="text" name="search" class="md-input md-search" placeholder="Search by name, skill, or course…" value="{{ request('search') }}">
    <select name="level" class="md-input md-select">
      <option value="">All Levels</option>
      @foreach(['200L','300L','400L','500L','Alumni'] as $l)
        <option value="{{ $l }}" {{ request('level')===$l?'selected':'' }}>{{ $l }}</option>
      @endforeach
    </select>
    <select name="skill" class="md-input md-select">
      <option value="">All Skills</option>
      @foreach($skills as $skill)
        <option value="{{ $skill->name }}" {{ request('skill')===$skill->name?'selected':'' }}>{{ $skill->name }}</option>
      @endforeach
    </select>
    <button type="submit" class="md-btn md-btn-primary">Search</button>
    <a href="{{ route('mentors.index') }}" class="md-btn md-btn-secondary">Clear</a>
  </form>

  {{-- AI Smart Match --}}
  <div class="md-panel">
    <div class="md-panel-head">
      <div>
        <h2 class="md-panel-title">AI Smart Mentor Match</h2>
        <div class="md-panel-sub">Describe your goals and get intelligent mentor recommendations</div>
      </div>
    </div>
    <div class="md-panel-body">
      <div class="md-ai-form">
        <textarea id="ai-goals" class="md-input" rows="2"
                  placeholder="e.g. I want to learn web development with PHP, improve data structures, and prepare for industry internships…"></textarea>
        <button type="button" onclick="findAiMatch()" id="ai-match-btn" class="md-btn md-btn-primary">Find Match</button>
      </div>
      <div id="ai-results" style="display:none;margin-top:18px">
        <div class="md-ai-results-label">Recommended Mentors</div>
        <div id="ai-cards" class="md-ai-grid"></div>
      </div>
    </div>
  </div>

  <div class="md-result-count">Showing <strong>{{ $mentors->count() }}</strong> {{ $mentors->count() === 1 ? 'mentor' : 'mentors' }}</div>

  {{-- Mentor cards --}}
  <div class="md-grid">
    @forelse($mentors as $m)
    @php
      $mentor = $m['mentor'];
      $score  = $m['score'];
      $tier   = $mentor->mentor_tier;
    @endphp
    <div class="md-card">
      <div class="md-card-top">
        @if($mentor->avatar_url)
        <img src="{{ $mentor->avatar_url }}" alt="" class="md-avatar-img">
        @else
        <div class="md-avatar">{{ $mentor->initials }}</div>
        @endif
        <div class="md-card-id">
          <div class="md-name-row">
            <span class="md-name">{{ $mentor->full_name }}</span>
            <span class="md-tier md-tier-{{ $tier }}">{{ $mentor->mentor_tier_label }}</span>
          </div>
          <div class="md-card-sub">{{ $mentor->level }} · {{ $mentor->department }}</div>
        </div>
        @if($mentor->is_active)
        <span class="md-online" title="Online"><span class="md-online-dot"></span>Online</span>
        @endif
      </div>

      <div class="md-skills">
        @foreach($mentor->hasSkills->take(4) as $skill)
        <span class="md-skill">{{ $skill->name }}</span>
        @endforeach
      </div>

      <div class="md-stats">
        <div class="md-stat">
          <span class="md-stat-value">{{ $mentor->average_rating > 0 ? number_format($mentor->average_rating,1) : '—' }}</span>
          <span class="md-stat-label">Rating</span>
        </div>
        <div class="md-stat-div"></div>
        <div class="md-stat">
          <span class="md-stat-value">{{ $mentor->mentorMentorships->count() }}</span>
          <span class="md-stat-label">Mentees</span>
        </div>
        @if($score >= 80)
        <span class="md-match-chip">{{ $score }}% match</span>
        @endif
      </div>

      @if($mentor->bio)
      <p class="md-bio">{{ Str::limit($mentor->bio, 110) }}</p>
      @endif

      <div class="md-card-actions">
        <a href="{{ route('mentors.show', $mentor) }}" class="md-btn md-btn-primary md-btn-grow">View &amp; Request</a>
        <a href="{{ route('chat.index') }}" class="md-btn md-btn-secondary">Message</a>
      </div>
    </div>
    @empty
    <div class="md-empty">
      <div class="md-empty-title">No mentors found</div>
      <p class="md-empty-sub">Try adjusting your search or filters.</p>
    </div>
    @endforelse
  </div>
</div>
@endsection

@push('styles')
<style>
/* ============================================================
   Mentor directory — corporate restyle, page-scoped .md
   ============================================================ */
.md { --m-navy:#1e3a8a; --m-navy-dark:#172554; --m-ink:#0f172a; --m-ink-2:#475569; --m-ink-3:#64748b;
      --m-line:#e2e8f0; --m-panel:#ffffff; --m-panel-2:#f8fafc; --m-teal:#0f766e; --m-gold:#b45309;
      font-family:'Inter',sans-serif; }
[data-theme="dark"] .md { --m-ink:#f1f5f9; --m-ink-2:#cbd5e1; --m-ink-3:#94a3b8;
      --m-line:#334155; --m-panel:#1e293b; --m-panel-2:#253044; --m-navy:#3b5bdb; }

/* ---- Header ---- */
.md-header { margin-bottom:20px; }
.md-breadcrumb { font-size:0.72rem; font-weight:600; letter-spacing:0.1em; text-transform:uppercase; color:var(--m-ink-3); margin-bottom:6px; }
.md-title { font-size:1.45rem; font-weight:700; color:var(--m-ink); letter-spacing:-0.01em; margin:0 0 4px; }
.md-sub { font-size:0.86rem; color:var(--m-ink-3); margin:0; }

/* ---- Panels ---- */
.md-panel { background:var(--m-panel); border:1px solid var(--m-line); border-radius:8px; margin-bottom:20px; }
.md-panel-head { padding:14px 18px; border-bottom:1px solid var(--m-line); }
.md-panel-title { font-size:0.92rem; font-weight:600; color:var(--m-ink); margin:0; }
.md-panel-sub { font-size:0.78rem; color:var(--m-ink-3); margin-top:2px; }
.md-panel-body { padding:16px 18px; }

/* ---- Inputs & buttons ---- */
.md-input { border:1px solid var(--m-line); border-radius:6px; padding:9px 12px; font-size:0.85rem;
            font-family:'Inter',sans-serif; background:var(--m-panel); color:var(--m-ink); outline:none;
            transition:border-color 0.15s; }
.md-input:focus { border-color:var(--m-navy); }
.md-btn { display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:9px 16px;
          border-radius:6px; font-family:'Inter',sans-serif; font-weight:600; font-size:0.84rem;
          cursor:pointer; border:1px solid transparent; text-decoration:none; transition:background 0.15s, border-color 0.15s; white-space:nowrap; }
.md-btn-primary { background:var(--m-navy); color:#fff; }
.md-btn-primary:hover { background:var(--m-navy-dark); }
.md-btn-primary:disabled { opacity:0.6; cursor:not-allowed; }
.md-btn-secondary { background:var(--m-panel); color:var(--m-ink-2); border-color:var(--m-line); }
.md-btn-secondary:hover { border-color:var(--m-ink-3); color:var(--m-ink); }
.md-btn-grow { flex:1; }

/* ---- Filter bar ---- */
.md-filterbar { display:flex; gap:10px; padding:12px 14px; align-items:center; flex-wrap:wrap; }
.md-search { flex:1; min-width:200px; background:var(--m-panel-2); }
.md-select { width:auto; background:var(--m-panel-2); }

/* ---- AI match ---- */
.md-ai-form { display:flex; gap:10px; align-items:flex-end; }
.md-ai-form textarea { flex:1; resize:none; background:var(--m-panel-2); }
.md-ai-results-label { font-size:0.74rem; font-weight:600; text-transform:uppercase; letter-spacing:0.08em;
                       color:var(--m-ink-3); margin-bottom:12px; padding-bottom:8px; border-bottom:1px solid var(--m-line); }
.md-ai-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(230px,1fr)); gap:14px; }

/* ---- Result count ---- */
.md-result-count { font-size:0.84rem; color:var(--m-ink-3); margin-bottom:14px; }
.md-result-count strong { color:var(--m-ink); }

/* ---- Mentor cards ---- */
.md-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:20px; }
.md-card { background:var(--m-panel); border:1px solid var(--m-line); border-radius:8px; padding:20px; display:flex; flex-direction:column; }
.md-card-top { display:flex; align-items:flex-start; gap:12px; margin-bottom:14px; }
.md-avatar { width:44px; height:44px; border-radius:6px; background:var(--m-panel-2); border:1px solid var(--m-line);
             color:var(--m-ink-2); font-weight:600; font-size:0.85rem;
             display:inline-flex; align-items:center; justify-content:center; flex-shrink:0; }
.md-avatar-img { width:44px; height:44px; border-radius:6px; object-fit:cover; flex-shrink:0; }
.md-card-id { flex:1; min-width:0; }
.md-name-row { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.md-name { font-weight:600; font-size:0.95rem; color:var(--m-ink); }
.md-card-sub { font-size:0.78rem; color:var(--m-ink-3); margin-top:2px; }

/* Tier chips — quiet, earned-looking */
.md-tier { font-size:0.66rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;
           border:1px solid var(--m-line); border-radius:4px; padding:2px 7px; color:var(--m-ink-3); }
.md-tier-senior { color:var(--m-navy); border-color:var(--m-navy); }
.md-tier-lead   { color:var(--m-gold); border-color:var(--m-gold); }

/* Online indicator */
.md-online { display:inline-flex; align-items:center; gap:5px; font-size:0.72rem; font-weight:500; color:var(--m-teal); flex-shrink:0; }
.md-online-dot { width:7px; height:7px; border-radius:50%; background:var(--m-teal); }

/* Skills */
.md-skills { display:flex; flex-wrap:wrap; gap:6px; margin-bottom:14px; min-height:24px; }
.md-skill { font-size:0.72rem; font-weight:500; padding:3px 9px; border-radius:4px;
            background:var(--m-panel-2); border:1px solid var(--m-line); color:var(--m-ink-2); }

/* Stats line */
.md-stats { display:flex; align-items:center; gap:14px; padding:10px 0; border-top:1px solid var(--m-line); border-bottom:1px solid var(--m-line); margin-bottom:12px; }
.md-stat { display:flex; align-items:baseline; gap:6px; }
.md-stat-value { font-weight:700; font-size:0.95rem; color:var(--m-ink); }
.md-stat-label { font-size:0.72rem; color:var(--m-ink-3); }
.md-stat-div { width:1px; height:16px; background:var(--m-line); }
.md-match-chip { margin-left:auto; font-size:0.72rem; font-weight:600; color:var(--m-navy);
                 border:1px solid var(--m-navy); border-radius:4px; padding:2px 8px; }

/* Bio */
.md-bio { font-size:0.81rem; color:var(--m-ink-2); line-height:1.6; margin:0 0 14px; }

/* Actions */
.md-card-actions { display:flex; gap:8px; margin-top:auto; }

/* AI result cards (JS-injected) */
.md-ai-card { background:var(--m-panel-2); border:1px solid var(--m-line); border-radius:8px; padding:14px; }
.md-ai-reason { font-size:0.75rem; color:var(--m-ink-2); background:var(--m-panel); border:1px solid var(--m-line);
                border-radius:6px; padding:7px 10px; margin:8px 0; line-height:1.5; }
.md-ai-reason strong { color:var(--m-navy); }

/* Empty state */
.md-empty { grid-column:1 / -1; text-align:center; padding:56px 20px; background:var(--m-panel); border:1px solid var(--m-line); border-radius:8px; }
.md-empty-title { font-weight:600; font-size:0.95rem; color:var(--m-ink); margin-bottom:6px; }
.md-empty-sub { font-size:0.82rem; color:var(--m-ink-3); margin:0; }

@media (max-width:700px) { .md-ai-form { flex-direction:column; align-items:stretch; } }
</style>
@endpush

@push('scripts')
@php
$mentorJson = $mentors->map(fn($m) => [
    'id'       => $m['mentor']->id,
    'name'     => $m['mentor']->full_name,
    'level'    => $m['mentor']->level,
    'dept'     => $m['mentor']->department,
    'skills'   => $m['mentor']->hasSkills->pluck('name')->values()->toArray(),
    'rating'   => $m['mentor']->average_rating,
    'mentees'  => $m['mentor']->mentorMentorships->count(),
    'bio'      => \Illuminate\Support\Str::limit($m['mentor']->bio ?? '', 100),
    'initials' => $m['mentor']->initials,
    'url'      => route('mentors.show', $m['mentor']),
    'online'   => (bool) $m['mentor']->is_active,
])->values();
@endphp
<script>
const mentorData = @json($mentorJson);

async function findAiMatch() {
  const goals = document.getElementById('ai-goals').value.trim();
  if (!goals) { alert('Please describe your goals first.'); return; }

  const btn = document.getElementById('ai-match-btn');
  btn.textContent = 'Analyzing…';
  btn.disabled = true;

  try {
    const res = await fetch('{{ route("ai.mentors.match") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({goals}),
    });
    const data = await res.json();
    if (data.error) { alert(data.error); return; }

    const container = document.getElementById('ai-cards');
    container.innerHTML = '';

    (data.matches || []).forEach(match => {
      const mentor = mentorData.find(m => m.id === match.mentor_id);
      if (!mentor) return;
      const skills = (mentor.skills || []).slice(0, 3)
        .map(s => `<span class="md-skill">${s}</span>`).join(' ');
      container.innerHTML += `
        <div class="md-ai-card">
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px">
            <div class="md-avatar" style="width:36px;height:36px;font-size:0.72rem">${mentor.initials}</div>
            <div style="min-width:0">
              <div class="md-name" style="font-size:0.88rem">${mentor.name}</div>
              <div class="md-card-sub">${mentor.level} · ${mentor.dept}</div>
            </div>
            ${mentor.online ? '<span class="md-online" style="margin-left:auto"><span class="md-online-dot"></span></span>' : ''}
          </div>
          <div class="md-ai-reason"><strong>Why:</strong> ${match.reason}</div>
          <div class="md-skills" style="margin-bottom:10px">${skills}</div>
          <a href="${mentor.url}" class="md-btn md-btn-primary" style="width:100%;font-size:0.8rem;padding:7px 12px">View &amp; Request</a>
        </div>`;
    });

    if (!(data.matches || []).length) {
      container.innerHTML = '<p style="color:var(--m-ink-3);font-size:0.85rem;grid-column:1/-1">No matches found. Try adjusting your goals description.</p>';
    }

    document.getElementById('ai-results').style.display = 'block';
    document.getElementById('ai-results').scrollIntoView({behavior: 'smooth'});
  } catch (err) {
    alert('Failed to get AI match. Please try again.');
  } finally {
    btn.textContent = 'Find Match';
    btn.disabled = false;
  }
}
</script>
@endpush
