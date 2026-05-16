@extends('layouts.sidebar')
@section('title', 'Mentor Upgrade Requests')
@section('breadcrumbs')<span style="opacity:0.5">›</span> Admin <span style="opacity:0.5">›</span> <a href="{{ route('upgrade.admin') }}" style="color:var(--blue-500);text-decoration:none">Upgrade Requests</a>@endsection

@section('page-content')
<div style="max-width:900px;margin:0 auto">

  <div style="margin-bottom:28px">
    <h1 style="font-family:'Sora',sans-serif;font-size:1.5rem;font-weight:800;margin:0">Mentor Upgrade Requests</h1>
    <p style="color:var(--text-3);font-size:0.88rem;margin:6px 0 0">Review and action mentee upgrade applications.</p>
  </div>

  @if($requests->isEmpty())
  <div style="background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:48px;text-align:center;color:var(--text-3);font-size:0.88rem">
    No upgrade requests yet.
  </div>
  @else
  <div style="display:flex;flex-direction:column;gap:16px">
    @foreach($requests as $req)
    @php
      $statusColors = [
        'pending'     => ['bg'=>'#fef9c3','text'=>'#854d0e','border'=>'#fde68a','label'=>' Awaiting Recommendation'],
        'recommended' => ['bg'=>'#eff6ff','text'=>'#1d4ed8','border'=>'#bfdbfe','label'=>' Ready for Review'],
        'approved'    => ['bg'=>'#f0fdf4','text'=>'#166534','border'=>'#86efac','label'=>' Approved'],
        'rejected'    => ['bg'=>'#fef2f2','text'=>'#991b1b','border'=>'#fecaca','label'=>' Rejected'],
      ];
      $sc = $statusColors[$req->status];
    @endphp
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:20px;overflow:hidden">

      {{-- Header --}}
      <div style="padding:18px 24px;display:flex;align-items:center;gap:14px;border-bottom:1px solid var(--border)">
        @if($req->mentee->avatar_url)
          <img src="{{ $req->mentee->avatar_url }}" style="width:44px;height:44px;border-radius:50%;object-fit:cover;flex-shrink:0">
        @else
          <div class="avatar avatar-sm" style="width:44px;height:44px;font-size:0.9rem;flex-shrink:0">{{ $req->mentee->initials }}</div>
        @endif
        <div style="flex:1;min-width:0">
          <div style="font-weight:700;font-size:0.95rem">{{ $req->mentee->full_name }}</div>
          <div style="font-size:0.78rem;color:var(--text-3)">{{ $req->mentee->department }} · {{ $req->mentee->level }} · Applied {{ $req->created_at->format('M j, Y') }}</div>
        </div>
        <span style="font-size:0.72rem;font-weight:700;background:{{ $sc['bg'] }};color:{{ $sc['text'] }};border:1px solid {{ $sc['border'] }};border-radius:8px;padding:4px 10px;flex-shrink:0">{{ $sc['label'] }}</span>
      </div>

      <div style="padding:18px 24px;display:flex;flex-direction:column;gap:14px">

        {{-- Mentor recommendation --}}
        <div>
          <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-3);margin-bottom:6px">Mentor — {{ $req->mentor->full_name }}</div>
          @if($req->mentor_note)
            <div style="background:var(--surface-2);border-radius:10px;padding:12px 14px;font-size:0.85rem;line-height:1.6;color:var(--text)">{{ $req->mentor_note }}</div>
            <div style="font-size:0.72rem;color:var(--text-3);margin-top:4px">Submitted {{ $req->mentor_recommended_at?->format('M j, Y g:i A') }}</div>
          @else
            <div style="font-size:0.85rem;color:var(--text-3);font-style:italic">Waiting for mentor recommendation…</div>
          @endif
        </div>

        {{-- Admin note (if reviewed) --}}
        @if($req->admin_note)
        <div>
          <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-3);margin-bottom:6px">Admin Note</div>
          <div style="background:var(--surface-2);border-radius:10px;padding:12px 14px;font-size:0.85rem;color:var(--text)">{{ $req->admin_note }}</div>
        </div>
        @endif

        {{-- Action buttons --}}
        @if($req->isRecommended())
        <div style="display:flex;gap:10px;flex-wrap:wrap;padding-top:4px">
          <form method="POST" action="{{ route('upgrade.approve', $req) }}" data-confirm="Approve this upgrade? {{ $req->mentee->full_name }} will become a mentor immediately.">
            @csrf
            <div style="display:flex;gap:8px;align-items:center">
              <input type="text" name="admin_note" class="form-input" placeholder="Optional note to mentee…" style="width:260px;font-size:0.82rem;padding:8px 12px">
              <button type="submit" class="btn btn-success btn-sm"> Approve</button>
            </div>
          </form>
          <form method="POST" action="{{ route('upgrade.reject', $req) }}" data-confirm="Reject this upgrade request?">
            @csrf
            <div style="display:flex;gap:8px;align-items:center">
              <input type="text" name="admin_note" class="form-input" placeholder="Reason for rejection (required)" required style="width:240px;font-size:0.82rem;padding:8px 12px">
              <button type="submit" class="btn btn-danger btn-sm"> Reject</button>
            </div>
          </form>
        </div>
        @elseif($req->isPending())
        <div style="font-size:0.82rem;color:var(--text-3);font-style:italic">Waiting for mentor to submit a recommendation before you can act.</div>
        @endif

      </div>
    </div>
    @endforeach
  </div>
  @endif

</div>
@endsection
