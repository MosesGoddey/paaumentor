@extends('layouts.app')

@section('content')
<div class="app-layout">
@php
  $authUser  = auth()->user();
  $userRole  = $authUser->role;
  $isMentor  = $userRole === 'mentor';
  $isAlumni  = $userRole === 'alumni';
  $isVerifier = $authUser->isVerifier();
  $isAdmin   = $authUser->isAdmin();
  $isPending = $authUser->isPendingVerification();

  $accentColor = match($userRole) {
      'mentor'   => '#f97316',
      'alumni'   => '#8b5cf6',
      'admin'    => '#0f172a',
      'verifier' => '#0d9488',
      default    => '#2563eb',
  };
@endphp
  <aside class="sidebar" style="border-left:4px solid {{ $accentColor }}">

    @if($isPending)
    <div style="margin:-20px -12px 12px;padding:10px 16px;background:#fef3c7;border-bottom:1px solid #fde68a;font-size:0.72rem;color:#92400e;font-weight:600">
      Pending portfolio verification — you'll be notified once approved.
    </div>
    @endif

<div style="flex:1;overflow-y:auto;padding-right:2px;overscroll-behavior:contain">
    <div class="sidebar-label">Main</div>
    <a href="{{ route('dashboard') }}"      class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
    @if($authUser->isMentee())
    <a href="{{ route('mentors.index') }}" class="sidebar-link {{ request()->routeIs('mentors.index') ? 'active' : '' }}">Find Mentor</a>
    <a href="{{ route('mentors.my') }}" class="sidebar-link {{ request()->routeIs('mentors.my') ? 'active' : '' }}">My Mentors
      @php $activeMentors = $authUser->menteeMentorships()->where('status','active')->count(); @endphp
      @if($activeMentors) <span class="count">{{ $activeMentors }}</span> @endif
    </a>
    @elseif($isMentor || $isAlumni)
    <a href="{{ route('mentors.mentees') }}" class="sidebar-link {{ request()->routeIs('mentors.mentees') ? 'active' : '' }}">My Mentees
      @php $activeMentees = $authUser->mentorMentorships()->where('status','active')->count(); @endphp
      @if($activeMentees) <span class="count">{{ $activeMentees }}</span> @endif
    </a>
    @endif
    <a href="{{ route('learning.index') }}" class="sidebar-link {{ request()->routeIs('learning.*') ? 'active' : '' }}">Learning Paths</a>
    <a href="{{ route('chat.index') }}"     class="sidebar-link {{ request()->routeIs('chat.*') ? 'active' : '' }}">Messages
      @php
        $unread = \App\Models\Message::where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->whereHas('conversation', function ($q) {
                $uid = auth()->id();
                $q->whereHas('mentorship', fn($q2) => $q2->where('mentor_id', $uid)->orWhere('mentee_id', $uid))
                  ->orWhereHas('skillExchangeRequest', fn($q2) =>
                      $q2->where('requester_id', $uid)
                         ->orWhereHas('exchange', fn($q3) => $q3->where('user_id', $uid)));
            })
            ->count();
      @endphp
      @if($unread) <span class="count">{{ $unread }}</span> @endif
    </a>
    <div class="sidebar-label">Collaborate</div>
    <a href="{{ route('hackathons.index') }}" class="sidebar-link {{ request()->routeIs('hackathons.*') ? 'active' : '' }}">Hackathons</a>
    <a href="{{ route('study-groups.index') }}"   class="sidebar-link {{ request()->routeIs('study-groups.*') ? 'active' : '' }}">Study Groups</a>
    <a href="{{ route('skill-exchange.index') }}" class="sidebar-link {{ request()->routeIs('skill-exchange.*') ? 'active' : '' }}">Skill Exchange</a>
    <a href="{{ route('resources.index') }}"      class="sidebar-link {{ request()->routeIs('resources.*') ? 'active' : '' }}">Resources</a>
    <a href="{{ route('sessions.index') }}"       class="sidebar-link {{ request()->routeIs('sessions.*') ? 'active' : '' }}">Sessions</a>
    <div class="sidebar-label">AI Tools</div>
    <a href="{{ route('ai.assistant') }}" class="sidebar-link {{ request()->routeIs('ai.*') ? 'active' : '' }}">Study Buddy</a>
    <div class="sidebar-label">My Account</div>
    <a href="{{ route('certificates.index') }}" class="sidebar-link {{ request()->routeIs('certificates.*') ? 'active' : '' }}">Certificates</a>
    @if($authUser->isMentee())
    <a href="{{ route('upgrade.show') }}" class="sidebar-link {{ request()->routeIs('upgrade.*') ? 'active' : '' }}" style="{{ request()->routeIs('upgrade.*') ? '' : 'color:#ea580c' }}">Upgrade to Mentor</a>
    @endif
    @if($isAdmin || $isVerifier)
    <div class="sidebar-label">Verification</div>
    <a href="{{ route('verifier.index') }}" class="sidebar-link {{ request()->routeIs('verifier.*') ? 'active' : '' }}">Mentor Portfolios
      @php $pendingCount = \App\Models\User::whereIn('role',['mentor','alumni'])->where('mentor_status','pending')->count(); @endphp
      @if($pendingCount) <span class="count">{{ $pendingCount }}</span> @endif
    </a>
    <a href="{{ route('upgrade.admin') }}" class="sidebar-link {{ request()->routeIs('upgrade.admin') ? 'active' : '' }}">Upgrade Requests</a>
    @endif
    <a href="{{ route('profile.edit') }}" class="sidebar-link">Settings</a>
    </div>{{-- end scrollable nav --}}
    <a href="{{ route('profile.show', auth()->user()) }}" style="padding:8px 12px;border-top:1px solid var(--border);display:flex;align-items:center;gap:10px;text-decoration:none;transition:background 0.15s;border-radius:0" onmouseover="this.style.background='var(--surface-2)'" onmouseout="this.style.background='transparent'">
      @if(auth()->user()->avatar_url)
      <img src="{{ auth()->user()->avatar_url }}" alt="" style="width:32px;height:32px;border-radius:50%;object-fit:cover;flex-shrink:0">
      @else
      <div class="avatar" style="width:32px;height:32px;font-size:0.78rem;flex-shrink:0">{{ auth()->user()->initials }}</div>
      @endif
      <div style="min-width:0">
        <div style="font-weight:700;font-size:0.82rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:var(--text)">{{ auth()->user()->full_name }}</div>
        <span style="background:var(--blue-500);color:#fff;border-radius:4px;padding:1px 6px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em">{{ auth()->user()->role }}</span>
      </div>
    </a>
    <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Are you sure you want to sign out?')">
      @csrf
      <button type="submit" class="sidebar-link" style="width:100%;text-align:left;background:#fecdd3;border:none;cursor:pointer;color:#9f1239;font-weight:600;border-radius:10px">Sign Out</button>
    </form>
  </aside>

  {{-- Mobile sidebar overlay --}}
  <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

  <main class="main-content">
    @hasSection('breadcrumbs')
    <nav style="display:flex;align-items:center;gap:6px;font-size:0.78rem;color:var(--text-3);margin-bottom:20px;flex-wrap:wrap">
      <a href="{{ route('dashboard') }}" style="color:var(--text-3);text-decoration:none;transition:color 0.15s" onmouseover="this.style.color='var(--blue-500)'" onmouseout="this.style.color='var(--text-3)'">Home</a>
      @yield('breadcrumbs')
    </nav>
    @endif
    @yield('page-content')
  </main>
</div>

@include('partials.mentor-ai-widget')
@endsection
