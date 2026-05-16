@extends('layouts.sidebar')
@section('title', 'Edit Learning Path')

@section('page-content')
<div style="max-width:760px">

<div style="display:flex;align-items:center;gap:12px;margin-bottom:24px">
  <a href="{{ route('learning.index') }}" style="color:var(--text-3);text-decoration:none;font-size:0.85rem">Learning Paths</a>
  <span style="color:var(--text-3)">/</span>
  <span style="font-size:0.85rem;font-weight:600">Edit</span>
</div>

<h1 class="section-title" style="margin-bottom:4px">Edit Learning Path</h1>
<p class="section-sub" style="margin-bottom:28px">Mentee: <strong>{{ $path->mentee->full_name }}</strong></p>

@if($errors->any())
<div class="alert alert-danger" style="margin-bottom:20px">
  <ul style="margin:0;padding-left:16px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

@if($hasSubmissions)
<div style="background:linear-gradient(135deg,#f97316,#ef4444);border-radius:12px;padding:14px 18px;margin-bottom:20px;color:#fff;font-size:0.85rem">
  <strong>Note:</strong> This path has task submissions — modules and tasks are locked to prevent data loss. You can still update the title, description, and due date.
</div>
@endif

<form method="POST" action="{{ route('learning.update', $path) }}">
@csrf @method('PUT')

{{-- Path details --}}
<div style="background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:24px;margin-bottom:20px">
  <div style="font-weight:700;font-size:0.95rem;margin-bottom:16px">Path Details</div>

  <div class="form-group">
    <label class="form-label">Title</label>
    <input type="text" name="title" class="form-input" value="{{ old('title', $path->title) }}" required>
  </div>

  <div class="form-group">
    <label class="form-label">Description <span style="color:var(--text-3);font-weight:400">(optional)</span></label>
    <textarea name="description" class="form-input" rows="3">{{ old('description', $path->description) }}</textarea>
  </div>

  <div class="form-group" style="margin-bottom:0">
    <label class="form-label">Due Date <span style="color:var(--text-3);font-weight:400">(optional)</span></label>
    <input type="date" name="due_date" class="form-input" value="{{ old('due_date', $path->due_date?->format('Y-m-d')) }}" style="max-width:220px">
  </div>
</div>

{{-- Modules & Tasks (only editable if no submissions) --}}
@if(!$hasSubmissions)
<div style="background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:24px;margin-bottom:20px">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
    <div style="font-weight:700;font-size:0.95rem">Modules &amp; Tasks</div>
    <button type="button" onclick="addModule()" class="btn btn-outline btn-sm">+ Add Module</button>
  </div>
  <div id="modules-container"></div>
  <div id="no-modules-msg" style="display:none;text-align:center;padding:32px;color:var(--text-3);font-size:0.85rem;border:2px dashed var(--border);border-radius:12px">
    Click <strong>+ Add Module</strong> to get started.
  </div>
</div>
@else
{{-- Read-only module list --}}
<div style="background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:24px;margin-bottom:20px;opacity:0.7">
  <div style="font-weight:700;font-size:0.95rem;margin-bottom:16px">Modules &amp; Tasks (locked)</div>
  @foreach($path->modules as $module)
  <div style="margin-bottom:12px">
    <div style="font-weight:600;font-size:0.88rem;margin-bottom:6px;color:var(--text-2)">{{ $module->title }}</div>
    @foreach($module->tasks as $task)
    <div style="font-size:0.82rem;color:var(--text-3);padding:4px 0 4px 12px;border-left:2px solid var(--border)">{{ $task->title }}</div>
    @endforeach
  </div>
  @endforeach
</div>
@endif

<div style="display:flex;gap:12px;justify-content:flex-end">
  <a href="{{ route('learning.index') }}" class="btn btn-outline">Cancel</a>
  <button type="submit" class="btn btn-primary">Save Changes</button>
</div>

</form>
</div>

@if(!$hasSubmissions)
<script>
let moduleCount = 0;
let taskCounters = {};

function addModule(title) {
  document.getElementById('no-modules-msg').style.display = 'none';
  const idx = moduleCount++;
  const div = document.createElement('div');
  div.id = 'module-' + idx;
  div.style.cssText = 'background:var(--surface-2);border:1px solid var(--border);border-radius:12px;padding:16px;margin-bottom:12px';
  div.innerHTML = `
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
      <input type="text" name="modules[${idx}][title]" placeholder="Module title" value="${title||''}" required
             style="flex:1;background:var(--surface);border:1px solid var(--border);border-radius:8px;padding:8px 12px;font-size:0.88rem;color:var(--text-1)">
      <button type="button" onclick="removeModule(${idx})" style="background:none;border:none;cursor:pointer;color:var(--text-3);font-size:1.2rem;padding:4px"></button>
    </div>
    <div id="tasks-${idx}" style="margin-bottom:10px"></div>
    <button type="button" onclick="addTask(${idx})" class="btn btn-outline btn-sm" style="font-size:0.78rem">+ Add Task</button>
  `;
  document.getElementById('modules-container').appendChild(div);
  return idx;
}

function removeModule(idx) {
  const el = document.getElementById('module-' + idx);
  if (el) el.remove();
  if (!document.getElementById('modules-container').children.length) {
    document.getElementById('no-modules-msg').style.display = '';
  }
}

function addTask(moduleIdx, task) {
  if (!taskCounters[moduleIdx]) taskCounters[moduleIdx] = 0;
  const tIdx = taskCounters[moduleIdx]++;
  const container = document.getElementById('tasks-' + moduleIdx);
  const div = document.createElement('div');
  div.id = `task-${moduleIdx}-${tIdx}`;
  div.style.cssText = 'background:var(--surface);border:1px solid var(--border);border-radius:8px;padding:12px;margin-bottom:8px';
  div.innerHTML = `
    <div style="display:flex;align-items:flex-start;gap:8px;margin-bottom:8px">
      <input type="text" name="modules[${moduleIdx}][tasks][${tIdx}][title]" placeholder="Task title" value="${task?.title||''}" required
             style="flex:1;background:var(--surface-2);border:1px solid var(--border);border-radius:6px;padding:7px 10px;font-size:0.85rem;color:var(--text-1)">
      <button type="button" onclick="removeTask(${moduleIdx},${tIdx})" style="background:none;border:none;cursor:pointer;color:var(--text-3);font-size:1.1rem;padding:4px;flex-shrink:0"></button>
    </div>
    <textarea name="modules[${moduleIdx}][tasks][${tIdx}][description]" placeholder="Description (optional)" rows="2"
              style="width:100%;background:var(--surface-2);border:1px solid var(--border);border-radius:6px;padding:7px 10px;font-size:0.82rem;color:var(--text-1);resize:vertical;margin-bottom:8px">${task?.description||''}</textarea>
    <div style="display:flex;align-items:center;gap:16px">
      <div style="display:flex;align-items:center;gap:8px">
        <label style="font-size:0.78rem;color:var(--text-3)">Max score</label>
        <input type="number" name="modules[${moduleIdx}][tasks][${tIdx}][max_score]" value="${task?.max_score||100}" min="1" max="1000"
               style="width:72px;background:var(--surface-2);border:1px solid var(--border);border-radius:6px;padding:5px 8px;font-size:0.82rem;color:var(--text-1)">
      </div>
      <label style="display:flex;align-items:center;gap:6px;font-size:0.78rem;color:var(--text-3);cursor:pointer">
        <input type="checkbox" name="modules[${moduleIdx}][tasks][${tIdx}][is_locked]" value="1" ${task?.is_locked?'checked':''}> Locked initially
      </label>
    </div>
  `;
  container.appendChild(div);
}

function removeTask(moduleIdx, tIdx) {
  const el = document.getElementById(`task-${moduleIdx}-${tIdx}`);
  if (el) el.remove();
}

// Pre-populate with existing modules and tasks
@php
$existingModules = $path->modules->map(fn($m) => [
    'title' => $m->title,
    'tasks' => $m->tasks->map(fn($t) => [
        'title'       => $t->title,
        'description' => $t->description,
        'max_score'   => $t->max_score,
        'is_locked'   => $t->is_locked,
    ])->values()->all(),
])->values()->all();
@endphp
const existing = @json($existingModules);

if (existing.length === 0) {
  document.getElementById('no-modules-msg').style.display = '';
} else {
  existing.forEach(m => {
    const mIdx = addModule(m.title);
    (m.tasks || []).forEach(t => addTask(mIdx, t));
  });
}
</script>
@endif
@endsection
