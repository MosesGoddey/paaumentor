@extends('layouts.sidebar')
@section('title', 'Learning Paths')

@section('page-content')
<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px">
  <div>
    <h1 class="section-title">My Learning Paths</h1>
    <p class="section-sub">Track your mentorship programs and complete tasks to earn certificates.</p>
  </div>
</div>

<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px">
  @forelse($paths as $lp)
  @php $path = $lp['path']; $progress = $lp['progress']; @endphp
  <div style="background:var(--surface);border:1px solid var(--border);border-radius:20px;overflow:hidden;transition:transform 0.25s" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform=''">
    <div style="height:80px;background:linear-gradient(135deg,var(--blue-700),var(--blue-500));position:relative;display:flex;align-items:flex-end;padding:12px 16px">
      <span style="position:absolute;top:16px;left:16px;font-size:2rem">{{ $progress == 100 ? '✅' : '🗺️' }}</span>
      <span class="badge {{ $progress == 100 ? 'badge-green' : 'badge-blue' }}" style="margin-left:auto">{{ $progress == 100 ? '✓ Completed' : 'In Progress' }}</span>
    </div>
    <div style="padding:16px">
      <div style="font-weight:800;font-size:1rem;margin-bottom:4px">{{ $path->title }}</div>
      <div style="font-size:0.78rem;color:var(--text-3);margin-bottom:10px;display:flex;align-items:center;gap:6px">
        <div class="avatar avatar-sm">{{ $path->mentor->initials }}</div> {{ $path->mentor->full_name }}
      </div>
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
        <span style="font-size:0.82rem;color:var(--text-3)">{{ $path->modules->sum(fn($m) => $m->tasks->count()) }} tasks</span>
        <span style="font-family:'Sora',sans-serif;font-weight:800;font-size:0.9rem;color:{{ $progress==100 ? 'var(--success)' : 'var(--blue-500)' }}">{{ $progress }}%</span>
      </div>
      <div class="progress-bar"><div class="progress-fill {{ $progress==100 ? 'green' : '' }}" style="width:{{ $progress }}%"></div></div>
    </div>
    <div style="padding:12px 16px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">
      <span style="font-size:0.78rem;color:var(--text-3)">{{ $path->due_date ? '📅 Due '.$path->due_date->format('M d') : '📅 No deadline' }}</span>
      <a href="{{ route('learning.show', $path) }}" class="btn btn-primary btn-sm">{{ $progress == 100 ? 'View' : 'Continue →' }}</a>
    </div>
  </div>
  @empty
  <div style="grid-column:span 3;text-align:center;padding:60px;color:var(--text-3)">
    <div style="font-size:3rem;margin-bottom:16px">🗺️</div>
    <p>No learning paths yet. Request a mentor and ask them to create a learning path for you!</p>
    <a href="{{ route('mentors.index') }}" class="btn btn-primary" style="margin-top:16px">Find a Mentor</a>
  </div>
  @endforelse
</div>
@endsection
