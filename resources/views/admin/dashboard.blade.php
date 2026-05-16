@extends('layouts.app')
@section('title','Admin Dashboard')

@section('content')
<div class="app-layout">
  <aside class="sidebar">
    <div class="sidebar-label">Admin</div>
    <a href="{{ route('admin.dashboard') }}" class="sidebar-link active">Overview</a>
    <a href="{{ route('admin.users') }}"     class="sidebar-link">Users</a>
    <a href="{{ route('sessions.index') }}"  class="sidebar-link">Mentorships</a>
    <a href="{{ route('certificates.index') }}" class="sidebar-link">Certificates</a>
    <div class="sidebar-label">Moderation</div>
    <a href="{{ route('verifier.index') }}"  class="sidebar-link">Approvals <span class="count">{{ $pendingMentors->count() }}</span></a>
    <a href="{{ route('profile.edit') }}"    class="sidebar-link">Settings</a>
    <form method="POST" action="{{ route('logout') }}" style="margin-top:auto">
      @csrf<button type="submit" class="sidebar-link" style="width:100%;text-align:left;background:none;border:none;cursor:pointer">Sign Out</button>
    </form>
  </aside>
  <main class="main-content">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;flex-wrap:wrap;gap:16px">
      <div>
        <h1 style="font-size:1.6rem;font-weight:800;margin-bottom:4px">Admin Dashboard</h1>
        <p style="color:var(--text-3);font-size:0.9rem">PAAUMENTOR · Department of Computer Science, PAAU</p>
      </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:28px">
      <div class="stat-card"><div class="stat-icon" style="background:#dbeafe"></div><div class="stat-value">{{ $stats['users'] }}</div><div class="stat-label">Total Users</div></div>
      <div class="stat-card"><div class="stat-icon" style="background:#d1fae5"></div><div class="stat-value">{{ $stats['mentorships'] }}</div><div class="stat-label">Active Mentorships</div></div>
      <div class="stat-card"><div class="stat-icon" style="background:#fef3c7"></div><div class="stat-value">{{ $stats['sessions'] }}</div><div class="stat-label">Sessions Done</div></div>
      <div class="stat-card"><div class="stat-icon" style="background:#ede9fe"></div><div class="stat-value">{{ $stats['certificates'] }}</div><div class="stat-label">Certificates</div></div>
    </div>

    @if($pendingMentors->count())
    <div style="background:#fef9ec;border:1px solid #fcd34d;border-radius:14px;padding:16px 20px;margin-bottom:20px;display:flex;align-items:center;gap:12px">
      <span style="font-size:1.3rem"></span>
      <div style="flex:1"><div style="font-weight:700;font-size:0.9rem">{{ $pendingMentors->count() }} mentor applications awaiting approval</div></div>
      <a href="{{ route('admin.users') }}?status=pending" class="btn btn-sm btn-gold">Review Now</a>
    </div>
    @endif

    <div class="card" style="margin-bottom:24px">
      <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1rem;margin-bottom:16px;display:flex;justify-content:space-between">
         Recent Users <a href="{{ route('admin.users') }}" style="font-size:0.8rem;font-weight:500;color:var(--blue-500)">View All </a>
      </div>
      <div class="table-wrap">
        <table>
          <thead><tr><th>User</th><th>Role</th><th>Level</th><th>Verified</th><th>Actions</th></tr></thead>
          <tbody>
            @foreach($recentUsers as $u)
            <tr>
              <td><div style="display:flex;align-items:center;gap:10px"><div class="avatar avatar-sm">{{ $u->initials }}</div><div><div style="font-weight:700">{{ $u->full_name }}</div><div style="font-size:0.75rem;color:var(--text-3)">{{ $u->email }}</div></div></div></td>
              <td><span class="badge badge-blue">{{ ucfirst($u->role) }}</span></td>
              <td>{{ $u->level }}</td>
              <td>@if($u->is_verified)<span class="badge badge-green"> Verified</span>@else<span class="badge badge-gray">Pending</span>@endif</td>
              <td>
                <div style="display:flex;gap:6px">
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
    </div>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;margin-bottom:24px">
      <div class="card">
        <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1rem;margin-bottom:16px"> Monthly Sessions</div>
        <canvas id="sessionsChart" width="600" height="200" style="max-width:100%"></canvas>
      </div>
      <div class="card">
        <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1rem;margin-bottom:16px"> User Roles</div>
        <canvas id="rolesChart" width="260" height="200" style="max-width:100%;display:block;margin:0 auto"></canvas>
        @php $roleColors = ['mentee'=>'#2563eb','mentor'=>'#f5a623','alumni'=>'#10b981','admin'=>'#ef4444','verifier'=>'#8b5cf6']; @endphp
        <div style="margin-top:12px;font-size:0.82rem;display:flex;flex-direction:column;gap:6px">
          @foreach($roleData as $role => $count)
          <div style="display:flex;align-items:center;gap:8px">
            <span style="width:12px;height:12px;border-radius:3px;background:{{ $roleColors[$role] ?? '#64748b' }};display:inline-block"></span>
            {{ ucfirst($role) }} — {{ $count }}
          </div>
          @endforeach
        </div>
      </div>
    </div>

    <div class="card">
      <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1rem;margin-bottom:16px"> Top Demanded Skills</div>
      <canvas id="skillsChart" width="900" height="220" style="max-width:100%"></canvas>
    </div>
  </main>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  drawBarChart('sessionsChart', @json($monthlyData->keys()), @json($monthlyData->values()), '#2563eb');

  const roleColors = { mentee:'#2563eb', mentor:'#f5a623', alumni:'#10b981', admin:'#ef4444', verifier:'#8b5cf6' };
  const roles = @json($roleData);
  const segments = Object.entries(roles).map(([r,v]) => ({ value:v, color: roleColors[r] || '#64748b' }));
  drawDonutChart('rolesChart', segments);

  const skills = @json($topSkills->pluck('total','name'));
  drawBarChart('skillsChart', Object.keys(skills), Object.values(skills), '#f97316');
});
</script>
@endpush
