@extends('layouts.sidebar')
@section('title', 'Assessment Result')

@section('page-content')
<div style="max-width:620px;margin:0 auto">

  @php $passed = $attempt->passed; $score = $attempt->score; @endphp

  {{-- Result hero card --}}
  <div style="text-align:center;padding:48px 40px;border-radius:28px;margin-bottom:28px;
    background:{{ $passed ? 'linear-gradient(135deg,#064e3b,#065f46)' : 'linear-gradient(135deg,#450a0a,#7f1d1d)' }};
    border:1px solid {{ $passed ? '#059669' : '#dc2626' }};
    color:#fff;box-shadow:0 24px 64px rgba(0,0,0,0.25)">

    <div style="font-size:4rem;margin-bottom:16px">{{ $passed ? '🎉' : '😔' }}</div>

    <div style="font-family:'Sora',sans-serif;font-weight:800;font-size:2.4rem;letter-spacing:-0.01em;margin-bottom:8px">
      {{ $score }}%
    </div>

    <div style="font-size:1.1rem;font-weight:700;margin-bottom:12px">
      {{ $passed ? 'Assessment Passed!' : 'Not Passed' }}
    </div>

    <div style="font-size:0.88rem;opacity:0.75;max-width:380px;margin:0 auto;line-height:1.65">
      @if($passed)
        Congratulations! Your result has been sent to the verifier for final certificate approval.
        You will be notified once reviewed.
      @else
        You need {{ $assessment->passing_score }}% to pass. Please review your learning materials
        and try again after the 24-hour cooldown.
      @endif
    </div>

    {{-- Score bar --}}
    <div style="margin-top:24px;background:rgba(255,255,255,0.12);border-radius:99px;height:10px;overflow:hidden;max-width:320px;margin:24px auto 0">
      <div style="height:100%;width:{{ $score }}%;background:{{ $passed ? '#10b981' : '#ef4444' }};border-radius:99px;transition:width 1s ease"></div>
    </div>
    <div style="font-size:0.75rem;opacity:0.55;margin-top:8px">Pass mark: {{ $assessment->passing_score }}%</div>
  </div>

  {{-- Stats --}}
  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:28px">
    <div class="stat-card">
      <div class="stat-value">{{ $attempt->score }}%</div>
      <div class="stat-label">Your Score</div>
    </div>
    <div class="stat-card">
      <div class="stat-value">{{ $attemptsUsed }}/3</div>
      <div class="stat-label">Attempts Used</div>
    </div>
    <div class="stat-card">
      <div class="stat-value">{{ $attempt->tab_switches }}</div>
      <div class="stat-label">Tab Switches</div>
    </div>
  </div>

  {{-- Next steps --}}
  <div class="card">
    <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:0.95rem;margin-bottom:14px">What's Next?</div>
    @if($passed)
      <div style="display:flex;flex-direction:column;gap:12px">
        <div style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;background:#f0fdf4;border-radius:12px;border:1px solid #86efac">
          <span style="font-size:1.2rem">✅</span>
          <div style="font-size:0.85rem;color:#166534">
            <strong>Verifier Review</strong> — A verifier will review your sessions, task grades, and assessment score before issuing your certificate.
          </div>
        </div>
        <div style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;background:var(--surface-2);border-radius:12px">
          <span style="font-size:1.2rem">🏆</span>
          <div style="font-size:0.85rem;color:var(--text-2)">
            Once approved, a signed certificate will be issued to both you and your mentor.
          </div>
        </div>
      </div>
    @elseif($attemptsUsed >= 3)
      <div style="padding:16px;background:#fee2e2;border-radius:12px;border:1px solid #fca5a5;font-size:0.85rem;color:#991b1b">
        <strong>Maximum attempts reached.</strong> Please speak with your mentor for guidance. A re-assessment can be requested through the verifier.
      </div>
    @else
      @php
        $lastFailed = auth()->user()->assessmentAttempts()
          ->where('certificate_request_id', $certRequest->id)
          ->whereNotNull('completed_at')->where('passed', false)
          ->latest('completed_at')->first();
        $retryAt = $lastFailed ? $lastFailed->completed_at->addHours(24) : null;
      @endphp
      <div style="display:flex;flex-direction:column;gap:12px">
        <div style="padding:14px 16px;background:#fef3c7;border-radius:12px;border:1px solid #fde68a;font-size:0.85rem;color:#92400e">
          <strong>{{ 3 - $attemptsUsed }} attempt(s) remaining.</strong>
          @if($retryAt) You can retry after <strong>{{ $retryAt->format('D, d M \a\t g:i A') }}</strong>. @endif
        </div>
        <div style="padding:14px 16px;background:var(--surface-2);border-radius:12px;font-size:0.85rem;color:var(--text-2)">
          💡 Review your learning path materials and discuss weak areas with your mentor before retrying.
        </div>
      </div>
    @endif
  </div>

  <div style="display:flex;gap:12px;margin-top:20px;justify-content:center;flex-wrap:wrap">
    <a href="{{ route('learning.index') }}" class="btn btn-outline btn-sm">← Back to Learning Paths</a>
    @if(!$passed && $attemptsUsed < 3)
    <a href="{{ route('assessment.show', $certRequest) }}" class="btn btn-primary btn-sm">View Assessment Lobby</a>
    @endif
    <a href="{{ route('dashboard') }}" class="btn btn-outline btn-sm">Dashboard</a>
  </div>

</div>
@endsection
