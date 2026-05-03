@extends('layouts.sidebar')
@section('title', 'Mentor Reflection')
@section('breadcrumbs')
  <span style="opacity:0.5">›</span>
  <a href="{{ route('learning.index') }}" style="color:var(--blue-500);text-decoration:none">Learning Paths</a>
  <span style="opacity:0.5">›</span> Mentor Reflection
@endsection

@section('page-content')
<div style="max-width:700px;margin:0 auto">

  <div style="margin-bottom:28px">
    <h1 style="font-family:'Sora',sans-serif;font-size:1.5rem;font-weight:800;margin:0">Mentor Reflection</h1>
    <p style="color:var(--text-3);font-size:0.88rem;margin:6px 0 0">
      Learning Path: <strong>{{ $certRequest->learningPath->title }}</strong> ·
      Mentee: <strong>{{ $certRequest->mentee->full_name }}</strong>
    </p>
  </div>

  {{-- Context card --}}
  <div style="background:linear-gradient(135deg,#fff7ed,#ffedd5);border:1.5px solid #fed7aa;border-radius:18px;padding:22px 24px;margin-bottom:28px">
    <div style="display:flex;align-items:flex-start;gap:14px">
      <div style="font-size:2rem;flex-shrink:0">🎯</div>
      <div>
        <div style="font-weight:700;font-size:0.95rem;color:#c2410c;margin-bottom:6px">Your mentee passed the assessment!</div>
        <div style="font-size:0.85rem;color:#92400e;line-height:1.65">
          <strong>{{ $certRequest->mentee->full_name }}</strong> scored
          <strong>{{ $certRequest->assessment_score }}%</strong> on the end-of-path assessment.
          Before a certificate can be issued, you need to submit a written reflection about
          this mentorship — this becomes part of the certificate review.
        </div>
      </div>
    </div>
  </div>

  {{-- Stats row --}}
  @php
    use App\Models\MentorSession;
    use App\Models\Mentorship;
    $mentorship = Mentorship::where('mentor_id', auth()->id())
      ->where('mentee_id', $certRequest->mentee_id)
      ->where('status', 'active')
      ->first();
    $completedSessions = $mentorship
      ? MentorSession::where('mentorship_id', $mentorship->id)->where('status','completed')->count()
      : 0;
    $sessionsOk = $completedSessions >= 3;
  @endphp

  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:28px">
    <div class="stat-card" style="border:2px solid {{ $sessionsOk ? '#86efac' : '#fca5a5' }}">
      <div class="stat-value" style="color:{{ $sessionsOk ? 'var(--success)' : '#dc2626' }}">{{ $completedSessions }}/3</div>
      <div class="stat-label">Sessions Completed</div>
    </div>
    <div class="stat-card">
      <div class="stat-value">{{ $certRequest->assessment_score }}%</div>
      <div class="stat-label">Assessment Score</div>
    </div>
    <div class="stat-card">
      <div class="stat-value">{{ $certRequest->learningPath->modules()->count() }}</div>
      <div class="stat-label">Modules Taught</div>
    </div>
  </div>

  @if(!$sessionsOk)
  <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:14px;padding:16px 20px;margin-bottom:20px;font-size:0.85rem;color:#991b1b">
    ⚠️ <strong>Note:</strong> You have only completed {{ $completedSessions }} of the required 3 sessions with this mentee.
    The verifier may block the certificate until the session requirement is met.
    Please complete the remaining sessions before or alongside submitting this reflection.
  </div>
  @endif

  {{-- Reflection form --}}
  <div class="card">
    <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1rem;margin-bottom:6px">Your Reflection</div>
    <p style="font-size:0.83rem;color:var(--text-3);margin:0 0 20px;line-height:1.6">
      Write honestly about the mentorship journey. This is read by the verifier before issuing the certificate.
      Cover what was taught, the mentee's growth, and the key achievements.
    </p>

    <form method="POST" action="{{ route('cert-request.reflect.submit', $certRequest) }}"
          style="display:flex;flex-direction:column;gap:16px">
      @csrf

      <div class="form-group" style="margin:0">
        <label class="form-label">
          What topics, skills, or concepts did you cover with {{ $certRequest->mentee->first_name }}?
        </label>
        <textarea name="mentor_reflection" class="form-input" rows="7"
                  style="resize:vertical;line-height:1.7"
                  minlength="80" maxlength="2000" required
                  placeholder="E.g. We covered the fundamentals of data structures including arrays, linked lists, and trees. I guided them through three hands-on projects...">{{ old('mentor_reflection') }}</textarea>
        <div id="charCount" style="font-size:0.75rem;color:var(--text-3);margin-top:4px;text-align:right">0 / 2000 characters (minimum 80)</div>
      </div>

      @error('mentor_reflection')
        <div style="color:#dc2626;font-size:0.83rem">{{ $message }}</div>
      @enderror

      <div style="background:var(--surface-2);border-radius:12px;padding:14px 16px">
        <div style="font-size:0.78rem;font-weight:700;color:var(--text-2);margin-bottom:8px">Reflection prompts (optional guidance):</div>
        <ul style="margin:0;padding-left:18px;font-size:0.8rem;color:var(--text-3);line-height:1.8">
          <li>What was the mentee's biggest challenge and how did they overcome it?</li>
          <li>What specific skills or knowledge did they gain?</li>
          <li>How did their confidence or capability change over the course of the mentorship?</li>
          <li>What are 2–3 achievements you are proud of from this mentorship?</li>
        </ul>
      </div>

      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:14px">
        Submit Reflection →
      </button>
    </form>
  </div>

</div>
@endsection

@push('scripts')
<script>
const ta = document.querySelector('textarea[name="mentor_reflection"]');
const cc = document.getElementById('charCount');
ta.addEventListener('input', () => {
  const n = ta.value.length;
  cc.textContent = `${n} / 2000 characters (minimum 80)`;
  cc.style.color = n < 80 ? '#dc2626' : 'var(--text-3)';
});
</script>
@endpush
