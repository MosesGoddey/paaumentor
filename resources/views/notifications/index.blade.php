@extends('layouts.sidebar')
@section('title', 'Notifications')

@section('page-content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
  <h1 class="section-title">Notifications</h1>
  <form method="POST" action="{{ route('notifications.readAll') }}">
    @csrf
    <button type="submit" class="btn btn-outline btn-sm">Mark all read</button>
  </form>
</div>

<div class="card">
  @forelse($notifs as $n)
  <div style="display:flex;align-items:flex-start;gap:14px;padding:16px;border-bottom:1px solid var(--border);background:{{ $n->read_at ? 'transparent' : 'var(--surface-2)' }}">
    <div style="width:10px;height:10px;border-radius:50%;background:{{ $n->read_at ? 'var(--border)' : 'var(--blue-500)' }};margin-top:6px;flex-shrink:0"></div>
    <div style="flex:1">
      <div style="font-weight:{{ $n->read_at ? '500' : '700' }};font-size:0.9rem">{{ $n->title }}</div>
      @if($n->body)<div style="font-size:0.82rem;color:var(--text-3);margin-top:2px">{{ $n->body }}</div>@endif
      @php $nd = is_array($n->data) ? $n->data : []; @endphp
      @if($n->type === 'session_scheduled')
        <a href="{{ route('sessions.index') }}" style="display:inline-block;margin-top:6px;font-size:0.8rem;color:var(--blue-500);font-weight:700;text-decoration:none">
          📅 View Sessions →
        </a>
      @elseif($n->type === 'upgrade_assessment_ready' && isset($nd['upgrade_request_id']))
        <a href="{{ route('upgrade-assessment.show', $nd['upgrade_request_id']) }}" style="display:inline-block;margin-top:6px;font-size:0.8rem;color:var(--blue-500);font-weight:700;text-decoration:none">
          🧠 Take Assessment →
        </a>
      @elseif(in_array($n->type, ['upgrade_assessment_passed','upgrade_assessment_failed']) && isset($nd['upgrade_request_id']))
        <a href="{{ route('upgrade-assessment.show', $nd['upgrade_request_id']) }}" style="display:inline-block;margin-top:6px;font-size:0.8rem;color:var(--blue-500);font-weight:700;text-decoration:none">
          View Assessment →
        </a>
      @elseif($n->type === 'upgrade_recommendation_request' && isset($nd['upgrade_request_id']))
        <a href="{{ route('upgrade.recommend.form', $nd['upgrade_request_id']) }}" style="display:inline-block;margin-top:6px;font-size:0.8rem;color:var(--blue-500);font-weight:700;text-decoration:none">
          ✍️ Write Recommendation →
        </a>
      @elseif($n->type === 'mentor_reflection_required' && isset($nd['certificate_request_id']))
        <a href="{{ route('cert-request.reflect', $nd['certificate_request_id']) }}" style="display:inline-block;margin-top:6px;font-size:0.8rem;color:var(--blue-500);font-weight:700;text-decoration:none">
          ✍️ Submit Reflection →
        </a>
      @elseif(in_array($n->type, ['certificate_pending_review','certificate_rejected','certificate_pending_verifier']) && isset($nd['certificate_request_id']))
        <a href="{{ route('learning.index') }}" style="display:inline-block;margin-top:6px;font-size:0.8rem;color:var(--blue-500);font-weight:700;text-decoration:none">
          View Learning Paths →
        </a>
      @elseif($n->type === 'certificate_issued')
        <a href="{{ route('certificates.index') }}" style="display:inline-block;margin-top:6px;font-size:0.8rem;color:var(--blue-500);font-weight:700;text-decoration:none">
          🏆 View My Certificates →
        </a>
      @elseif($n->type === 'mentor_portfolio_approved' || $n->type === 'mentor_portfolio_rejected')
        <a href="{{ route('dashboard') }}" style="display:inline-block;margin-top:6px;font-size:0.8rem;color:var(--blue-500);font-weight:700;text-decoration:none">
          Go to Dashboard →
        </a>
      @endif
      <div style="font-size:0.72rem;color:var(--text-3);margin-top:4px">{{ $n->created_at->diffForHumans() }}</div>
    </div>
    @if(!$n->read_at)
    <form method="POST" action="{{ route('notifications.read', $n) }}">
      @csrf
      <button type="submit" class="btn btn-outline btn-sm">Mark read</button>
    </form>
    @endif
  </div>
  @empty
  <div style="padding:48px;text-align:center;color:var(--text-3)">
    <p>You're all caught up! No notifications.</p>
  </div>
  @endforelse
</div>

<div style="margin-top:20px">{{ $notifs->links() }}</div>
@endsection
