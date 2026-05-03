@extends('layouts.sidebar')
@section('title', 'Learning Paths')

@section('page-content')
<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px">
  <div>
    <h1 class="section-title">Learning Paths</h1>
    <p class="section-sub">Manage the learning programs you have created for your mentees.</p>
  </div>
  <a href="{{ route('learning.create') }}" class="btn btn-primary">+ New Path</a>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:20px">{{ session('success') }}</div>
@endif

<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px">
  @forelse($paths as $path)
  @php
    $allTasks    = $path->modules->flatMap->tasks;
    $menteeSubm  = $allTasks->flatMap->submissions->where('user_id', $path->mentee_id);
    $total       = $allTasks->count();
    $submitted   = $menteeSubm->where('status', 'submitted')->count();
    $graded      = $menteeSubm->where('status', 'graded')->count();
    $progress    = $total > 0 ? (int) round(($graded / $total) * 100) : 0;
  @endphp
  <div style="background:var(--surface);border:1px solid var(--border);border-radius:20px;overflow:hidden;transition:transform 0.25s" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform=''">
    <div style="height:80px;background:linear-gradient(135deg,var(--blue-700),var(--blue-500));position:relative;display:flex;align-items:flex-end;padding:12px 16px">
      <span style="position:absolute;top:14px;left:16px;font-size:2rem">{{ $progress == 100 ? '✅' : '🗺️' }}</span>
      <span class="badge {{ $path->status === 'completed' ? 'badge-green' : 'badge-blue' }}" style="margin-left:auto">
        {{ $path->status === 'completed' ? 'Completed' : 'Active' }}
      </span>
    </div>
    <div style="padding:16px">
      <div style="font-weight:800;font-size:1rem;margin-bottom:6px">{{ $path->title }}</div>
      <div style="font-size:0.78rem;color:var(--text-3);margin-bottom:12px;display:flex;align-items:center;gap:6px">
        <div class="avatar avatar-sm">{{ $path->mentee->initials }}</div>
        {{ $path->mentee->full_name }}
      </div>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:6px;margin-bottom:10px">
        <div style="background:var(--surface-2);border-radius:8px;padding:6px;text-align:center">
          <div style="font-family:'Sora',sans-serif;font-size:1.1rem;font-weight:800;color:var(--text-1)">{{ $total }}</div>
          <div style="font-size:0.68rem;color:var(--text-3);text-transform:uppercase;letter-spacing:0.05em">Tasks</div>
        </div>
        <div style="background:var(--surface-2);border-radius:8px;padding:6px;text-align:center">
          <div style="font-family:'Sora',sans-serif;font-size:1.1rem;font-weight:800;color:var(--blue-500)">{{ $submitted }}</div>
          <div style="font-size:0.68rem;color:var(--text-3);text-transform:uppercase;letter-spacing:0.05em">Pending</div>
        </div>
        <div style="background:var(--surface-2);border-radius:8px;padding:6px;text-align:center">
          <div style="font-family:'Sora',sans-serif;font-size:1.1rem;font-weight:800;color:var(--success)">{{ $graded }}</div>
          <div style="font-size:0.68rem;color:var(--text-3);text-transform:uppercase;letter-spacing:0.05em">Graded</div>
        </div>
      </div>
      <div class="progress-bar"><div class="progress-fill {{ $progress==100 ? 'green' : '' }}" style="width:{{ $progress }}%"></div></div>
      <div style="font-size:0.78rem;color:var(--text-3);margin-top:4px;text-align:right">{{ $progress }}% complete</div>
    </div>
    <div style="padding:12px 16px;border-top:1px solid var(--border);display:flex;gap:8px;justify-content:flex-end">
      @if($submitted > 0)
      <a href="{{ route('learning.grade', $path) }}" class="btn btn-primary btn-sm">Grade ({{ $submitted }})</a>
      @else
      <a href="{{ route('learning.grade', $path) }}" class="btn btn-outline btn-sm">View</a>
      @endif
      <a href="{{ route('learning.edit', $path) }}" class="btn btn-outline btn-sm">Edit</a>
      <form method="POST" action="{{ route('learning.destroy', $path) }}" data-confirm="Delete this learning path? This cannot be undone.">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-sm" style="background:var(--surface-2);color:var(--danger);border:1px solid var(--danger)">Delete</button>
      </form>
    </div>
  </div>
  @empty
  <div style="grid-column:span 3;text-align:center;padding:60px;color:var(--text-3)">
    <div style="font-size:3rem;margin-bottom:16px">🗺️</div>
    <p style="margin-bottom:16px">You haven't created any learning paths yet.</p>
    <a href="{{ route('learning.create') }}" class="btn btn-primary">Create your first path</a>
  </div>
  @endforelse
</div>
@endsection
