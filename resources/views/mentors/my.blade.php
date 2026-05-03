@extends('layouts.sidebar')
@section('title', 'My Mentors')

@section('page-content')
<h1 style="font-size:1.4rem;font-weight:800;margin-bottom:20px">My Mentors</h1>

@if(session('success'))
<div style="background:#d1fae5;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:0.88rem;color:#065f46">{{ session('success') }}</div>
@endif

@forelse($mentorships as $m)
@php $mentor = $m->mentor; @endphp
<div class="card" style="margin-bottom:16px">
  <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap">
    @if($mentor->avatar_url)
    <img src="{{ $mentor->avatar_url }}" alt="" style="width:56px;height:56px;border-radius:50%;object-fit:cover;flex-shrink:0">
    @else
    <div class="avatar" style="width:56px;height:56px;font-size:1.1rem;flex-shrink:0">{{ $mentor->initials }}</div>
    @endif

    <div style="flex:1;min-width:0">
      <div style="font-weight:800;font-size:1rem">{{ $mentor->full_name }}</div>
      <div style="font-size:0.8rem;color:var(--text-3);margin-top:2px">{{ ucfirst($mentor->role) }} · {{ $mentor->level }} · {{ $mentor->department }}</div>
      <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:8px">
        @foreach($mentor->hasSkills->take(4) as $s)
        <span class="badge badge-blue">{{ $s->name }}</span>
        @endforeach
      </div>
    </div>

    <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;flex-shrink:0">
      @if($m->status === 'active')
        <span style="background:#d1fae5;color:#065f46;border-radius:6px;padding:3px 10px;font-size:0.75rem;font-weight:700">Active</span>
        @if($m->conversation)
        <a href="{{ route('chat.show', $m->conversation) }}" class="btn btn-primary btn-sm">Message</a>
        @endif
      @elseif($m->status === 'pending')
        <span style="background:#fef3c7;color:#92400e;border-radius:6px;padding:3px 10px;font-size:0.75rem;font-weight:700">Pending</span>
        <span style="font-size:0.75rem;color:var(--text-3)">Awaiting response</span>
      @elseif($m->status === 'rejected')
        <span style="background:#fee2e2;color:#991b1b;border-radius:6px;padding:3px 10px;font-size:0.75rem;font-weight:700">Declined</span>
      @else
        <span style="background:var(--surface-2);color:var(--text-3);border-radius:6px;padding:3px 10px;font-size:0.75rem;font-weight:700">{{ ucfirst($m->status) }}</span>
      @endif
    </div>
  </div>

  <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--border);display:flex;gap:24px;font-size:0.8rem;color:var(--text-3)">
    <span>Topic: <strong style="color:var(--text-1)">{{ $m->topic }}</strong></span>
    @if($m->started_at)<span>Started: <strong style="color:var(--text-1)">{{ $m->started_at->format('M d, Y') }}</strong></span>@endif
  </div>

  @if($m->status === 'active')
  @php $myRating = $m->ratings->first(); @endphp
  <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--border)">
    @if($myRating)
    <div style="font-size:0.82rem;color:var(--text-3)">
      Your rating: <span style="color:#f59e0b;font-size:1rem">{{ str_repeat('★', $myRating->score) }}{{ str_repeat('☆', 5 - $myRating->score) }}</span>
      @if($myRating->review) — <em>{{ $myRating->review }}</em>@endif
      <span style="margin-left:8px;color:var(--blue-500);cursor:pointer;font-size:0.78rem" onclick="toggleRateForm('rate-{{ $m->id }}')">Edit</span>
    </div>
    @else
    <div style="font-size:0.82rem;font-weight:600;color:var(--text-3);margin-bottom:8px">Rate this mentor</div>
    @endif
    <form id="rate-{{ $m->id }}" method="POST" action="{{ route('mentors.rate', $m) }}"
          style="{{ $myRating ? 'display:none' : '' }}margin-top:8px">
      @csrf
      <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap">
        <div class="star-group" data-form="rate-{{ $m->id }}" style="display:flex;gap:4px;font-size:1.5rem;cursor:pointer">
          @for($i = 1; $i <= 5; $i++)
          <span class="star" data-val="{{ $i }}" style="color:{{ $myRating && $myRating->score >= $i ? '#f59e0b' : '#d1d5db' }}">★</span>
          @endfor
          <input type="hidden" name="score" value="{{ $myRating->score ?? '' }}">
        </div>
        <input type="text" name="review" class="form-input" placeholder="Optional review..." value="{{ $myRating->review ?? '' }}"
               style="flex:1;min-width:160px;font-size:0.82rem">
        <button type="submit" class="btn btn-primary btn-sm">Submit</button>
      </div>
    </form>
  </div>
  @endif
</div>
@empty
<div class="card" style="text-align:center;padding:40px 20px;color:var(--text-3)">
  <div style="font-weight:700;margin-bottom:6px">No mentors yet</div>
  <p style="font-size:0.88rem;margin-bottom:20px">Browse available mentors and send a request to get started.</p>
  <a href="{{ route('mentors.index') }}" class="btn btn-primary">Find a Mentor</a>
</div>
@endforelse

@push('scripts')
<script>
function toggleRateForm(id) {
  const f = document.getElementById(id);
  f.style.display = f.style.display === 'none' ? 'block' : 'none';
}

document.querySelectorAll('.star-group').forEach(function(group) {
  const stars = group.querySelectorAll('.star');
  const input = group.querySelector('input[name="score"]');

  stars.forEach(function(star) {
    star.addEventListener('mouseenter', function() {
      const val = parseInt(this.dataset.val);
      stars.forEach(function(s) {
        s.style.color = parseInt(s.dataset.val) <= val ? '#f59e0b' : '#d1d5db';
      });
    });
    star.addEventListener('mouseleave', function() {
      const selected = parseInt(input.value) || 0;
      stars.forEach(function(s) {
        s.style.color = parseInt(s.dataset.val) <= selected ? '#f59e0b' : '#d1d5db';
      });
    });
    star.addEventListener('click', function() {
      input.value = this.dataset.val;
      const val = parseInt(this.dataset.val);
      stars.forEach(function(s) {
        s.style.color = parseInt(s.dataset.val) <= val ? '#f59e0b' : '#d1d5db';
      });
    });
  });
});
</script>
@endpush
@endsection
