@extends('layouts.sidebar')
@section('title', $user->full_name)

@section('page-content')
<div class="card">
  <div style="display:flex;align-items:center;gap:20px;margin-bottom:20px">
    @if($user->avatar_url)
    <img src="{{ $user->avatar_url }}" alt="{{ $user->full_name }}" class="avatar avatar-xl" style="object-fit:cover;">
    @else
    <div class="avatar avatar-xl">{{ $user->initials }}</div>
    @endif
    <div>
      <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:4px">
        <h1 style="font-size:1.4rem;font-weight:800;margin:0">{{ $user->full_name }}</h1>
        @if($user->isMentor())
        @php $tier = $user->mentor_tier; @endphp
        <span style="font-size:0.7rem;font-weight:700;padding:3px 10px;border-radius:99px;letter-spacing:0.05em;
          background:{{ $tier==='lead' ? '#fef3c7' : ($tier==='senior' ? '#ede9fe' : '#dbeafe') }};
          color:{{ $tier==='lead' ? '#92400e' : ($tier==='senior' ? '#5b21b6' : '#1d4ed8') }}">
          {{ $user->mentor_tier_icon }} {{ $user->mentor_tier_label }}
        </span>
        @endif
      </div>
      <p style="color:var(--text-3);font-size:0.88rem">{{ ucfirst($user->role) }} · {{ $user->level }} · {{ $user->department }}</p>
      @if($user->student_id)<p style="font-size:0.82rem;color:var(--text-3)">ID: {{ $user->student_id }}</p>@endif
      @if($user->isMentor())
      <div style="display:flex;align-items:center;gap:8px;margin-top:6px">
        <span style="color:#f59e0b;font-size:1rem;letter-spacing:1px">
          @for($s = 1; $s <= 5; $s++){{ $s <= round($user->average_rating) ? '★' : '☆' }}@endfor
        </span>
        <span style="font-size:0.8rem;color:var(--text-3)">
          {{ $user->average_rating > 0 ? number_format($user->average_rating, 1).' / 5 · '.$user->ratings()->count().' rating(s)' : 'No ratings yet' }}
        </span>
      </div>
      @endif
    </div>
    @if(auth()->id() === $user->id)
    <a href="{{ route('profile.edit') }}" class="btn btn-outline btn-sm" style="margin-left:auto">Edit Profile</a>
    @endif
  </div>
  @if($user->bio)<p style="font-size:0.9rem;line-height:1.7;color:var(--text-2);margin-bottom:16px">{{ $user->bio }}</p>@endif
  <div style="display:flex;flex-wrap:wrap;gap:6px">
    @foreach($user->hasSkills as $s)<span class="badge badge-blue">{{ $s->name }}</span>@endforeach
  </div>
</div>
@endsection
