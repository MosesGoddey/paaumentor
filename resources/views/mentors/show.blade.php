@extends('layouts.sidebar')
@section('title', $mentor->full_name)

@section('page-content')
<div class="card" style="margin-bottom:24px">
  <div style="height:100px;background:linear-gradient(135deg,var(--blue-700),var(--blue-500));border-radius:16px 16px 0 0;margin:-24px -24px 0"></div>
  <div style="margin-top:-50px;padding:0 0 20px">
    <div class="avatar avatar-xl" style="border:4px solid var(--surface);margin-bottom:12px">{{ $mentor->initials }}</div>
    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:4px">
      <h1 style="font-size:1.5rem;font-weight:800;margin:0">{{ $mentor->full_name }}</h1>
      @php $tier = $mentor->mentor_tier; @endphp
      <span style="font-size:0.72rem;font-weight:700;padding:3px 10px;border-radius:99px;letter-spacing:0.05em;
        background:{{ $tier==='lead' ? '#fef3c7' : ($tier==='senior' ? '#ede9fe' : '#dbeafe') }};
        color:{{ $tier==='lead' ? '#92400e' : ($tier==='senior' ? '#5b21b6' : '#1d4ed8') }}">
        {{ $mentor->mentor_tier_icon }} {{ $mentor->mentor_tier_label }}
      </span>
    </div>
    <p style="color:var(--text-3);font-size:0.9rem;margin-bottom:12px">{{ $mentor->level }} · {{ $mentor->department }}</p>
    <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:16px">
      @foreach($mentor->hasSkills as $s)<span class="badge badge-blue">{{ $s->name }}</span>@endforeach
    </div>
    @if($mentor->bio)<p style="font-size:0.9rem;color:var(--text-2);line-height:1.7;margin-bottom:16px">{{ $mentor->bio }}</p>@endif
    <div style="display:flex;gap:20px;margin-bottom:20px">
      <div>
        <div style="color:#f59e0b;font-size:1rem;letter-spacing:1px">
          @for($s = 1; $s <= 5; $s++){{ $s <= round($mentor->average_rating) ? '★' : '☆' }}@endfor
        </div>
        <div style="font-size:0.75rem;color:var(--text-3)">{{ $mentor->average_rating > 0 ? number_format($mentor->average_rating,1).' / 5' : 'No ratings yet' }}</div>
      </div>
      <div><div style="font-family:'Sora',sans-serif;font-weight:800;font-size:1.3rem">{{ $mentor->mentorMentorships->count() }}</div><div style="font-size:0.75rem;color:var(--text-3)">Mentees</div></div>
    </div>

    <form method="POST" action="{{ route('mentors.request', $mentor) }}" style="background:var(--surface-2);border-radius:14px;padding:20px">
      @csrf
      <div style="font-family:'Sora',sans-serif;font-weight:700;margin-bottom:16px">Request Mentorship</div>
      <div class="form-group"><label class="form-label">Topic</label><input type="text" name="topic" class="form-input" placeholder="e.g. Laravel & Database Design" required></div>
      <div class="form-group"><label class="form-label">Your Goal</label><textarea name="goal" class="form-input" rows="3" placeholder="What do you want to achieve?"></textarea></div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">Send Mentorship Request</button>
    </form>
  </div>
</div>

@if($reviews->count())
<div class="card">
  <div style="font-family:'Sora',sans-serif;font-weight:700;margin-bottom:16px">Reviews</div>
  @foreach($reviews as $r)
  <div style="padding:14px 0;border-bottom:1px solid var(--border)">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
      <div class="avatar avatar-sm">{{ $r->rater->initials }}</div>
      <div><div style="font-weight:700;font-size:0.88rem">{{ $r->rater->full_name }}</div><div style="font-size:0.75rem;color:var(--text-3)">{{ str_repeat('⭐', $r->score) }}</div></div>
    </div>
    @if($r->review)<p style="font-size:0.85rem;color:var(--text-2)">{{ $r->review }}</p>@endif
  </div>
  @endforeach
</div>
@endif
@endsection
