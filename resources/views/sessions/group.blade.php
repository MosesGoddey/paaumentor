@extends('layouts.sidebar')
@section('title', 'Group Sessions')
@section('breadcrumbs')<span style="opacity:0.5">›</span> <a href="{{ route('sessions.index') }}" style="color:var(--blue-500);text-decoration:none">Sessions</a> <span style="opacity:0.5">›</span> Group@endsection

@section('page-content')
<style>
.sess-card{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:18px;display:flex;gap:14px;align-items:flex-start;border-left-width:4px}
.sess-card.status-scheduled  {border-left-color:#7c3aed}
.sess-card.status-in_progress{border-left-color:#f59e0b;background:#fffbeb}
.sess-card.status-completed  {border-left-color:#10b981}
.sess-card.status-cancelled  {border-left-color:#ef4444;opacity:0.75}
.sess-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:0.65rem;font-weight:700;text-transform:uppercase;color:var(--text-3);background:var(--surface-2);flex-shrink:0}
.status-badge{display:inline-block;border-radius:6px;padding:2px 8px;font-size:0.7rem;font-weight:700;text-transform:uppercase}
.empty-state{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:48px 24px;text-align:center}
.avatar-stack{display:flex}
.avatar-stack .av{width:26px;height:26px;border-radius:7px;background:linear-gradient(135deg,#1d4ed8,#7c3aed);display:flex;align-items:center;justify-content:center;font-size:0.6rem;font-weight:800;color:#fff;border:2px solid var(--surface);margin-left:-6px}
.avatar-stack .av:first-child{margin-left:0}
.host-badge{background:#ede9fe;color:#5b21b6;font-size:0.68rem;font-weight:700;padding:2px 8px;border-radius:999px}
</style>

<div style="max-width:900px;margin:0 auto">

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px">
    <div>
      <h1 style="font-family:'Sora',sans-serif;font-size:1.4rem;font-weight:800;margin:0">Group Sessions</h1>
      <p style="color:var(--text-3);font-size:0.85rem;margin:4px 0 0">Host or join multi-participant video calls</p>
    </div>
    <div style="display:flex;gap:10px">
      <a href="{{ route('sessions.index') }}" class="btn btn-sm" style="background:var(--surface-2);color:var(--text-2);border:1px solid var(--border)">1-on-1 Sessions</a>
      <button onclick="document.getElementById('createModal').style.display='flex'" class="btn btn-primary">+ New Group Session</button>
    </div>
  </div>

  @if(session('success'))
  <div style="background:#dcfce7;border:1px solid #86efac;color:#166534;padding:10px 16px;border-radius:10px;margin-bottom:16px;font-size:0.88rem">{{ session('success') }}</div>
  @endif

  {{-- Upcoming --}}
  @php $upcoming = $hosted->merge($invited)->sortBy('scheduled_at'); @endphp
  <h2 style="font-size:1rem;font-weight:700;margin-bottom:12px">Upcoming ({{ $upcoming->count() }})</h2>

  @if($upcoming->isNotEmpty())
  <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:32px">
    @foreach($upcoming as $gs)
    <div class="sess-card status-{{ $gs->status }}">
      <div class="sess-icon" title="{{ ucfirst($gs->type) }}"><x-icon :name="$gs->type === 'video' ? 'video' : 'phone'" :size="19" /></div>
      <div style="flex:1;min-width:0">
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
          <span style="font-weight:700;font-size:0.95rem">{{ $gs->title }}</span>
          @if($gs->host_id === auth()->id())
          <span class="host-badge">You host</span>
          @else
          <span style="font-size:0.78rem;color:var(--text-3)">Host: {{ $gs->host->full_name }}</span>
          @endif
        </div>
        @if($gs->description)
        <div style="font-size:0.8rem;color:var(--text-3);margin-top:3px">{{ $gs->description }}</div>
        @endif

        {{-- Participant avatars --}}
        <div style="display:flex;align-items:center;gap:8px;margin-top:8px">
          <div class="avatar-stack">
            @foreach($gs->members->take(5) as $m)
            <div class="av" title="{{ $m->full_name }}">{{ strtoupper(substr($m->first_name,0,1).substr($m->last_name,0,1)) }}</div>
            @endforeach
            @if($gs->members->count() > 5)
            <div class="av" style="background:#64748b">+{{ $gs->members->count()-5 }}</div>
            @endif
          </div>
          <span style="font-size:0.78rem;color:var(--text-3)">{{ $gs->participantCount() }} participant{{ $gs->participantCount() !== 1 ? 's' : '' }}</span>
          <span style="font-size:0.78rem;color:var(--text-3)">·</span>
          <span style="font-size:0.78rem;color:var(--text-3)">{{ $gs->scheduled_at->format('D, M j · g:i A') }}</span>
          <span class="status-badge" style="background:{{ $gs->status === 'in_progress' ? '#fef9c3' : '#ede9fe' }};color:{{ $gs->status === 'in_progress' ? '#854d0e' : '#5b21b6' }}">
            {{ $gs->status === 'in_progress' ? 'Live' : 'Scheduled' }}
          </span>
        </div>
        <div class="session-countdown" data-time="{{ $gs->scheduled_at->toISOString() }}"
             style="font-size:0.75rem;font-weight:600;color:var(--blue-500);margin-top:6px"></div>
      </div>
      <div style="display:flex;flex-direction:column;gap:8px;flex-shrink:0">
        <a href="{{ route('group-sessions.room', $gs) }}" class="btn btn-primary btn-sm">Join Call</a>
        @if($gs->host_id === auth()->id())
        <form method="POST" action="{{ route('group-sessions.complete', $gs) }}" data-confirm="End this group session for everyone?">
          @csrf
          <button type="submit" class="btn btn-sm" style="background:#dcfce7;color:#166534;border:1px solid #86efac;width:100%">End Session</button>
        </form>
        @endif
      </div>
    </div>
    @endforeach
  </div>
  @else
  <div class="empty-state" style="margin-bottom:32px">
    <div style="margin-bottom:12px;color:var(--text-3)"><x-icon name="users-round" :size="44" :stroke="1.5" /></div>
    <div style="font-weight:700;font-size:0.95rem;margin-bottom:6px">No upcoming group sessions</div>
    <div style="font-size:0.82rem;color:var(--text-3);margin-bottom:18px">Create a group session and invite multiple people to join</div>
    <button onclick="document.getElementById('createModal').style.display='flex'" class="btn btn-primary btn-sm">+ New Group Session</button>
  </div>
  @endif

  {{-- Past --}}
  <h2 style="font-size:1rem;font-weight:700;margin-bottom:12px">Past Sessions ({{ $past->count() }})</h2>
  @if($past->isNotEmpty())
  <div style="display:flex;flex-direction:column;gap:12px">
    @foreach($past as $gs)
    <div class="sess-card status-{{ $gs->status }}">
      <div class="sess-icon" title="{{ ucfirst($gs->type) }}"><x-icon :name="$gs->type === 'video' ? 'video' : 'phone'" :size="19" /></div>
      <div style="flex:1;min-width:0">
        <div style="font-weight:700;font-size:0.95rem">{{ $gs->title }}</div>
        <div style="font-size:0.8rem;color:var(--text-3);margin-top:2px">Host: {{ $gs->host->full_name }} · {{ $gs->participantCount() }} participants</div>
        <div style="display:flex;align-items:center;gap:10px;margin-top:8px;flex-wrap:wrap">
          <span style="font-size:0.78rem;color:var(--text-3)">{{ ($gs->ended_at ?? $gs->scheduled_at)?->format('D, M j · g:i A') }}</span>
          @if($gs->duration_minutes)
          <span style="font-size:0.78rem;color:var(--text-3)">{{ $gs->duration_minutes }}m</span>
          @endif
          <span class="status-badge" style="background:var(--surface-2);color:var(--text-3)">{{ ucfirst($gs->status) }}</span>
        </div>
      </div>
    </div>
    @endforeach
  </div>
  @else
  <div class="empty-state">
    <div style="font-weight:700;font-size:0.95rem;margin-bottom:6px">No past group sessions</div>
    <div style="font-size:0.82rem;color:var(--text-3)">Completed group sessions will appear here</div>
  </div>
  @endif
</div>

@push('modals')
<div id="createModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.55);z-index:9999;align-items:center;justify-content:center;padding:16px" onclick="if(event.target===this)this.style.display='none'">
  <div style="background:#fff;border-radius:20px;padding:28px;width:100%;max-width:500px;box-shadow:0 8px 40px rgba(0,0,0,0.3);max-height:90vh;overflow-y:auto">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
      <h2 style="font-family:'Sora',sans-serif;font-size:1.1rem;font-weight:800;margin:0;color:#0f172a">New Group Session</h2>
      <button onclick="document.getElementById('createModal').style.display='none'" style="background:none;border:none;cursor:pointer;display:inline-flex;align-items:center;color:#64748b"><x-icon name="x" :size="18" /></button>
    </div>
    <form method="POST" action="{{ route('group-sessions.store') }}">
      @csrf
      @if($errors->any())
      <div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:10px 14px;border-radius:10px;font-size:0.83rem;margin-bottom:12px">{{ $errors->first() }}</div>
      @endif
      <div style="display:flex;flex-direction:column;gap:14px">
        <div>
          <label class="form-label">Session Title</label>
          <input type="text" name="title" class="form-input" placeholder="e.g. React Bootcamp Q&A" required value="{{ old('title') }}">
        </div>
        <div>
          <label class="form-label">Description <span style="color:#64748b;font-weight:400">(optional)</span></label>
          <textarea name="description" class="form-input" rows="2" placeholder="What will be covered?">{{ old('description') }}</textarea>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
          <div>
            <label class="form-label">Type</label>
            <select name="type" class="form-select" required>
              <option value="video">Video Call</option>
              <option value="voice">Voice Call</option>
            </select>
          </div>
          <div>
            <label class="form-label">Max Participants</label>
            <input type="number" name="max_participants" class="form-input" value="{{ old('max_participants', 20) }}" min="2" max="100">
          </div>
        </div>
        <div>
          <label class="form-label">Date & Time</label>
          <input type="datetime-local" name="scheduled_at" class="form-input" required
                 min="{{ now()->addMinutes(1)->format('Y-m-d\TH:i') }}"
                 value="{{ old('scheduled_at', now()->addHour()->format('Y-m-d\TH:i')) }}">
        </div>

        @if($connections->isNotEmpty())
        <div>
          <label class="form-label">Invite Participants <span style="color:#64748b;font-weight:400">(optional)</span></label>
          <div style="border:1px solid var(--border);border-radius:10px;max-height:180px;overflow-y:auto;padding:8px 4px">
            @foreach($connections as $conn)
            <label style="display:flex;align-items:center;gap:10px;padding:7px 10px;border-radius:8px;cursor:pointer;transition:background 0.12s" onmouseover="this.style.background='var(--surface-2)'" onmouseout="this.style.background='transparent'">
              <input type="checkbox" name="invitees[]" value="{{ $conn->id }}" style="width:15px;height:15px;accent-color:#7c3aed">
              <div style="width:30px;height:30px;border-radius:8px;background:linear-gradient(135deg,#1d4ed8,#7c3aed);display:flex;align-items:center;justify-content:center;font-size:0.62rem;font-weight:800;color:#fff;flex-shrink:0">{{ strtoupper(substr($conn->first_name,0,1).substr($conn->last_name,0,1)) }}</div>
              <div>
                <div style="font-size:0.85rem;font-weight:600;color:#0f172a">{{ $conn->full_name }}</div>
                <div style="font-size:0.72rem;color:#64748b;text-transform:capitalize">{{ $conn->role }}</div>
              </div>
            </label>
            @endforeach
          </div>
          <p style="font-size:0.75rem;color:#64748b;margin-top:5px">You can also share the session link after creating it</p>
        </div>
        @else
        <p style="font-size:0.82rem;color:#64748b;background:#f8fafc;border-radius:10px;padding:10px 14px">You have no active mentorship connections yet. You can share the session link with anyone after creating it.</p>
        @endif

        <button type="submit" class="btn btn-primary" style="margin-top:4px">Create Group Session</button>
      </div>
    </form>
  </div>
</div>
@if($errors->any())
<script>document.getElementById('createModal').style.display='flex';</script>
@endif
@endpush

@push('scripts')
<script>
function updateCountdowns() {
  document.querySelectorAll('.session-countdown').forEach(el => {
    const diff = new Date(el.dataset.time) - new Date();
    if (diff <= 0) { el.textContent = 'Starting now'; el.style.color = 'var(--danger)'; return; }
    const d = Math.floor(diff / 86400000);
    const h = Math.floor((diff % 86400000) / 3600000);
    const m = Math.floor((diff % 3600000) / 60000);
    if (d > 0)      el.textContent = `Starts in ${d}d ${h}h`;
    else if (h > 0) el.textContent = `Starts in ${h}h ${m}m`;
    else            { el.textContent = `Starts in ${m}m`; el.style.color = 'var(--danger)'; }
  });
}
updateCountdowns();
setInterval(updateCountdowns, 30000);

document.querySelectorAll('[data-confirm]').forEach(form => {
  form.addEventListener('submit', e => {
    if (!confirm(form.dataset.confirm)) e.preventDefault();
  });
});
</script>
@endpush
@endsection
