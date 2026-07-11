@extends('layouts.sidebar')
@section('title', 'Learning Paths')

@section('page-content')
<div class="lp">
  <div class="lp-header">
    <div>
      <div class="lp-breadcrumb">Mentoring</div>
      <h1 class="lp-title">Learning Paths</h1>
      <p class="lp-sub">Manage the learning programs you have created for your mentees.</p>
    </div>
    <a href="{{ route('learning.create') }}" class="lp-btn lp-btn-primary">New Path</a>
  </div>

  @if(session('success'))
  <div class="alert alert-success" style="margin-bottom:20px">{{ session('success') }}</div>
  @endif

  <div class="lp-grid">
    @forelse($paths as $path)
    @php
      $allTasks    = $path->modules->flatMap->tasks;
      $menteeSubm  = $allTasks->flatMap->submissions->where('user_id', $path->mentee_id);
      $total       = $allTasks->count();
      $submitted   = $menteeSubm->where('status', 'submitted')->count();
      $graded      = $menteeSubm->where('status', 'graded')->count();
      $progress    = $total > 0 ? (int) round(($graded / $total) * 100) : 0;
      $isComplete  = $path->status === 'completed' || $progress === 100;
    @endphp
    <div class="lp-card">
      <div class="lp-card-head">
        <div class="lp-card-title">{{ $path->title }}</div>
        <span class="lp-status {{ $isComplete ? 'is-complete' : '' }}">{{ $isComplete ? 'Completed' : 'Active' }}</span>
      </div>

      <div class="lp-mentee">
        @if($path->mentee->avatar_url)
        <img src="{{ $path->mentee->avatar_url }}" alt="" class="lp-mentee-img">
        @else
        <div class="lp-mentee-avatar">{{ $path->mentee->initials }}</div>
        @endif
        <span>{{ $path->mentee->full_name }}</span>
      </div>

      <div class="lp-stats">
        <div class="lp-stat">
          <div class="lp-stat-value">{{ $total }}</div>
          <div class="lp-stat-label">Tasks</div>
        </div>
        <div class="lp-stat">
          <div class="lp-stat-value {{ $submitted ? 'is-pending' : '' }}">{{ $submitted }}</div>
          <div class="lp-stat-label">Pending</div>
        </div>
        <div class="lp-stat">
          <div class="lp-stat-value {{ $graded ? 'is-graded' : '' }}">{{ $graded }}</div>
          <div class="lp-stat-label">Graded</div>
        </div>
      </div>

      <div class="lp-progress-row">
        <div class="lp-progress"><div class="lp-progress-fill {{ $isComplete ? 'is-complete' : '' }}" style="width:{{ $progress }}%"></div></div>
        <span class="lp-progress-pct">{{ $progress }}%</span>
      </div>

      <div class="lp-card-actions">
        @if($submitted > 0)
        <a href="{{ route('learning.grade', $path) }}" class="lp-btn lp-btn-primary lp-btn-sm">Grade ({{ $submitted }})</a>
        @else
        <a href="{{ route('learning.grade', $path) }}" class="lp-btn lp-btn-secondary lp-btn-sm">View</a>
        @endif
        <a href="{{ route('learning.edit', $path) }}" class="lp-btn lp-btn-secondary lp-btn-sm">Edit</a>
        <form method="POST" action="{{ route('learning.destroy', $path) }}" data-confirm="Delete this learning path? This cannot be undone." style="margin-left:auto">
          @csrf @method('DELETE')
          <button type="submit" class="lp-btn lp-btn-danger lp-btn-sm">Delete</button>
        </form>
      </div>
    </div>
    @empty
    <div class="lp-empty">
      <div class="lp-empty-title">No learning paths yet</div>
      <p class="lp-empty-sub">Create a structured program of modules and tasks for one of your mentees.</p>
      <a href="{{ route('learning.create') }}" class="lp-btn lp-btn-primary">Create your first path</a>
    </div>
    @endforelse
  </div>
</div>
@endsection

@push('styles')
<style>
/* ============================================================
   Learning Paths (mentor) — corporate restyle, page-scoped .lp
   ============================================================ */
.lp { --l-navy:#1e3a8a; --l-navy-dark:#172554; --l-ink:#0f172a; --l-ink-2:#475569; --l-ink-3:#64748b;
      --l-line:#e2e8f0; --l-panel:#ffffff; --l-panel-2:#f8fafc; --l-teal:#0f766e; --l-amber:#b45309; --l-red:#b91c1c;
      font-family:'Inter',sans-serif; }
[data-theme="dark"] .lp { --l-ink:#f1f5f9; --l-ink-2:#cbd5e1; --l-ink-3:#94a3b8;
      --l-line:#334155; --l-panel:#1e293b; --l-panel-2:#253044; --l-navy:#3b5bdb; }

/* ---- Header ---- */
.lp-header { display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:16px; margin-bottom:24px; }
.lp-breadcrumb { font-size:0.72rem; font-weight:600; letter-spacing:0.1em; text-transform:uppercase; color:var(--l-ink-3); margin-bottom:6px; }
.lp-title { font-size:1.45rem; font-weight:700; color:var(--l-ink); letter-spacing:-0.01em; margin:0 0 4px; }
.lp-sub { font-size:0.86rem; color:var(--l-ink-3); margin:0; }

/* ---- Buttons ---- */
.lp-btn { display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:9px 16px;
          border-radius:6px; font-family:'Inter',sans-serif; font-weight:600; font-size:0.84rem;
          cursor:pointer; border:1px solid transparent; text-decoration:none; transition:background 0.15s, border-color 0.15s; }
.lp-btn-primary { background:var(--l-navy); color:#fff; }
.lp-btn-primary:hover { background:var(--l-navy-dark); }
.lp-btn-secondary { background:var(--l-panel); color:var(--l-ink-2); border-color:var(--l-line); }
.lp-btn-secondary:hover { border-color:var(--l-ink-3); color:var(--l-ink); }
.lp-btn-danger { background:transparent; color:var(--l-red); border-color:var(--l-line); }
.lp-btn-danger:hover { border-color:var(--l-red); }
.lp-btn-sm { padding:6px 12px; font-size:0.78rem; }

/* ---- Grid & cards ---- */
.lp-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:20px; }
.lp-card { background:var(--l-panel); border:1px solid var(--l-line); border-radius:8px; padding:20px; display:flex; flex-direction:column; }
.lp-card-head { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; margin-bottom:12px; }
.lp-card-title { font-weight:600; font-size:0.95rem; color:var(--l-ink); line-height:1.35; }
.lp-status { font-size:0.7rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;
             color:var(--l-ink-2); background:var(--l-panel-2); border:1px solid var(--l-line);
             border-radius:4px; padding:3px 8px; flex-shrink:0; }
.lp-status.is-complete { color:var(--l-teal); border-color:var(--l-teal); background:transparent; }

/* ---- Mentee line ---- */
.lp-mentee { display:flex; align-items:center; gap:8px; font-size:0.8rem; color:var(--l-ink-2); margin-bottom:16px; }
.lp-mentee-avatar { width:26px; height:26px; border-radius:5px; background:var(--l-panel-2); border:1px solid var(--l-line);
                    color:var(--l-ink-2); font-weight:600; font-size:0.62rem;
                    display:inline-flex; align-items:center; justify-content:center; flex-shrink:0; }
.lp-mentee-img { width:26px; height:26px; border-radius:5px; object-fit:cover; flex-shrink:0; }

/* ---- Stats ---- */
.lp-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:14px; }
.lp-stat { border:1px solid var(--l-line); border-radius:6px; padding:8px 6px; text-align:center; background:var(--l-panel-2); }
.lp-stat-value { font-size:1.05rem; font-weight:700; color:var(--l-ink); line-height:1.2; }
.lp-stat-value.is-pending { color:var(--l-amber); }
.lp-stat-value.is-graded { color:var(--l-teal); }
.lp-stat-label { font-size:0.64rem; font-weight:600; color:var(--l-ink-3); text-transform:uppercase; letter-spacing:0.06em; margin-top:2px; }

/* ---- Progress ---- */
.lp-progress-row { display:flex; align-items:center; gap:10px; margin-bottom:16px; }
.lp-progress { flex:1; height:5px; background:var(--l-panel-2); border-radius:3px; overflow:hidden; }
.lp-progress-fill { height:100%; background:var(--l-navy); border-radius:3px; transition:width 0.5s ease; }
.lp-progress-fill.is-complete { background:var(--l-teal); }
.lp-progress-pct { font-size:0.75rem; font-weight:600; color:var(--l-ink-2); flex-shrink:0; }

/* ---- Actions ---- */
.lp-card-actions { display:flex; gap:8px; align-items:center; border-top:1px solid var(--l-line); padding-top:14px; margin-top:auto; }

/* ---- Empty state ---- */
.lp-empty { grid-column:1 / -1; text-align:center; padding:56px 20px; background:var(--l-panel); border:1px solid var(--l-line); border-radius:8px; }
.lp-empty-title { font-weight:600; font-size:0.95rem; color:var(--l-ink); margin-bottom:6px; }
.lp-empty-sub { font-size:0.82rem; color:var(--l-ink-3); margin:0 0 18px; }
</style>
@endpush
