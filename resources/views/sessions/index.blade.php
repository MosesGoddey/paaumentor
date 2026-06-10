@extends('layouts.sidebar')
@section('title', 'Sessions')
@section('breadcrumbs')<span style="opacity:0.5">›</span> <a href="{{ route('sessions.index') }}" style="color:var(--blue-500);text-decoration:none">Sessions</a>@endsection

@section('page-content')
<style>
.sess-card{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:18px;display:flex;gap:14px;align-items:flex-start;border-left-width:4px}
.sess-card.status-scheduled  {border-left-color:#2563eb}
.sess-card.status-in_progress{border-left-color:#f59e0b;background:#fffbeb}
.sess-card.status-completed  {border-left-color:#10b981}
.sess-card.status-cancelled  {border-left-color:#ef4444;opacity:0.75}
.sess-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0}
.outcome-badge{display:inline-block;border-radius:6px;padding:2px 8px;font-size:0.7rem;font-weight:700;text-transform:uppercase}
.status-badge{display:inline-block;border-radius:6px;padding:2px 8px;font-size:0.7rem;font-weight:700;text-transform:uppercase}
.empty-state{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:48px 24px;text-align:center}
.empty-state .es-icon{font-size:3rem;margin-bottom:12px}
.empty-state .es-title{font-weight:700;font-size:0.95rem;margin-bottom:6px}
.empty-state .es-sub{font-size:0.82rem;color:var(--text-3);margin-bottom:18px}
</style>

<div style="max-width:900px;margin:0 auto">

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px">
    <div>
      <h1 style="font-family:'Sora',sans-serif;font-size:1.4rem;font-weight:800;margin:0">Sessions</h1>
      <p style="color:var(--text-3);font-size:0.85rem;margin:4px 0 0">Your scheduled and past mentorship sessions</p>
    </div>
    @if($activeMentorships->isNotEmpty())
    <button onclick="document.getElementById('scheduleModal').style.display='flex'" class="btn btn-primary">+ Schedule Session</button>
    @endif
  </div>

  @if(session('success'))
  <div style="background:#dcfce7;border:1px solid #86efac;color:#166534;padding:10px 16px;border-radius:10px;margin-bottom:16px;font-size:0.88rem">{{ session('success') }}</div>
  @endif

  {{-- Upcoming --}}
  <h2 style="font-size:1rem;font-weight:700;margin-bottom:12px">Upcoming ({{ $upcoming->count() }})</h2>
  @if($upcoming->isNotEmpty())
  <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:32px">
    @foreach($upcoming as $sess)
    @php
      $other = $sess->mentorship->mentor_id === auth()->id()
               ? $sess->mentorship->mentee
               : $sess->mentorship->mentor;
    @endphp
    <div class="sess-card status-{{ $sess->status }}">
      <div class="sess-icon" style="background:var(--surface-2);font-size:0.65rem;font-weight:700;text-transform:uppercase;color:var(--text-3)">{{ $sess->type }}</div>
      <div style="flex:1;min-width:0">
        <div style="font-weight:700;font-size:0.95rem">{{ $sess->title }}</div>
        <div style="font-size:0.8rem;color:var(--text-3);margin-top:2px">with {{ $other->full_name }}</div>
        @if($sess->description)
        <div style="font-size:0.8rem;color:var(--text-3);margin-top:4px">{{ $sess->description }}</div>
        @endif
        <div style="display:flex;align-items:center;gap:10px;margin-top:8px;flex-wrap:wrap">
          <span style="font-size:0.78rem;color:var(--text-3)">{{ $sess->scheduled_at->format('D, M j · g:i A') }}</span>
          <span class="status-badge" style="background:{{ $sess->status === 'in_progress' ? '#fef9c3' : '#eff6ff' }};color:{{ $sess->status === 'in_progress' ? '#854d0e' : '#1d4ed8' }}">
            {{ $sess->status === 'in_progress' ? ' Live' : 'Scheduled' }}
          </span>
          <span style="font-size:0.78rem;color:var(--text-3);text-transform:capitalize">{{ $sess->type }}</span>
        </div>
        <div class="session-countdown" data-time="{{ $sess->scheduled_at->toISOString() }}"
             style="font-size:0.75rem;font-weight:600;color:var(--blue-500);margin-top:6px"></div>
      </div>
      <div style="display:flex;flex-direction:column;gap:8px;flex-shrink:0">
        @if($sess->room && $sess->type !== 'chat')
        <a href="{{ route('sessions.room', $sess) }}" class="btn btn-primary btn-sm">
          Join Call
        </a>
        @endif
        <form method="POST" action="{{ route('sessions.complete', $sess) }}" data-confirm="Mark this session as completed? This cannot be undone.">
          @csrf
          <button type="submit" class="btn btn-sm" style="background:#dcfce7;color:#166534;border:1px solid #86efac;width:100%"> Mark Complete</button>
        </form>
      </div>
    </div>
    @endforeach
  </div>
  @else
  <div class="empty-state" style="margin-bottom:32px">
    <div class="es-icon"></div>
    <div class="es-title">No upcoming sessions</div>
    <div class="es-sub">Schedule a session with your mentor or mentee to get started</div>
    @if($activeMentorships->isNotEmpty())
    <button onclick="document.getElementById('scheduleModal').style.display='flex'" class="btn btn-primary btn-sm">+ Schedule Session</button>
    @endif
  </div>
  @endif

  {{-- Past --}}
  <h2 style="font-size:1rem;font-weight:700;margin-bottom:12px">Past Sessions ({{ $past->count() }})</h2>
  @if($past->isNotEmpty())
  <div style="display:flex;flex-direction:column;gap:12px">
    @foreach($past as $sess)
    @php
      $other = $sess->mentorship->mentor_id === auth()->id()
               ? $sess->mentorship->mentee
               : $sess->mentorship->mentor;
    @endphp
    <div class="sess-card status-{{ $sess->status }}">
      <div class="sess-icon" style="background:var(--surface-2);font-size:0.65rem;font-weight:700;text-transform:uppercase;color:var(--text-3)">{{ $sess->type }}</div>
      <div style="flex:1;min-width:0">
        <div style="font-weight:700;font-size:0.95rem">{{ $sess->title }}</div>
        <div style="font-size:0.8rem;color:var(--text-3);margin-top:2px">with {{ $other->full_name }}</div>
        <div style="display:flex;align-items:center;gap:10px;margin-top:8px;flex-wrap:wrap">
          <span style="font-size:0.78rem;color:var(--text-3)">
             {{ ($sess->started_at ?? $sess->scheduled_at)?->format('D, M j · g:i A') }}
          </span>
          @if($sess->call_outcome)
          <span class="outcome-badge" style="background:{{ $sess->call_outcome === 'answered' ? '#dcfce7' : '#fee2e2' }};color:{{ $sess->call_outcome === 'answered' ? '#166534' : '#991b1b' }}">
            {{ $sess->call_outcome === 'answered' ? ' Answered' : ' Missed' }}
          </span>
          @endif
          <span class="status-badge" style="background:var(--surface-2);color:var(--text-3)">
            {{ ucfirst($sess->status) }}
          </span>
        </div>
      </div>
    </div>
    @endforeach
  </div>
  @else
  <div class="empty-state">
    <div class="es-icon"></div>
    <div class="es-title">No past sessions yet</div>
    <div class="es-sub">Completed sessions will appear here</div>
  </div>
  @endif
</div>

@push('modals')
@if($activeMentorships->isNotEmpty())
<div id="scheduleModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.55);z-index:9999;align-items:center;justify-content:center;padding:16px" onclick="if(event.target===this)this.style.display='none'">
  <div style="background:#fff;border-radius:20px;padding:28px;width:100%;max-width:480px;box-shadow:0 8px 40px rgba(0,0,0,0.3);max-height:90vh;overflow-y:auto;position:relative">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
      <h2 style="font-family:'Sora',sans-serif;font-size:1.1rem;font-weight:800;margin:0;color:#0f172a">Schedule Session</h2>
      <button onclick="document.getElementById('scheduleModal').style.display='none'" style="background:none;border:none;cursor:pointer;font-size:1.2rem;color:#64748b"></button>
    </div>
    <form method="POST" action="{{ route('sessions.store') }}">
      @csrf
      @if($errors->any())
      <div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:10px 14px;border-radius:10px;font-size:0.83rem;margin-bottom:12px">
        {{ $errors->first() }}
      </div>
      @endif
      <div style="display:flex;flex-direction:column;gap:14px">
        <div>
          <label class="form-label">With</label>
          <select name="mentorship_id" class="form-select" required>
            @foreach($activeMentorships as $m)
            @php $other = $m->mentor_id === auth()->id() ? $m->mentee : $m->mentor; @endphp
            <option value="{{ $m->id }}">{{ $other->full_name }} — {{ $m->topic }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="form-label">Title</label>
          <input type="text" name="title" class="form-input" placeholder="e.g. Week 3 Check-in" required value="{{ old('title') }}">
        </div>
        <div>
          <label class="form-label">Description <span style="color:#64748b;font-weight:400">(optional)</span></label>
          <textarea name="description" class="form-input" rows="2" placeholder="What will you cover?">{{ old('description') }}</textarea>
        </div>
        <div>
          <label class="form-label">Type</label>
          <select name="type" class="form-select" required>
            <option value="video"> Video Call</option>
            <option value="voice"> Voice Call</option>
            <option value="chat"> Chat Session</option>
          </select>
        </div>
        <div>
          <label class="form-label">Date & Time</label>
          <input type="datetime-local" name="scheduled_at" class="form-input" required
                 min="{{ now()->addMinutes(1)->format('Y-m-d\TH:i') }}"
                 value="{{ old('scheduled_at', now()->addHour()->format('Y-m-d\TH:i')) }}">
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top:4px"> Schedule Session</button>
      </div>
    </form>
  </div>
</div>
@if($errors->any())
<script>document.getElementById('scheduleModal').style.display='flex';</script>
@endif
@endif
@endpush

@push('scripts')
<script>
function updateCountdowns() {
  document.querySelectorAll('.session-countdown').forEach(el => {
    const diff = new Date(el.dataset.time) - new Date();
    if (diff <= 0) {
      el.textContent = ' Starting now';
      el.style.color = 'var(--danger)';
      return;
    }
    const d = Math.floor(diff / 86400000);
    const h = Math.floor((diff % 86400000) / 3600000);
    const m = Math.floor((diff % 3600000) / 60000);
    if (d > 0)      el.textContent = ` Starts in ${d}d ${h}h`;
    else if (h > 0) el.textContent = ` Starts in ${h}h ${m}m`;
    else            { el.textContent = ` Starts in ${m}m`; el.style.color = 'var(--danger)'; }
  });
}
updateCountdowns();
setInterval(updateCountdowns, 30000);
</script>
@endpush
@endsection
