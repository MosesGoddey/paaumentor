@extends('layouts.sidebar')
@section('title', 'Write Recommendation')

@section('page-content')
<div style="max-width:620px;margin:0 auto">

  <div style="margin-bottom:28px">
    <h1 style="font-family:'Sora',sans-serif;font-size:1.5rem;font-weight:800;margin:0">Write a Recommendation</h1>
    <p style="color:var(--text-3);font-size:0.88rem;margin:6px 0 0">Your recommendation will be reviewed by an admin before the upgrade is granted.</p>
  </div>

  {{-- Mentee summary card --}}
  <div style="background:var(--surface);border:1px solid var(--border);border-radius:20px;padding:20px 24px;margin-bottom:24px;display:flex;align-items:center;gap:16px">
    @if($upgradeRequest->mentee->avatar_url)
      <img src="{{ $upgradeRequest->mentee->avatar_url }}" style="width:52px;height:52px;border-radius:50%;object-fit:cover;flex-shrink:0">
    @else
      <div class="avatar" style="width:52px;height:52px;font-size:1rem;flex-shrink:0">{{ $upgradeRequest->mentee->initials }}</div>
    @endif
    <div>
      <div style="font-weight:700;font-size:1rem">{{ $upgradeRequest->mentee->full_name }}</div>
      <div style="font-size:0.8rem;color:var(--text-3);margin-top:2px">{{ $upgradeRequest->mentee->department }} · {{ $upgradeRequest->mentee->level }}</div>
      <span style="font-size:0.68rem;background:#dbeafe;color:#1d4ed8;border-radius:4px;padding:2px 7px;font-weight:700;text-transform:uppercase;letter-spacing:0.04em;display:inline-block;margin-top:6px">Mentee</span>
    </div>
    <div style="margin-left:auto;text-align:right">
      <div style="font-size:0.75rem;color:var(--text-3)">Applied</div>
      <div style="font-weight:600;font-size:0.85rem">{{ $upgradeRequest->created_at->format('M j, Y') }}</div>
    </div>
  </div>

  {{-- Recommendation form --}}
  <div style="background:var(--surface);border:1px solid var(--border);border-radius:20px;padding:24px">
    <form method="POST" action="{{ route('upgrade.recommend', $upgradeRequest) }}">
      @csrf
      <div class="form-group" style="margin-bottom:20px">
        <label class="form-label">Your Recommendation</label>
        <textarea name="mentor_note" class="form-input" rows="6" required minlength="20" maxlength="1000"
          placeholder="Describe why this mentee is ready to become a mentor. Include their strengths, progress, and how they have demonstrated the ability to guide others...">{{ old('mentor_note') }}</textarea>
        @error('mentor_note')
          <div style="font-size:0.78rem;color:var(--danger);margin-top:4px">{{ $message }}</div>
        @enderror
        <div style="font-size:0.75rem;color:var(--text-3);margin-top:4px">Minimum 20 characters.</div>
      </div>
      <div style="display:flex;gap:10px">
        <button type="submit" class="btn btn-primary">Submit Recommendation</button>
        <a href="{{ route('sessions.index') }}" class="btn btn-outline">Cancel</a>
      </div>
    </form>
  </div>

</div>
@endsection
