<div style="background:var(--surface);border:1px solid {{ $highlight ? '#c9a227' : 'var(--border)' }};border-radius:16px;overflow:hidden;display:flex;{{ $highlight ? 'box-shadow:0 0 0 2px rgba(201,162,39,0.15)' : '' }}">

  {{-- Left accent bar --}}
  <div style="width:5px;background:{{ $highlight ? 'linear-gradient(180deg,#c9a227,#f5c842)' : 'var(--surface-2)' }};flex-shrink:0"></div>

  <div style="flex:1;padding:16px 20px">
    <div style="display:flex;align-items:flex-start;gap:14px;flex-wrap:wrap">

      {{-- Avatar --}}
      @if($ex->user->avatar_url)
      <img src="{{ $ex->user->avatar_url }}" style="width:44px;height:44px;border-radius:50%;object-fit:cover;flex-shrink:0">
      @else
      <div class="avatar" style="width:44px;height:44px;font-size:0.9rem;flex-shrink:0">{{ $ex->user->initials }}</div>
      @endif

      {{-- Info --}}
      <div style="flex:1;min-width:0">
        <div style="font-weight:700;font-size:0.95rem;margin-bottom:6px">{{ $ex->user->full_name }}</div>

        {{-- Offering / Seeking pills --}}
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:8px">
          <span style="background:#d1fae5;color:#065f46;border-radius:6px;padding:3px 10px;font-size:0.75rem;font-weight:700">
            Offering: {{ $ex->offering }}
          </span>
          <span style="color:var(--text-3);font-size:0.8rem"></span>
          <span style="background:#ede9fe;color:#5b21b6;border-radius:6px;padding:3px 10px;font-size:0.75rem;font-weight:700">
            Seeking: {{ $ex->seeking }}
          </span>
        </div>

        @if($ex->description)
        <div style="font-size:0.82rem;color:var(--text-3);line-height:1.5">{{ $ex->description }}</div>
        @endif

        <div style="font-size:0.75rem;color:var(--text-3);margin-top:6px">
          {{ $ex->created_at->diffForHumans() }}
          @if($ex->user->department) &nbsp;·&nbsp; {{ $ex->user->department }} @endif
        </div>
      </div>

      {{-- Action --}}
      <div style="flex-shrink:0;display:flex;flex-direction:column;align-items:flex-end;gap:8px">
        @if($ex->my_request > 0)
          <span style="background:#fef3c7;color:#92400e;border-radius:6px;padding:4px 12px;font-size:0.75rem;font-weight:700">Request Sent</span>
        @else
          <button onclick="document.getElementById('req-modal-{{ $ex->id }}').style.display='flex'"
                  class="btn btn-primary btn-sm">Request Exchange</button>
        @endif
      </div>

    </div>
  </div>
</div>

{{-- Request modal --}}
<div id="req-modal-{{ $ex->id }}" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9000;align-items:center;justify-content:center;padding:16px">
  <div style="background:var(--surface);border-radius:20px;padding:28px;width:100%;max-width:440px;box-shadow:0 20px 60px rgba(0,0,0,0.3)">
    <div style="font-weight:800;font-size:1.05rem;margin-bottom:4px">Request Skill Exchange</div>
    <div style="font-size:0.82rem;color:var(--text-3);margin-bottom:16px">
      You're offering <strong>{{ $ex->offering }}</strong> in exchange for learning <strong>{{ $ex->seeking }}</strong> from <strong>{{ $ex->user->full_name }}</strong>.
    </div>
    <form method="POST" action="{{ route('skill-exchange.request', $ex) }}">
      @csrf
      <textarea name="message" class="form-input" rows="3"
                placeholder="Optional: introduce yourself or explain what you'd like to cover…"
                style="width:100%;font-size:0.85rem;margin-bottom:14px;resize:vertical"></textarea>
      <div style="display:flex;gap:10px;justify-content:flex-end">
        <button type="button" onclick="document.getElementById('req-modal-{{ $ex->id }}').style.display='none'"
                class="btn btn-sm" style="background:var(--surface-2);color:var(--text);border:1px solid var(--border)">Cancel</button>
        <button type="submit" class="btn btn-primary btn-sm">Send Request</button>
      </div>
    </form>
  </div>
</div>
