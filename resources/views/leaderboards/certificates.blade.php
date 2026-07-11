@extends('layouts.sidebar')
@section('title', 'Certificate Leaderboard')

@section('page-content')
<div class="lb">

  <div class="lb-header">
    <div>
      <div class="lb-breadcrumb">Leaderboards</div>
      <h1 class="lb-title">Certificate Progress</h1>
      <p class="lb-sub">Mentorship completion and recognition across departments and levels.</p>
    </div>
    <a href="{{ route('leaderboards.mentors') }}" class="lb-btn lb-btn-secondary">Top Mentors</a>
  </div>

  <div class="lb-grid-2">

    {{-- By Department --}}
    <div class="lb-panel">
      <div class="lb-panel-head">
        <h2 class="lb-panel-title">By Department</h2>
      </div>
      @if($byDept->isNotEmpty())
      <div class="lb-thead lb-cols-3">
        <span class="lb-th lb-col-rank">#</span>
        <span class="lb-th">Department</span>
        <span class="lb-th lb-col-stat">Certificates</span>
      </div>
      @foreach($byDept as $idx => $row)
      <div class="lb-row lb-cols-3">
        <span class="lb-rank {{ $idx === 0 ? 'is-first' : ($idx === 1 ? 'is-second' : ($idx === 2 ? 'is-third' : '')) }}">{{ $idx + 1 }}</span>
        <span class="lb-cell">{{ $row->department }}</span>
        <span class="lb-stat lb-col-stat">{{ $row->total }}</span>
      </div>
      @endforeach
      @else
      <div class="lb-panel-empty">No certificates issued yet</div>
      @endif
    </div>

    {{-- By Level --}}
    <div class="lb-panel">
      <div class="lb-panel-head">
        <h2 class="lb-panel-title">By Student Level</h2>
      </div>
      @if($byLevel->isNotEmpty())
      <div class="lb-thead lb-cols-3">
        <span class="lb-th lb-col-rank">#</span>
        <span class="lb-th">Level</span>
        <span class="lb-th lb-col-stat">Certificates</span>
      </div>
      @foreach($byLevel as $idx => $row)
      <div class="lb-row lb-cols-3">
        <span class="lb-rank {{ $idx === 0 ? 'is-first' : ($idx === 1 ? 'is-second' : ($idx === 2 ? 'is-third' : '')) }}">{{ $idx + 1 }}</span>
        <span class="lb-cell">{{ $row->level }}</span>
        <span class="lb-stat lb-col-stat">{{ $row->total }}</span>
      </div>
      @endforeach
      @else
      <div class="lb-panel-empty">No certificates issued yet</div>
      @endif
    </div>

  </div>

  {{-- Recent Certificates --}}
  <div class="lb-panel" style="margin-top:20px">
    <div class="lb-panel-head">
      <h2 class="lb-panel-title">Recently Completed</h2>
    </div>
    @if($recent->isNotEmpty())
    @foreach($recent as $cert)
    <div class="lb-cert-row">
      <div class="lb-mentor">
        @if($cert->user->avatar_url)
        <img src="{{ $cert->user->avatar_url }}" alt="" class="lb-avatar-img">
        @else
        <div class="lb-avatar">{{ $cert->user->initials }}</div>
        @endif
        <div class="lb-mentor-id">
          <div class="lb-name">{{ $cert->user->full_name }}</div>
          <div class="lb-mentor-sub">{{ $cert->user->level }}@if($cert->user->department) · {{ $cert->user->department }}@endif</div>
        </div>
      </div>
      <div class="lb-cert-meta">
        <span class="lb-cert-date">{{ $cert->issued_at?->format('M j, Y') ?? 'Recently' }}</span>
        <span class="lb-cert-type">{{ ucfirst($cert->type ?? 'mentee') }}</span>
      </div>
    </div>
    @endforeach
    @else
    <div class="lb-panel-empty">No certificates issued yet</div>
    @endif
  </div>

</div>
@endsection

@push('styles')
<style>
/* ============================================================
   Certificate leaderboard — corporate restyle, page-scoped .lb
   ============================================================ */
.lb { --b-navy:#1e3a8a; --b-ink:#0f172a; --b-ink-2:#475569; --b-ink-3:#64748b;
      --b-line:#e2e8f0; --b-panel:#ffffff; --b-panel-2:#f8fafc; --b-gold:#b45309; --b-teal:#0f766e;
      max-width:1000px; margin:0 auto; font-family:'Inter',sans-serif; }
[data-theme="dark"] .lb { --b-ink:#f1f5f9; --b-ink-2:#cbd5e1; --b-ink-3:#94a3b8;
      --b-line:#334155; --b-panel:#1e293b; --b-panel-2:#253044; --b-navy:#3b5bdb; }

.lb-header { display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:16px; margin-bottom:24px; }
.lb-breadcrumb { font-size:0.72rem; font-weight:600; letter-spacing:0.1em; text-transform:uppercase; color:var(--b-ink-3); margin-bottom:6px; }
.lb-title { font-size:1.45rem; font-weight:700; color:var(--b-ink); letter-spacing:-0.01em; margin:0 0 4px; }
.lb-sub { font-size:0.86rem; color:var(--b-ink-3); margin:0; }

.lb-btn { display:inline-flex; align-items:center; justify-content:center; padding:9px 16px; border-radius:6px;
          font-weight:600; font-size:0.84rem; cursor:pointer; border:1px solid transparent; text-decoration:none;
          transition:border-color 0.15s; white-space:nowrap; }
.lb-btn-secondary { background:var(--b-panel); color:var(--b-ink-2); border-color:var(--b-line); }
.lb-btn-secondary:hover { border-color:var(--b-ink-3); color:var(--b-ink); }

.lb-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:20px; }

.lb-panel { background:var(--b-panel); border:1px solid var(--b-line); border-radius:8px; overflow:hidden; }
.lb-panel-head { padding:14px 18px; border-bottom:1px solid var(--b-line); }
.lb-panel-title { font-size:0.92rem; font-weight:600; color:var(--b-ink); margin:0; }
.lb-panel-empty { padding:32px 18px; text-align:center; font-size:0.84rem; color:var(--b-ink-3); }

.lb-thead, .lb-row { display:grid; align-items:center; padding:11px 18px; gap:12px; }
.lb-cols-3 { grid-template-columns:40px 1fr 100px; }
.lb-thead { border-bottom:1px solid var(--b-line); background:var(--b-panel-2); padding-top:9px; padding-bottom:9px; }
.lb-th { font-size:0.68rem; font-weight:600; text-transform:uppercase; letter-spacing:0.08em; color:var(--b-ink-3); }
.lb-row { border-bottom:1px solid var(--b-line); }
.lb-row:last-child { border-bottom:none; }
.lb-col-rank { text-align:center; }
.lb-col-stat { text-align:right; }
.lb-cell { font-size:0.86rem; font-weight:500; color:var(--b-ink); }
.lb-stat { font-weight:700; font-size:0.9rem; color:var(--b-ink); }

.lb-rank { font-weight:700; font-size:0.88rem; color:var(--b-ink-3); text-align:center; }
.lb-rank.is-first  { color:var(--b-gold); }
.lb-rank.is-second { color:#6b7280; }
.lb-rank.is-third  { color:#92613a; }

.lb-mentor { display:flex; align-items:center; gap:12px; min-width:0; }
.lb-avatar { width:36px; height:36px; border-radius:6px; background:var(--b-panel-2); border:1px solid var(--b-line);
             color:var(--b-ink-2); font-weight:600; font-size:0.68rem;
             display:inline-flex; align-items:center; justify-content:center; flex-shrink:0; }
.lb-avatar-img { width:36px; height:36px; border-radius:6px; object-fit:cover; flex-shrink:0; }
.lb-mentor-id { min-width:0; }
.lb-name { font-weight:600; font-size:0.86rem; color:var(--b-ink); }
.lb-mentor-sub { font-size:0.74rem; color:var(--b-ink-3); margin-top:1px; }

.lb-cert-row { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:11px 18px; border-bottom:1px solid var(--b-line); }
.lb-cert-row:last-child { border-bottom:none; }
.lb-cert-meta { display:flex; align-items:center; gap:12px; flex-shrink:0; }
.lb-cert-date { font-size:0.78rem; color:var(--b-ink-3); }
.lb-cert-type { font-size:0.68rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;
                color:var(--b-teal); border:1px solid var(--b-teal); border-radius:4px; padding:2px 8px; }

@media (max-width:768px) { .lb-grid-2 { grid-template-columns:1fr; } }
</style>
@endpush
