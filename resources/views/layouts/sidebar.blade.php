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

  // Sidebar accent colours per role
  $accentColor  = match($userRole) {
      'mentor'   => '#f97316',
      'alumni'   => '#8b5cf6',
      'admin'    => '#0f172a',
      'verifier' => '#0d9488',
      default    => '#2563eb',
  };
  $cardBg = match($userRole) {
      'mentor'   => 'linear-gradient(135deg,#fff7ed,#ffedd5)',
      'alumni'   => 'linear-gradient(135deg,#f5f3ff,#ede9fe)',
      'admin'    => 'linear-gradient(135deg,#f8fafc,#e2e8f0)',
      'verifier' => 'linear-gradient(135deg,#f0fdfa,#ccfbf1)',
      default    => 'linear-gradient(135deg,#eff6ff,#dbeafe)',
  };
  $cardBorder = match($userRole) {
      'mentor'   => '#fed7aa',
      'alumni'   => '#ddd6fe',
      'admin'    => '#cbd5e1',
      'verifier' => '#99f6e4',
      default    => '#bfdbfe',
  };
  $roleLabel = match($userRole) {
      'mentor'   => 'MENTOR',
      'alumni'   => 'ALUMNI',
      'admin'    => 'ADMIN',
      'verifier' => 'VERIFIER',
      default    => 'MENTEE',
  };
  $roleEmoji = match($userRole) {
      'mentor'   => '👨‍🏫',
      'alumni'   => '🎓',
      'admin'    => '🛡️',
      'verifier' => '🔍',
      default    => '🎓',
  };
  $roleSub = match($userRole) {
      'mentor'   => 'Guide & Inspire',
      'alumni'   => 'Give Back & Lead',
      'admin'    => 'System Administrator',
      'verifier' => 'Quality & Verification',
      default    => 'Learn & Grow',
  };
@endphp
  <aside class="sidebar" style="border-left:4px solid {{ $accentColor }}">

    {{-- Role identity card --}}
    <div style="margin:-20px -12px 16px;padding:18px 16px;background:{{ $cardBg }};border-bottom:2px solid {{ $cardBorder }}">
      <div style="display:flex;align-items:center;gap:12px">
        <div style="font-size:2.2rem;line-height:1">{{ $roleEmoji }}</div>
        <div>
          <div style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:{{ $accentColor }}">You are a</div>
          <div style="font-size:1.25rem;font-weight:800;font-family:'Sora',sans-serif;color:{{ $accentColor }};line-height:1.2">{{ $roleLabel }}</div>
          <div style="font-size:0.7rem;color:{{ $accentColor }};opacity:0.8;margin-top:2px">{{ $roleSub }}</div>
        </div>
      </div>
      @if($isPending)
      <div style="margin-top:10px;padding:7px 10px;background:#fef3c7;border:1px solid #fde68a;border-radius:8px;font-size:0.72rem;color:#92400e;font-weight:600">
        ⏳ Pending portfolio verification — you'll be notified once approved.
      </div>
      @endif
    </div>

    <div class="sidebar-label">Main</div>
    <a href="{{ route('dashboard') }}"      class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><span class="icon">🏠</span> Dashboard</a>
    @if($authUser->isMentee())
    <a href="{{ route('mentors.index') }}" class="sidebar-link {{ request()->routeIs('mentors.index') ? 'active' : '' }}"><span class="icon">🔍</span> Find Mentor</a>
    <a href="{{ route('mentors.my') }}" class="sidebar-link {{ request()->routeIs('mentors.my') ? 'active' : '' }}"><span class="icon">👥</span> My Mentors
      @php $activeMentors = $authUser->menteeMentorships()->where('status','active')->count(); @endphp
      @if($activeMentors) <span class="count">{{ $activeMentors }}</span> @endif
    </a>
    @elseif($isMentor || $isAlumni)
    <a href="{{ route('mentors.mentees') }}" class="sidebar-link {{ request()->routeIs('mentors.mentees') ? 'active' : '' }}"><span class="icon">🎓</span> My Mentees
      @php $activeMentees = $authUser->mentorMentorships()->where('status','active')->count(); @endphp
      @if($activeMentees) <span class="count">{{ $activeMentees }}</span> @endif
    </a>
    @endif
    <a href="{{ route('learning.index') }}" class="sidebar-link {{ request()->routeIs('learning.*') ? 'active' : '' }}"><span class="icon">📚</span> Learning Paths</a>
    <a href="{{ route('chat.index') }}"     class="sidebar-link {{ request()->routeIs('chat.*') ? 'active' : '' }}"><span class="icon">💬</span> Messages
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
    <a href="{{ route('study-groups.index') }}"   class="sidebar-link {{ request()->routeIs('study-groups.*') ? 'active' : '' }}"><span class="icon">🧑‍🤝‍🧑</span> Study Groups</a>
    <a href="{{ route('skill-exchange.index') }}" class="sidebar-link {{ request()->routeIs('skill-exchange.*') ? 'active' : '' }}"><span class="icon">🔄</span> Skill Exchange</a>
    <a href="{{ route('resources.index') }}"      class="sidebar-link {{ request()->routeIs('resources.*') ? 'active' : '' }}"><span class="icon">📁</span> Resources</a>
    <a href="{{ route('sessions.index') }}"       class="sidebar-link {{ request()->routeIs('sessions.*') ? 'active' : '' }}"><span class="icon">📅</span> Sessions</a>
    <div class="sidebar-label">AI Tools</div>
    <a href="{{ route('ai.assistant') }}" class="sidebar-link {{ request()->routeIs('ai.*') ? 'active' : '' }}"><span class="icon">✨</span> Study Buddy</a>
    <div class="sidebar-label">My Account</div>
    <a href="{{ route('certificates.index') }}" class="sidebar-link {{ request()->routeIs('certificates.*') ? 'active' : '' }}"><span class="icon">🏆</span> Certificates</a>
    @if($authUser->isMentee())
    <a href="{{ route('upgrade.show') }}" class="sidebar-link {{ request()->routeIs('upgrade.*') ? 'active' : '' }}" style="{{ request()->routeIs('upgrade.*') ? '' : 'color:#ea580c' }}"><span class="icon">⬆️</span> Upgrade to Mentor</a>
    @endif
    @if($isAdmin || $isVerifier)
    <div class="sidebar-label">Verification</div>
    <a href="{{ route('verifier.index') }}" class="sidebar-link {{ request()->routeIs('verifier.*') ? 'active' : '' }}"><span class="icon">🔍</span> Mentor Portfolios
      @php $pendingCount = \App\Models\User::whereIn('role',['mentor','alumni'])->where('mentor_status','pending')->count(); @endphp
      @if($pendingCount) <span class="count">{{ $pendingCount }}</span> @endif
    </a>
    <a href="{{ route('upgrade.admin') }}" class="sidebar-link {{ request()->routeIs('upgrade.admin') ? 'active' : '' }}"><span class="icon">🛡️</span> Upgrade Requests</a>
    @endif
    <a href="{{ route('profile.edit') }}" class="sidebar-link"><span class="icon">⚙️</span> Settings</a>
    <div style="margin-top:auto;padding:12px;border-top:1px solid var(--border);display:flex;align-items:center;gap:10px">
      @if(auth()->user()->avatar_url)
      <img src="{{ auth()->user()->avatar_url }}" alt="" style="width:38px;height:38px;border-radius:50%;object-fit:cover;flex-shrink:0">
      @else
      <div class="avatar" style="width:38px;height:38px;font-size:0.85rem;flex-shrink:0">{{ auth()->user()->initials }}</div>
      @endif
      <div style="min-width:0">
        <div style="font-weight:700;font-size:0.82rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ auth()->user()->full_name }}</div>
        <span style="background:var(--blue-500);color:#fff;border-radius:4px;padding:1px 6px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em">{{ auth()->user()->role }}</span>
      </div>
    </div>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="sidebar-link" style="width:100%;text-align:left;background:none;border:none;cursor:pointer"><span class="icon">🚪</span> Sign Out</button>
    </form>
  </aside>

  {{-- Mobile sidebar overlay --}}
  <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

  <main class="main-content">
    @hasSection('breadcrumbs')
    <nav style="display:flex;align-items:center;gap:6px;font-size:0.78rem;color:var(--text-3);margin-bottom:20px;flex-wrap:wrap">
      <a href="{{ route('dashboard') }}" style="color:var(--text-3);text-decoration:none;transition:color 0.15s" onmouseover="this.style.color='var(--blue-500)'" onmouseout="this.style.color='var(--text-3)'">🏠 Home</a>
      @yield('breadcrumbs')
    </nav>
    @endif
    @yield('page-content')
  </main>
</div>
@endsection
