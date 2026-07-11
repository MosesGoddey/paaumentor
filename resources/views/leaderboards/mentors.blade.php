@extends('layouts.sidebar')
@section('title', 'Mentor Leaderboard')

@section('page-content')
<div class="lb">

  <div class="lb-header">
    <div>
      <div class="lb-breadcrumb">Leaderboards</div>
      <h1 class="lb-title">Top Mentors</h1>
      <p class="lb-sub">Recognition for mentors making the biggest impact at PAAU — ranked by rating, active mentees, and certificates issued.</p>
    </div>
    <a href="{{ route('leaderboards.certificates') }}" class="lb-btn lb-btn-secondary">Certificate Progress</a>
  </div>

  @if($mentors->isNotEmpty())
  <div class="lb-panel">
    <div class="lb-thead">
      <span class="lb-th lb-col-rank">#</span>
      <span class="lb-th">Mentor</span>
      <span class="lb-th lb-col-stat">Rating</span>
      <span class="lb-th lb-col-stat">Mentees</span>
      <span class="lb-th lb-col-stat">Certificates</span>
    </div>
    @foreach($mentors as $idx => $mentor)
    <div class="lb-row">
      <span class="lb-rank {{ $idx === 0 ? 'is-first' : ($idx === 1 ? 'is-second' : ($idx === 2 ? 'is-third' : '')) }}">{{ $idx + 1 }}</span>

      <div class="lb-mentor">
        @if($mentor->avatar_url)
        <img src="{{ $mentor->avatar_url }}" alt="" class="lb-avatar-img">
        @else
        <div class="lb-avatar">{{ $mentor->avatar_initials }}</div>
        @endif
        <div class="lb-mentor-id">
          <div class="lb-name-row">
            <a href="{{ route('profile.show', $mentor->id) }}" class="lb-name">{{ $mentor->full_name }}</a>
            <span class="lb-tier lb-tier-{{ $mentor->tier }}">{{ $mentor->tier_label }}</span>
          </div>
          <div class="lb-mentor-sub">{{ ucfirst($mentor->role) }}@if($mentor->department) · {{ $mentor->department }}@endif</div>
        </div>
      </div>

      <span class="lb-stat lb-col-stat">{{ $mentor->rating > 0 ? number_format($mentor->rating, 1) : '—' }}</span>
      <span class="lb-stat lb-col-stat">{{ $mentor->active_mentees }}</span>
      <span class="lb-stat lb-col-stat">{{ $mentor->certs_issued }}</span>
    </div>
    @endforeach
  </div>
  @else
  <div class="lb-empty">
    <div class="lb-empty-title">No active mentors yet</div>
    <p class="lb-empty-sub">As mentors get verified and start guiding students, they'll appear here.</p>
  </div>
  @endif

</div>
@endsection

@push('styles')
<style>
/* ============================================================
   Leaderboards — corporate restyle, page-scoped .lb
   ============================================================ */
.lb { --b-navy:#1e3a8a; --b-navy-dark:#172554; --b-ink:#0f172a; --b-ink-2:#475569; --b-ink-3:#64748b;
      --b-line:#e2e8f0; --b-panel:#ffffff; --b-panel-2:#f8fafc; --b-gold:#b45309; --b-teal:#0f766e;
      max-width:960px; margin:0 auto; font-family:'Inter',sans-serif; }
[data-theme="dark"] .lb { --b-ink:#f1f5f9; --b-ink-2:#cbd5e1; --b-ink-3:#94a3b8;
      --b-line:#334155; --b-panel:#1e293b; --b-panel-2:#253044; --b-navy:#3b5bdb; }

.lb-header { display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:16px; margin-bottom:24px; }
.lb-breadcrumb { font-size:0.72rem; font-weight:600; letter-spacing:0.1em; text-transform:uppercase; color:var(--b-ink-3); margin-bottom:6px; }
.lb-title { font-size:1.45rem; font-weight:700; color:var(--b-ink); letter-spacing:-0.01em; margin:0 0 4px; }
.lb-sub { font-size:0.86rem; color:var(--b-ink-3); margin:0; max-width:560px; }

.lb-btn { display:inline-flex; align-items:center; justify-content:center; padding:9px 16px; border-radius:6px;
          font-weight:600; font-size:0.84rem; cursor:pointer; border:1px solid transparent; text-decoration:none;
          transition:background 0.15s, border-color 0.15s; white-space:nowrap; }
.lb-btn-secondary { background:var(--b-panel); color:var(--b-ink-2); border-color:var(--b-line); }
.lb-btn-secondary:hover { border-color:var(--b-ink-3); color:var(--b-ink); }

/* ---- Table panel ---- */
.lb-panel { background:var(--b-panel); border:1px solid var(--b-line); border-radius:8px; overflow:hidden; }
.lb-thead, .lb-row { display:grid; grid-template-columns:48px 1fr 90px 90px 110px; gap:12px; align-items:center; padding:12px 18px; }
.lb-thead { border-bottom:1px solid var(--b-line); background:var(--b-panel-2); padding-top:10px; padding-bottom:10px; }
.lb-th { font-size:0.68rem; font-weight:600; text-transform:uppercase; letter-spacing:0.08em; color:var(--b-ink-3); }
.lb-row { border-bottom:1px solid var(--b-line); }
.lb-row:last-child { border-bottom:none; }
.lb-col-rank { text-align:center; }
.lb-col-stat { text-align:right; }

.lb-rank { font-weight:700; font-size:0.9rem; color:var(--b-ink-3); text-align:center; }
.lb-rank.is-first  { color:var(--b-gold); }
.lb-rank.is-second { color:#6b7280; }
.lb-rank.is-third  { color:#92613a; }

.lb-mentor { display:flex; align-items:center; gap:12px; min-width:0; }
.lb-avatar { width:38px; height:38px; border-radius:6px; background:var(--b-panel-2); border:1px solid var(--b-line);
             color:var(--b-ink-2); font-weight:600; font-size:0.72rem;
             display:inline-flex; align-items:center; justify-content:center; flex-shrink:0; }
.lb-avatar-img { width:38px; height:38px; border-radius:6px; object-fit:cover; flex-shrink:0; }
.lb-mentor-id { min-width:0; }
.lb-name-row { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.lb-name { font-weight:600; font-size:0.88rem; color:var(--b-ink); text-decoration:none; }
.lb-name:hover { color:var(--b-navy); }
.lb-mentor-sub { font-size:0.75rem; color:var(--b-ink-3); margin-top:1px; }

.lb-tier { font-size:0.64rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;
           border:1px solid var(--b-line); border-radius:4px; padding:2px 7px; color:var(--b-ink-3); }
.lb-tier-senior { color:var(--b-navy); border-color:var(--b-navy); }
.lb-tier-lead   { color:var(--b-gold); border-color:var(--b-gold); }

.lb-stat { font-weight:700; font-size:0.9rem; color:var(--b-ink); }

.lb-empty { text-align:center; padding:56px 20px; background:var(--b-panel); border:1px solid var(--b-line); border-radius:8px; }
.lb-empty-title { font-weight:600; font-size:0.95rem; color:var(--b-ink); margin-bottom:6px; }
.lb-empty-sub { font-size:0.82rem; color:var(--b-ink-3); margin:0; }

@media (max-width:700px) {
  .lb-thead { display:none; }
  .lb-row { grid-template-columns:36px 1fr; row-gap:8px; }
  .lb-col-stat { display:none; }
}
</style>
@endpush
