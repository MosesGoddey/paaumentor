@extends('layouts.sidebar')
@section('title', 'Create Learning Path')

@section('page-content')
<div style="max-width:760px">

<div style="display:flex;align-items:center;gap:12px;margin-bottom:24px">
  <a href="{{ route('learning.index') }}" style="color:var(--text-3);text-decoration:none;font-size:0.85rem">Learning Paths</a>
  <span style="color:var(--text-3)">/</span>
  <span style="font-size:0.85rem;font-weight:600">New Path</span>
</div>

<h1 class="section-title" style="margin-bottom:4px">Create Learning Path</h1>
<p class="section-sub" style="margin-bottom:28px">Define the modules and tasks your mentee will complete to earn a certificate.</p>

@if($errors->any())
<div class="alert alert-danger" style="margin-bottom:20px">
  <ul style="margin:0;padding-left:16px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('learning.store') }}">
@csrf

{{-- Path details --}}
<div style="background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:24px;margin-bottom:20px">
  <div style="font-weight:700;font-size:0.95rem;margin-bottom:16px">Path Details</div>

  <div class="form-group">
    <label class="form-label">Mentee</label>
    <select name="mentee_id" class="form-input" required>
      <option value="">Select a mentee...</option>
      @foreach($mentees as $mentee)
      <option value="{{ $mentee->id }}" {{ old('mentee_id') == $mentee->id ? 'selected' : '' }}>
        {{ $mentee->full_name }} &mdash; {{ $mentee->department }}
      </option>
      @endforeach
    </select>
  </div>

  <div class="form-group">
    <label class="form-label">Title</label>
    <input type="text" name="title" class="form-input" placeholder="e.g. Introduction to Web Development" value="{{ old('title') }}" required>
  </div>

  <div class="form-group">
    <label class="form-label">Description <span style="color:var(--text-3);font-weight:400">(optional)</span></label>
    <textarea name="description" class="form-input" rows="3" placeholder="What will the mentee learn?">{{ old('description') }}</textarea>
  </div>

  <div class="form-group" style="margin-bottom:0">
    <label class="form-label">Due Date <span style="color:var(--text-3);font-weight:400">(optional)</span></label>
    <input type="date" name="due_date" class="form-input" value="{{ old('due_date') }}" style="max-width:220px">
  </div>
</div>

{{-- Modules & Tasks --}}
<div style="background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:24px;margin-bottom:20px">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
    <div style="font-weight:700;font-size:0.95rem">Modules &amp; Tasks</div>
    <div style="display:flex;gap:8px">
      <button type="button" onclick="toggleAiPanel()" class="btn btn-outline btn-sm" style="border-color:var(--blue-500);color:var(--blue-500);font-size:0.8rem">✨ Generate with AI</button>
      <button type="button" onclick="addModule()" class="btn btn-outline btn-sm">+ Add Module</button>
    </div>
  </div>

  <div id="ai-panel" style="display:none;background:var(--surface-2);border:1.5px solid var(--blue-500);border-radius:12px;padding:16px;margin-bottom:16px">
    <div style="font-size:0.85rem;font-weight:700;color:var(--blue-500);margin-bottom:12px">✨ AI Learning Path Generator</div>
    <div style="display:grid;grid-template-columns:1fr 140px 80px;gap:10px;align-items:end">
      <div>
        <label style="font-size:0.78rem;font-weight:600;color:var(--text-3);display:block;margin-bottom:4px">Topic / Subject</label>
        <input type="text" id="ai-topic" class="form-input" placeholder="e.g. Python, Data Structures..." style="margin-bottom:0">
      </div>
      <div>
        <label style="font-size:0.78rem;font-weight:600;color:var(--text-3);display:block;margin-bottom:4px">Level</label>
        <select id="ai-level" class="form-input" style="margin-bottom:0">
          <option value="Beginner">Beginner</option>
          <option value="Intermediate" selected>Intermediate</option>
          <option value="Advanced">Advanced</option>
        </select>
      </div>
      <div>
        <label style="font-size:0.78rem;font-weight:600;color:var(--text-3);display:block;margin-bottom:4px">Weeks</label>
        <input type="number" id="ai-weeks" class="form-input" value="4" min="1" max="24" style="margin-bottom:0">
      </div>
    </div>
    <div style="display:flex;gap:8px;margin-top:12px">
      <button type="button" id="ai-generate-btn" onclick="generateWithAI()" class="btn btn-primary btn-sm">Generate</button>
      <button type="button" onclick="document.getElementById('ai-panel').style.display='none'" class="btn btn-outline btn-sm">Cancel</button>
    </div>
  </div>

  <div id="modules-container"></div>

  <div id="no-modules-msg" style="text-align:center;padding:32px;color:var(--text-3);font-size:0.85rem;border:2px dashed var(--border);border-radius:12px">
    Click <strong>+ Add Module</strong> or <strong>✨ Generate with AI</strong> to get started.
  </div>
</div>

<div style="display:flex;gap:12px;justify-content:flex-end">
  <a href="{{ route('learning.index') }}" class="btn btn-outline">Cancel</a>
  <button type="submit" class="btn btn-primary">Create Learning Path</button>
</div>

</form>
</div>

<script>
let moduleCount = 0;

function addModule() {
  document.getElementById('no-modules-msg').style.display = 'none';
  const idx = moduleCount++;
  const div = document.createElement('div');
  div.id = 'module-' + idx;
  div.style.cssText = 'background:var(--surface-2);border:1px solid var(--border);border-radius:12px;padding:16px;margin-bottom:12px';
  div.innerHTML = `
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
      <input type="text" name="modules[${idx}][title]" placeholder="Module title" required
             style="flex:1;background:var(--surface);border:1px solid var(--border);border-radius:8px;padding:8px 12px;font-size:0.88rem;color:var(--text-1)">
      <button type="button" onclick="removeModule(${idx})" style="background:none;border:none;cursor:pointer;color:var(--text-3);font-size:1.2rem;padding:4px">✕</button>
    </div>
    <div id="tasks-${idx}" style="margin-bottom:10px"></div>
    <button type="button" onclick="addTask(${idx})" class="btn btn-outline btn-sm" style="font-size:0.78rem">+ Add Task</button>
  `;
  document.getElementById('modules-container').appendChild(div);
}

function removeModule(idx) {
  const el = document.getElementById('module-' + idx);
  if (el) el.remove();
  if (!document.getElementById('modules-container').children.length) {
    document.getElementById('no-modules-msg').style.display = '';
  }
}

let taskCounters = {};

function addTask(moduleIdx) {
  if (!taskCounters[moduleIdx]) taskCounters[moduleIdx] = 0;
  const tIdx = taskCounters[moduleIdx]++;
  const container = document.getElementById('tasks-' + moduleIdx);
  const div = document.createElement('div');
  div.id = `task-${moduleIdx}-${tIdx}`;
  div.style.cssText = 'background:var(--surface);border:1px solid var(--border);border-radius:8px;padding:12px;margin-bottom:8px';
  div.innerHTML = `
    <div style="display:flex;align-items:flex-start;gap:8px;margin-bottom:8px">
      <input type="text" name="modules[${moduleIdx}][tasks][${tIdx}][title]" placeholder="Task title" required
             style="flex:1;background:var(--surface-2);border:1px solid var(--border);border-radius:6px;padding:7px 10px;font-size:0.85rem;color:var(--text-1)">
      <button type="button" onclick="removeTask(${moduleIdx},${tIdx})" style="background:none;border:none;cursor:pointer;color:var(--text-3);font-size:1.1rem;padding:4px;flex-shrink:0">✕</button>
    </div>
    <textarea name="modules[${moduleIdx}][tasks][${tIdx}][description]" placeholder="Description (optional)" rows="2"
              style="width:100%;background:var(--surface-2);border:1px solid var(--border);border-radius:6px;padding:7px 10px;font-size:0.82rem;color:var(--text-1);resize:vertical;margin-bottom:8px"></textarea>
    <div style="display:flex;align-items:center;gap:16px">
      <div style="display:flex;align-items:center;gap:8px">
        <label style="font-size:0.78rem;color:var(--text-3)">Max score</label>
        <input type="number" name="modules[${moduleIdx}][tasks][${tIdx}][max_score]" value="100" min="1" max="1000"
               style="width:72px;background:var(--surface-2);border:1px solid var(--border);border-radius:6px;padding:5px 8px;font-size:0.82rem;color:var(--text-1)">
      </div>
      <label style="display:flex;align-items:center;gap:6px;font-size:0.78rem;color:var(--text-3);cursor:pointer">
        <input type="checkbox" name="modules[${moduleIdx}][tasks][${tIdx}][is_locked]" value="1"> Locked initially
      </label>
    </div>
  `;
  container.appendChild(div);
}

function removeTask(moduleIdx, tIdx) {
  const el = document.getElementById(`task-${moduleIdx}-${tIdx}`);
  if (el) el.remove();
}

function toggleAiPanel() {
  const p = document.getElementById('ai-panel');
  p.style.display = p.style.display === 'none' ? 'block' : 'none';
}

async function generateWithAI() {
  const topic = document.getElementById('ai-topic').value.trim();
  const level = document.getElementById('ai-level').value;
  const weeks = parseInt(document.getElementById('ai-weeks').value);
  if (!topic) { alert('Please enter a topic.'); return; }

  const btn = document.getElementById('ai-generate-btn');
  btn.textContent = 'Generating...';
  btn.disabled = true;

  try {
    const res = await fetch('{{ route("ai.learning.generate") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({topic, level, weeks}),
    });
    const data = await res.json();
    if (data.error) { alert(data.error); return; }

    document.getElementById('modules-container').innerHTML = '';
    moduleCount = 0;
    taskCounters = {};
    document.getElementById('no-modules-msg').style.display = 'none';

    (data.modules || []).forEach(mod => {
      const idx = moduleCount;
      addModule();
      document.querySelector(`[name="modules[${idx}][title]"]`).value = mod.title;
      (mod.tasks || []).forEach(task => {
        addTask(idx);
        const tIdx = (taskCounters[idx] || 1) - 1;
        const tEl = document.querySelector(`[name="modules[${idx}][tasks][${tIdx}][title]"]`);
        const dEl = document.querySelector(`[name="modules[${idx}][tasks][${tIdx}][description]"]`);
        const sEl = document.querySelector(`[name="modules[${idx}][tasks][${tIdx}][max_score]"]`);
        if (tEl) tEl.value = task.title || '';
        if (dEl) dEl.value = task.description || '';
        if (sEl) sEl.value = task.max_score || 100;
      });
    });

    const titleEl = document.querySelector('[name="title"]');
    if (titleEl && !titleEl.value) titleEl.value = topic;
    document.getElementById('ai-panel').style.display = 'none';
  } catch (err) {
    alert('Failed to generate. Please try again.');
  } finally {
    btn.textContent = 'Generate';
    btn.disabled = false;
  }
}

// Start with one module
addModule();
</script>
@endsection
