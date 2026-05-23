@extends('layouts.app')
@section('title','Manage Users — Admin')

@section('content')
<div class="app-layout">
  <aside class="sidebar">
    <div class="sidebar-label">Admin</div>
    <a href="{{ route('admin.dashboard') }}" class="sidebar-link"><span class="icon"></span> Overview</a>
    <a href="{{ route('admin.users') }}"     class="sidebar-link active"><span class="icon"></span> Users</a>
    <form method="POST" action="{{ route('logout') }}" style="margin-top:auto">
      @csrf<button type="submit" class="sidebar-link" style="width:100%;text-align:left;background:none;border:none;cursor:pointer"><span class="icon"></span> Sign Out</button>
    </form>
  </aside>
  <main class="main-content">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px">
      <h1 style="font-size:1.6rem;font-weight:800;margin:0">All Users</h1>
      <button onclick="document.getElementById('verifierModal').style.display='flex'" class="btn btn-primary btn-sm">+ Add Verifier</button>
    </div>

    @if(session('success'))
    <div style="background:#dcfce7;border:1px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:0.85rem;color:#166534">
      {{ session('success') }}
    </div>
    @endif

    {{-- Add Verifier Modal --}}
    <div id="verifierModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center;padding:20px">
      <div style="background:var(--bg);border-radius:20px;padding:36px;width:100%;max-width:480px;box-shadow:0 24px 60px rgba(0,0,0,0.3)">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
          <h2 style="font-size:1.2rem;font-weight:800;margin:0">Create Verifier Account</h2>
          <button onclick="document.getElementById('verifierModal').style.display='none'" style="background:none;border:none;font-size:1.4rem;cursor:pointer;color:var(--text-3);line-height:1">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.createVerifier') }}" style="display:flex;flex-direction:column;gap:14px">
          @csrf
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div class="form-group" style="margin:0">
              <label class="form-label">First Name</label>
              <input type="text" name="first_name" class="form-input" placeholder="e.g. Fatima" required value="{{ old('first_name') }}">
            </div>
            <div class="form-group" style="margin:0">
              <label class="form-label">Last Name</label>
              <input type="text" name="last_name" class="form-input" placeholder="e.g. Bello" required value="{{ old('last_name') }}">
            </div>
          </div>
          <div class="form-group" style="margin:0">
            <label class="form-label">Institutional Email</label>
            <input type="email" name="email" class="form-input" placeholder="verifier@paau.edu.ng" required value="{{ old('email') }}">
          </div>
          <div class="form-group" style="margin:0">
            <label class="form-label">Staff ID</label>
            <input type="text" name="staff_id" class="form-input" placeholder="e.g. STAFF001" required value="{{ old('staff_id') }}">
          </div>
          <div class="form-group" style="margin:0">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-input" placeholder="Minimum 8 characters" required>
          </div>
          <div class="form-group" style="margin:0">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-input" placeholder="Repeat password" required>
          </div>
          @if($errors->any())
          <div style="background:#fee2e2;border-radius:8px;padding:10px 14px;font-size:0.82rem;color:#991b1b">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
          </div>
          @endif
          <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:4px">
            <button type="button" onclick="document.getElementById('verifierModal').style.display='none'" class="btn btn-outline btn-sm">Cancel</button>
            <button type="submit" class="btn btn-primary btn-sm">Create Account</button>
          </div>
        </form>
      </div>
    </div>

    <form method="GET" style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap">
      <input type="text" name="search" class="form-input" placeholder="Search..." value="{{ request('search') }}" style="width:200px">
      <select name="role" class="form-select" style="width:auto">
        <option value="">All Roles</option>
        @foreach(['mentee','mentor','alumni','admin','verifier'] as $r)
          <option value="{{ $r }}" {{ request('role')===$r?'selected':'' }}>{{ ucfirst($r) }}</option>
        @endforeach
      </select>
      <button type="submit" class="btn btn-primary btn-sm">Filter</button>
      <a href="{{ route('admin.users') }}" class="btn btn-outline btn-sm">Clear</a>
    </form>

    <div class="card">
      <div class="table-wrap">
        <table>
          <thead><tr><th>User</th><th>Student ID</th><th>Role</th><th>Level</th><th>Verified</th><th>Active</th><th>Actions</th></tr></thead>
          <tbody>
            @foreach($users as $u)
            <tr>
              <td><div style="display:flex;align-items:center;gap:10px"><div class="avatar avatar-sm">{{ $u->initials }}</div><div><div style="font-weight:700">{{ $u->full_name }}</div><div style="font-size:0.75rem;color:var(--text-3)">{{ $u->email }}</div></div></div></td>
              <td style="font-size:0.82rem">{{ $u->student_id }}</td>
              <td><span class="badge badge-blue">{{ ucfirst($u->role) }}</span></td>
              <td>{{ $u->level }}</td>
              <td>@if($u->is_verified)<span class="badge badge-green"></span>@else<span class="badge badge-gray">Pending</span>@endif</td>
              <td>@if($u->is_active)<span class="badge badge-green">Active</span>@else<span class="badge badge-gray">Suspended</span>@endif</td>
              <td>
                <div style="display:flex;gap:6px;flex-wrap:wrap">
                  @if(!$u->is_verified)
                  <form method="POST" action="{{ route('admin.verify', $u) }}">@csrf @method('PATCH')<button type="submit" class="btn btn-sm btn-success">Verify</button></form>
                  @endif
                  <form method="POST" action="{{ route('admin.suspend', $u) }}">@csrf @method('PATCH')<button type="submit" class="btn btn-sm btn-danger">{{ $u->is_active ? 'Suspend' : 'Activate' }}</button></form>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div style="padding:16px">{{ $users->withQueryString()->links() }}</div>
    </div>
  </main>
</div>
@if($errors->any())
<script>document.getElementById('verifierModal').style.display = 'flex';</script>
@endif
@endsection
