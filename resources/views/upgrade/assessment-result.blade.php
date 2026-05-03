@extends('layouts.sidebar')
@section('title', 'Upgrade Assessment Result')

@section('page-content')
<div style="max-width:620px;margin:0 auto">

  @php $passed = $attempt->passed; $score = $attempt->score; @endphp

  {{-- Result hero --}}
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
        Your mentor has been notified to write your recommendation. You will be notified once submitted.
      @else
        You need {{ $assessment->passing_score }}% to pass. Review your learning materials and try again after the 24-hour cooldown.
      @endif
    </div>
    <div style="margin-top:24px;background:rgba(255,255,255,0.12);border-radius:99px;height:10px;overflow:hidden;max-width:320px;margin:24px auto 0">
      <div style="height:100%;width:{{ $score }}%;background:{{ $passed ? '#10b981' : '#ef4444' }};border-radius:99px"></div>
    </div>
    <div style="font-size:0.75rem;opacity:0.55;margin-top:8px">Pass mark: {{ $assessment->passing_score }}%</div>
  </div>

  {{-- Stats --}}
  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px">
    <div class="stat-card">
      <div class="stat-value">{{ $score }}%</div>
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

  {{-- Score breakdown chart --}}
  <div class="card" style="margin-bottom:24px">
    <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:0.95rem;margin-bottom:16px">Score Breakdown</div>
    <div style="display:flex;align-items:center;gap:28px">
      <canvas id="scoreDonut" width="160" height="160" style="flex-shrink:0"></canvas>
      <div style="display:flex;flex-direction:column;gap:10px;font-size:0.85rem">
        <div style="display:flex;align-items:center;gap:10px">
          <span style="width:14px;height:14px;border-radius:3px;background:{{ $passed ? '#10b981' : '#ef4444' }};display:inline-block;flex-shrink:0"></span>
          <span>Your score — <strong>{{ $score }}%</strong></span>
        </div>
        @if(!$passed && $score < $assessment->passing_score)
        <div style="display:flex;align-items:center;gap:10px">
          <span style="width:14px;height:14px;border-radius:3px;background:#f59e0b;display:inline-block;flex-shrink:0"></span>
          <span>Gap to pass — <strong>{{ $assessment->passing_score - $score }}%</strong></span>
        </div>
        @endif
        <div style="display:flex;align-items:center;gap:10px">
          <span style="width:14px;height:14px;border-radius:3px;background:#e2e8f0;display:inline-block;flex-shrink:0"></span>
          <span>Remaining — <strong>{{ 100 - max($score, $assessment->passing_score) }}%</strong></span>
        </div>
        <div style="margin-top:4px;padding:8px 12px;background:var(--surface-2);border-radius:8px;font-size:0.8rem">
          Pass mark: <strong>{{ $assessment->passing_score }}%</strong>
          @if($passed)
            <span style="color:#10b981;font-weight:700;margin-left:6px">✓ Passed by {{ $score - $assessment->passing_score }}%</span>
          @else
            <span style="color:#ef4444;font-weight:700;margin-left:6px">✗ Short by {{ $assessment->passing_score - $score }}%</span>
          @endif
        </div>
      </div>
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
            <strong>Step 1 Complete</strong> — Your mentor <strong>{{ $upgradeRequest->mentor->full_name }}</strong> has been notified to write your recommendation.
          </div>
        </div>
        <div style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;background:var(--surface-2);border-radius:12px">
          <span style="font-size:1.2rem">📝</span>
          <div style="font-size:0.85rem;color:var(--text-2)">
            Once your mentor submits the recommendation, an admin or verifier will review and approve your upgrade.
          </div>
        </div>
      </div>
    @elseif($attemptsUsed >= 3)
      <div style="padding:16px;background:#fee2e2;border-radius:12px;border:1px solid #fca5a5;font-size:0.85rem;color:#991b1b">
        <strong>Maximum attempts reached.</strong> Please speak with your mentor for guidance before requesting another attempt.
      </div>
    @else
      @php
        $lastFailed = \App\Models\UpgradeAssessmentAttempt::where('upgrade_request_id', $upgradeRequest->id)
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
          💡 Review your completed learning path materials with your mentor before retrying.
        </div>
      </div>
    @endif
  </div>

  <div style="display:flex;gap:12px;margin-top:20px;justify-content:center;flex-wrap:wrap">
    @if(!$passed && $attemptsUsed < 3)
    <a href="{{ route('upgrade-assessment.show', $upgradeRequest) }}" class="btn btn-primary btn-sm">Retry Assessment</a>
    @endif
    <a href="{{ route('upgrade.show') }}" class="btn btn-outline btn-sm">Upgrade Page</a>
    <a href="{{ route('dashboard') }}" class="btn btn-outline btn-sm">Dashboard</a>
  </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const score   = {{ $score }};
  const pass    = {{ $assessment->passing_score }};
  const passed  = {{ $passed ? 'true' : 'false' }};
  const gap     = Math.max(0, pass - score);
  const rest    = Math.max(0, 100 - Math.max(score, pass));

  const segments = passed
    ? [{ value: score, color: '#10b981' }, { value: rest, color: '#e2e8f0' }]
    : [{ value: score, color: '#ef4444' }, { value: gap, color: '#f59e0b' }, { value: rest, color: '#e2e8f0' }];

  drawDonutChart('scoreDonut', segments);
});
</script>
@endpush
@endsection
