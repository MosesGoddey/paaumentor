@extends('layouts.sidebar')
@section('title', 'Grade Submissions')

@section('page-content')

<div style="display:flex;align-items:center;gap:12px;margin-bottom:24px">
  <a href="{{ route('learning.index') }}" style="color:var(--text-3);text-decoration:none;font-size:0.85rem">Learning Paths</a>
  <span style="color:var(--text-3)">/</span>
  <span style="font-size:0.85rem;font-weight:600">Grade Submissions</span>
</div>

<div style="display:flex;align-items:flex-start;gap:16px;margin-bottom:28px">
  <div style="width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,var(--blue-700),var(--blue-500));display:flex;align-items:center;justify-content:center;font-size:1.6rem;flex-shrink:0">🗺️</div>
  <div>
    <h1 style="font-size:1.3rem;font-weight:800;margin-bottom:4px">{{ $path->title }}</h1>
    <div style="font-size:0.82rem;color:var(--text-3);display:flex;align-items:center;gap:8px">
      <div class="avatar avatar-sm">{{ $path->mentee->initials }}</div>
      {{ $path->mentee->full_name }}
      @if($path->due_date)<span>&bull; Due {{ $path->due_date->format('M d, Y') }}</span>@endif
    </div>
  </div>
  <a href="{{ route('learning.edit', $path) }}" class="btn btn-outline btn-sm" style="margin-left:auto">Edit Path</a>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:20px">{{ session('success') }}</div>
@endif

@forelse($path->modules as $module)
<div style="margin-bottom:28px">
  <div style="font-size:0.78rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:var(--text-3);padding:10px 0 8px;border-bottom:1px solid var(--border);margin-bottom:10px">
    {{ $module->title }}
  </div>

  @foreach($module->tasks as $task)
  @php $sub = $task->submissions->first(); @endphp
  <div style="background:var(--surface);border:1.5px solid {{ $sub && $sub->status==='graded' ? 'var(--success)' : ($sub && $sub->status==='submitted' ? 'var(--blue-400)' : 'var(--border)') }};border-radius:14px;padding:18px;margin-bottom:10px">

    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:10px">
      <div>
        <div style="font-weight:700;font-size:0.92rem;margin-bottom:2px">{{ $task->title }}</div>
        @if($task->description)<div style="font-size:0.78rem;color:var(--text-3)">{{ $task->description }}</div>@endif
      </div>
      <div style="display:flex;align-items:center;gap:8px;flex-shrink:0">
        <span style="font-size:0.75rem;color:var(--text-3)">Max: {{ $task->max_score }}</span>
        @if(!$sub)
          <span class="badge badge-gray">No submission</span>
        @elseif($sub->status === 'graded')
          <span class="badge badge-green">Graded &mdash; {{ $sub->score }}/{{ $task->max_score }}</span>
        @elseif($sub->status === 'rejected')
          <span class="badge badge-red">Returned</span>
        @else
          <span class="badge badge-blue">Awaiting grade</span>
        @endif
      </div>
    </div>

    @if($sub)
    {{-- Submission content --}}
    @if($sub->notes)
    <div style="background:var(--surface-2);border-radius:8px;padding:10px 12px;font-size:0.82rem;color:var(--text-2);margin-bottom:10px;border-left:3px solid var(--blue-400)">
      <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-3);margin-bottom:4px">Mentee Notes</div>
      {{ $sub->notes }}
    </div>
    @endif

    @if($sub->file_path)
    <div style="margin-bottom:10px">
      <a href="{{ asset('storage/' . $sub->file_path) }}" target="_blank" class="btn btn-outline btn-sm">
        Download Attachment
      </a>
    </div>
    @endif

    {{-- Existing feedback --}}
    @if($sub->feedback)
    <div style="background:var(--surface-2);border-radius:8px;padding:10px 12px;font-size:0.82rem;color:var(--text-2);margin-bottom:10px;border-left:3px solid var(--success)">
      <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-3);margin-bottom:4px">Your Previous Feedback</div>
      {{ $sub->feedback }}
    </div>
    @endif

    {{-- Grade form --}}
    <form method="POST" action="{{ route('learning.grade-submission', $sub) }}" style="border-top:1px solid var(--border);padding-top:12px;margin-top:4px">
      @csrf
      <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
        <div>
          <label style="font-size:0.75rem;font-weight:600;color:var(--text-3);display:block;margin-bottom:4px">Score (out of {{ $task->max_score }})</label>
          <input type="number" name="score" value="{{ $sub->score }}" min="0" max="{{ $task->max_score }}"
                 style="width:90px;background:var(--surface-2);border:1px solid var(--border);border-radius:8px;padding:7px 10px;font-size:0.88rem;color:var(--text-1)">
        </div>
        <div style="flex:1;min-width:200px">
          <label style="font-size:0.75rem;font-weight:600;color:var(--text-3);display:block;margin-bottom:4px">Feedback (optional)</label>
          <input type="text" name="feedback" value="{{ $sub->feedback }}" placeholder="Leave feedback for the mentee..."
                 style="width:100%;background:var(--surface-2);border:1px solid var(--border);border-radius:8px;padding:7px 10px;font-size:0.88rem;color:var(--text-1)">
        </div>
        <div style="display:flex;gap:8px">
          <button type="submit" name="status" value="graded" class="btn btn-primary btn-sm">Mark Graded</button>
          <button type="submit" name="status" value="rejected" class="btn btn-sm" style="background:var(--surface-2);color:var(--danger);border:1px solid var(--danger)">Return</button>
        </div>
      </div>
    </form>
    @else
    <div style="font-size:0.82rem;color:var(--text-3);font-style:italic">The mentee has not submitted this task yet.</div>
    @endif

  </div>
  @endforeach
</div>
@empty
<div style="text-align:center;padding:60px;color:var(--text-3)">
  <p>No modules found. <a href="{{ route('learning.edit', $path) }}">Add modules</a> to this path.</p>
</div>
@endforelse

@endsection
