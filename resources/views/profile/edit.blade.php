@extends('layouts.sidebar')
@section('title', 'Edit Profile')

@section('page-content')
<h1 style="font-size:1.4rem;font-weight:800;margin-bottom:20px">Edit Profile</h1>

<div class="card">
  <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
    @csrf
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
      <div class="form-group"><label class="form-label">First Name</label><input type="text" name="first_name" class="form-input" value="{{ old('first_name', $user->first_name) }}" required></div>
      <div class="form-group"><label class="form-label">Last Name</label><input type="text" name="last_name" class="form-input" value="{{ old('last_name', $user->last_name) }}" required></div>
    </div>
    <div class="form-group"><label class="form-label">Bio</label><textarea name="bio" class="form-input" rows="3">{{ old('bio', $user->bio) }}</textarea></div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
      <div class="form-group"><label class="form-label">Phone</label><input type="text" name="phone" class="form-input" value="{{ old('phone', $user->phone) }}"></div>
      <div class="form-group"><label class="form-label">Availability</label><input type="text" name="availability" class="form-input" placeholder="e.g. Weekdays 4-6pm" value="{{ old('availability', $user->availability) }}"></div>
    </div>
    <div class="form-group">
      <label class="form-label">Profile Photo</label>
      @if($user->avatar_url)
      <div style="margin-bottom:8px"><img src="{{ $user->avatar_url }}" alt="Current photo" style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:2px solid var(--border)"></div>
      @endif
      <input type="file" name="avatar" class="form-input" accept="image/*">
    </div>

    <div class="form-group">
      <label class="form-label">My Skills</label>
      <div style="display:flex;flex-wrap:wrap;gap:8px;padding:12px;border:1.5px solid var(--border);border-radius:10px">
        @foreach($skills as $skill)
        <label style="display:flex;align-items:center;gap:6px;font-size:0.85rem;cursor:pointer">
          <input type="checkbox" name="skill_ids[]" value="{{ $skill->id }}"
                 {{ $user->hasSkills->contains($skill->id) ? 'checked' : '' }}
                 style="accent-color:var(--blue-500)">
          {{ $skill->name }}
        </label>
        @endforeach
      </div>
    </div>

    @if($errors->any())
    <div style="background:#fee2e2;border-radius:10px;padding:12px;margin-bottom:16px;font-size:0.85rem;color:#991b1b">
      @foreach($errors->all() as $e)<div> {{ $e }}</div>@endforeach
    </div>
    @endif

    @if(session('success'))
    <div style="background:#dcfce7;border:1px solid #86efac;border-radius:10px;padding:12px;margin-bottom:16px;font-size:0.85rem;color:#166534">
      {{ session('success') }}
    </div>
    @endif

    <button type="submit" class="btn btn-primary">Save Changes</button>
  </form>
</div>

{{-- Change Password --}}
<div class="card" style="margin-top:20px">
  <h2 style="font-size:1rem;font-weight:700;margin-bottom:20px">Change Password</h2>

  @if(session('password_success'))
  <div style="background:#dcfce7;border:1px solid #86efac;border-radius:10px;padding:12px;margin-bottom:16px;font-size:0.85rem;color:#166534">
    {{ session('password_success') }}
  </div>
  @endif

  <form method="POST" action="{{ route('profile.password') }}" style="display:flex;flex-direction:column;gap:14px">
    @csrf
    <div class="form-group">
      <label class="form-label">Current Password</label>
      <input type="password" name="current_password" class="form-input" placeholder="Enter your current password" required>
    </div>
    <div class="form-group">
      <label class="form-label">New Password</label>
      <input type="password" name="password" class="form-input" placeholder="Minimum 8 characters, letters and numbers" required>
    </div>
    <div class="form-group">
      <label class="form-label">Confirm New Password</label>
      <input type="password" name="password_confirmation" class="form-input" placeholder="Repeat new password" required>
    </div>
    <button type="submit" class="btn btn-primary" style="align-self:flex-start">Update Password</button>
  </form>
</div>
@endsection
