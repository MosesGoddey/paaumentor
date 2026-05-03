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
      <h1 style="font-size:1.4rem;font-weight:800;margin-bottom:4px">{{ $user->full_name }}</h1>
      <p style="color:var(--text-3);font-size:0.88rem">{{ ucfirst($user->role) }} · {{ $user->level }} · {{ $user->department }}</p>
      @if($user->student_id)<p style="font-size:0.82rem;color:var(--text-3)">ID: {{ $user->student_id }}</p>@endif
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
