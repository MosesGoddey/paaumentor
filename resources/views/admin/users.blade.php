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
    <h1 style="font-size:1.6rem;font-weight:800;margin-bottom:20px">All Users</h1>

    <form method="GET" style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap">
      <input type="text" name="search" class="form-input" placeholder="Search..." value="{{ request('search') }}" style="width:200px">
      <select name="role" class="form-select" style="width:auto">
        <option value="">All Roles</option>
        @foreach(['mentee','mentor','alumni','admin'] as $r)
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
@endsection
