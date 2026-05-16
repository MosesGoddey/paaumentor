@extends('layouts.sidebar')
@section('title', $path->title)
@php use Illuminate\Support\Facades\Storage; @endphp

@section('page-content')
{{--  Certificate pipeline status banner  --}}
@if($path->certificate)
<div style="background:linear-gradient(135deg,#f59e0b,#f97316);border-radius:16px;padding:20px 24px;display:flex;align-items:center;gap:16px;margin-bottom:24px">
  <span style="font-size:2rem"></span>
  <div style="flex:1">
    <h3 style="font-size:1rem;font-weight:800;color:#fff;margin-bottom:4px">Certificate Issued!</h3>
    <p style="font-size:0.82rem;color:rgba(255,255,255,0.8)">ID: {{ $path->certificate->certificate_id }}</p>
  </div>
  <a href="{{ route('certificates.download', $path->certificate) }}" class="btn btn-sm" style="background:#fff;color:#1d4ed8;white-space:nowrap">Download PDF</a>
</div>

@elseif($certRequest && $certRequest->isPendingVerifier())
<div style="background:linear-gradient(135deg,#7c3aed,#5b21b6);border-radius:16px;padding:20px 24px;display:flex;align-items:center;gap:16px;margin-bottom:24px">
  <span style="font-size:2rem"></span>
  <div style="flex:1">
    <h3 style="font-size:1rem;font-weight:800;color:#fff;margin-bottom:4px">Under Verifier Review</h3>
    <p style="font-size:0.82rem;color:rgba(255,255,255,0.8)">Your mentor submitted their reflection. A verifier is now reviewing your certificate request.</p>
  </div>
</div>

@elseif($certRequest && $certRequest->isPendingMentorReflection())
  @if($user->id === $path->mentor_id)
  <div style="background:linear-gradient(135deg,#d97706,#b45309);border-radius:16px;padding:20px 24px;display:flex;align-items:center;gap:16px;margin-bottom:24px">
    <span style="font-size:2rem"></span>
    <div style="flex:1">
      <h3 style="font-size:1rem;font-weight:800;color:#fff;margin-bottom:4px">Action Required: Submit Your Reflection</h3>
      <p style="font-size:0.82rem;color:rgba(255,255,255,0.8)">{{ $certRequest->mentee->full_name }} passed the assessment ({{ $certRequest->assessment_score }}%). Submit your mentor reflection to proceed to certificate issuance.</p>
    </div>
    <a href="{{ route('cert-request.reflect', $certRequest) }}" class="btn btn-sm" style="background:#fff;color:#92400e;white-space:nowrap;font-weight:700">Submit Reflection </a>
  </div>
  @else
  <div style="background:linear-gradient(135deg,#d97706,#b45309);border-radius:16px;padding:20px 24px;display:flex;align-items:center;gap:16px;margin-bottom:24px">
    <span style="font-size:2rem"></span>
    <div style="flex:1">
      <h3 style="font-size:1rem;font-weight:800;color:#fff;margin-bottom:4px">Waiting for Mentor Reflection</h3>
      <p style="font-size:0.82rem;color:rgba(255,255,255,0.8)">You passed the assessment ({{ $certRequest->assessment_score }}%)! Your mentor needs to submit their reflection before a verifier reviews your certificate.</p>
    </div>
  </div>
  @endif

@elseif($certRequest && $certRequest->isPendingAssessment())
<div style="background:linear-gradient(135deg,var(--blue-700),var(--blue-500));border-radius:16px;padding:20px 24px;display:flex;align-items:center;gap:16px;margin-bottom:24px">
  <span style="font-size:2rem"></span>
  <div style="flex:1">
    <h3 style="font-size:1rem;font-weight:800;color:#fff;margin-bottom:4px">Assessment Ready!</h3>
    <p style="font-size:0.82rem;color:rgba(255,255,255,0.75)">You completed all tasks. Take the end-of-path assessment to proceed to certificate verification.</p>
  </div>
  <a href="{{ route('assessment.show', $certRequest) }}" class="btn btn-sm" style="background:#fff;color:#1d4ed8;white-space:nowrap;font-weight:700">Take Assessment </a>
</div>

@elseif($progress < 100)
<div style="background:linear-gradient(135deg,var(--blue-700),var(--blue-500));border-radius:16px;padding:20px 24px;display:flex;align-items:center;gap:16px;margin-bottom:24px">
  <span style="font-size:2.2rem"></span>
  <div style="flex:1">
    <h3 style="font-size:1rem;font-weight:800;color:#fff;margin-bottom:4px">You're {{ $progress }}% toward your certificate!</h3>
    <p style="font-size:0.82rem;color:rgba(255,255,255,0.75)">Complete all tasks — your mentor will then grade them and unlock your assessment.</p>
  </div>
  <div style="text-align:right"><div style="font-family:'Sora',sans-serif;font-size:2rem;font-weight:800;color:#fff">{{ $progress }}%</div></div>
</div>
@endif

<div style="display:flex;align-items:flex-start;gap:20px;margin-bottom:28px">
  <div style="width:64px;height:64px;border-radius:16px;background:linear-gradient(135deg,var(--blue-700),var(--blue-500));display:flex;align-items:center;justify-content:center;font-size:2rem;flex-shrink:0"></div>
  <div>
    <h1 style="font-size:1.4rem;font-weight:800;margin-bottom:8px">{{ $path->title }}</h1>
    <div style="display:flex;gap:16px;flex-wrap:wrap;font-size:0.82rem;color:var(--text-3)">
      <span> {{ $path->mentor->full_name }}</span>
      @if($path->due_date)<span> Due: {{ $path->due_date->format('M d, Y') }}</span>@endif
    </div>
  </div>
</div>

@foreach($path->modules as $module)
<div style="font-size:0.78rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:var(--text-3);padding:10px 0 8px;border-bottom:1px solid var(--border);margin-bottom:8px;display:flex;align-items:center;gap:8px">
   {{ $module->title }}
</div>
@foreach($module->tasks->sortBy('order') as $task)
@php
  $sub      = $task->submissions->first();
  $done     = $sub && $sub->status === 'graded';
  $pending  = $sub && $sub->status === 'submitted';
  $rejected = $sub && $sub->status === 'rejected';
  $canSubmit = !$done && !$pending && !$task->is_locked;
@endphp

<div style="background:var(--surface-2);border-radius:14px;border:1.5px solid {{ $done ? '#10b981' : ($task->is_locked ? 'var(--border)' : ($pending ? '#f59e0b' : ($rejected ? '#ef4444' : 'var(--blue-400)'))) }};margin-bottom:12px;overflow:hidden;opacity:{{ $task->is_locked ? '0.55' : '1' }}">

  {{-- Task header row --}}
  <div style="display:flex;align-items:flex-start;gap:14px;padding:14px 16px">
    <div style="width:26px;height:26px;border-radius:50%;border:2px solid {{ $done ? '#10b981' : 'var(--border)' }};display:flex;align-items:center;justify-content:center;background:{{ $done ? '#10b981' : 'transparent' }};flex-shrink:0;margin-top:1px">
      @if($done)<span style="font-size:0.7rem;color:#fff;font-weight:700"></span>
      @elseif($pending)<span style="font-size:0.75rem"></span>
      @elseif($rejected)<span style="font-size:0.75rem"></span>
      @elseif(!$task->is_locked)<span style="font-size:0.7rem;color:var(--blue-500);font-weight:700"></span>
      @endif
    </div>
    <div style="flex:1;min-width:0">
      <div style="font-weight:700;font-size:0.92rem">{{ $task->title }}</div>
      @if($task->description)
        <div style="font-size:0.78rem;color:var(--text-3);margin-top:3px">{{ $task->description }}</div>
      @endif
      <div style="margin-top:6px;display:flex;align-items:center;gap:8px;flex-wrap:wrap">
        @if($done)
          <span style="font-size:0.75rem;background:#dcfce7;color:#166534;padding:2px 8px;border-radius:6px;font-weight:700"> Graded</span>
          @if($sub->score !== null)
            <span style="font-size:0.75rem;color:#166534;font-weight:600">Score: {{ $sub->score }} / {{ $task->max_score }}</span>
          @endif
        @elseif($task->is_locked)
          <span style="font-size:0.75rem;background:var(--surface);color:var(--text-3);padding:2px 8px;border-radius:6px;font-weight:700"> Locked</span>
        @elseif($pending)
          <span style="font-size:0.75rem;background:#fef9c3;color:#854d0e;padding:2px 8px;border-radius:6px;font-weight:700"> Awaiting Grade</span>
        @elseif($rejected)
          <span style="font-size:0.75rem;background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:6px;font-weight:700"> Returned — Resubmit</span>
        @else
          <span style="font-size:0.75rem;background:#eff6ff;color:#1d4ed8;padding:2px 8px;border-radius:6px;font-weight:700"> Not yet submitted</span>
        @endif
        <span style="font-size:0.72rem;color:var(--text-3)">Max score: {{ $task->max_score }}</span>
      </div>
    </div>
    @if($canSubmit || $rejected)
      <button type="button"
        onclick="toggleSubmitForm({{ $task->id }})"
        id="toggleBtn_{{ $task->id }}"
        class="btn btn-primary btn-sm" style="flex-shrink:0;white-space:nowrap">
        {{ $rejected ? ' Resubmit' : '+ Submit Work' }}
      </button>
    @endif
  </div>

  {{-- Mentor feedback on graded task --}}
  @if($done && $sub->feedback)
  <div style="margin:0 16px 14px;padding:10px 14px;background:linear-gradient(135deg,#f0fdf4,#dcfce7);border-radius:10px;border-left:3px solid #10b981">
    <div style="font-size:0.72rem;font-weight:700;color:#166534;margin-bottom:4px">Mentor Feedback</div>
    <div style="font-size:0.82rem;color:#166534">{{ $sub->feedback }}</div>
  </div>
  @endif

  {{-- Mentor feedback on rejected task --}}
  @if($rejected && $sub->feedback)
  <div style="margin:0 16px 8px;padding:10px 14px;background:#fff5f5;border-radius:10px;border-left:3px solid #ef4444">
    <div style="font-size:0.72rem;font-weight:700;color:#991b1b;margin-bottom:4px">Returned — Mentor's Note</div>
    <div style="font-size:0.82rem;color:#991b1b">{{ $sub->feedback }}</div>
  </div>
  @endif

  {{-- Submission form (hidden by default, toggled) --}}
  @if($canSubmit || $rejected)
  <div id="submitForm_{{ $task->id }}" style="display:none;border-top:1px solid var(--border);padding:16px">
    <form method="POST" action="{{ route('learning.submit', $task) }}" enctype="multipart/form-data">
      @csrf
      <div style="font-size:0.8rem;font-weight:700;margin-bottom:10px;color:var(--text-2)">
        {{ $rejected ? 'Resubmit Your Work' : 'Submit Your Work' }}
      </div>
      <div style="display:flex;flex-direction:column;gap:10px">
        <div>
          <label style="font-size:0.78rem;font-weight:600;color:var(--text-2);display:block;margin-bottom:4px">Notes / Explanation <span style="font-weight:400;color:var(--text-3)">(optional)</span></label>
          <textarea name="notes" rows="3" class="form-input" style="font-size:0.85rem;resize:vertical"
            placeholder="Describe what you did, any challenges faced, or anything you want your mentor to know…"></textarea>
        </div>
        <div>
          <label style="font-size:0.78rem;font-weight:600;color:var(--text-2);display:block;margin-bottom:4px">Attach File <span style="font-weight:400;color:var(--text-3)">(optional, max 10 MB)</span></label>
          <input type="file" name="file" class="form-input" style="font-size:0.83rem;padding:6px 10px"
            accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.zip,.jpg,.jpeg,.png">
          <div style="font-size:0.72rem;color:var(--text-3);margin-top:4px">PDF, Word, PowerPoint, images, ZIP…</div>
        </div>
        <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:4px">
          <button type="button" onclick="toggleSubmitForm({{ $task->id }})" class="btn btn-outline btn-sm">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm"> Submit</button>
        </div>
      </div>
    </form>
  </div>
  @endif

  {{-- Show previously submitted notes/file (pending or rejected) --}}
  @if($pending || $rejected)
  <div style="border-top:1px solid var(--border);padding:12px 16px;background:{{ $pending ? '#fffbeb' : '#fff5f5' }}">
    <div style="font-size:0.72rem;font-weight:700;color:var(--text-3);margin-bottom:6px">YOUR PREVIOUS SUBMISSION</div>
    @if($sub->notes)
      <div style="font-size:0.82rem;color:var(--text-2);margin-bottom:6px">{{ $sub->notes }}</div>
    @endif
    @if($sub->file_path)
      <a href="{{ Storage::url($sub->file_path) }}" target="_blank"
         style="font-size:0.78rem;color:var(--blue-500);font-weight:600;text-decoration:none">
         View attached file
      </a>
    @endif
    @if(!$sub->notes && !$sub->file_path)
      <div style="font-size:0.78rem;color:var(--text-3);font-style:italic">No notes or file attached.</div>
    @endif
  </div>
  @endif

</div>
@endforeach
@endforeach

@push('scripts')
<script>
function toggleSubmitForm(taskId) {
  const form = document.getElementById('submitForm_' + taskId);
  const btn  = document.getElementById('toggleBtn_' + taskId);
  if (!form) return;
  const isOpen = form.style.display !== 'none';
  form.style.display = isOpen ? 'none' : 'block';
  if (btn) btn.textContent = isOpen ? (btn.dataset.label || '+ Submit Work') : ' Cancel';
}
// Store original label on page load
document.querySelectorAll('[id^="toggleBtn_"]').forEach(btn => {
  btn.dataset.label = btn.textContent.trim();
});
</script>
@endpush
@endsection
