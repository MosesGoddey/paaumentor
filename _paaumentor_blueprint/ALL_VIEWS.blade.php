{{-- ============================================================
     PAAUMENTOR — Blade Views
     Place each file in resources/views/ as indicated
     ============================================================ --}}


{{-- ============================================================
FILE: resources/views/layouts/app.blade.php
Shared layout — all authenticated pages extend this
============================================================ --}}
{{-- SAVE AS: resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en" data-theme="{{ session('theme', 'light') }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'PAAUMENTOR') — Prince Abubakar Audu University</title>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
@stack('styles')
</head>
<body>

{{-- NAVBAR --}}
<nav class="navbar">
  <div class="container">
    <div class="nav-inner">
      <a href="{{ route('home') }}" class="nav-brand">
        <div class="nav-logo">PM</div>
        <span class="nav-brand-text">PAAU<span>MENTOR</span></span>
      </a>

      @auth
      <div class="nav-links">
        <a href="{{ route('dashboard') }}"      class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('mentors.index') }}"  class="{{ request()->routeIs('mentors.*') ? 'active' : '' }}">Find Mentor</a>
        <a href="{{ route('learning.index') }}" class="{{ request()->routeIs('learning.*') ? 'active' : '' }}">Learning Paths</a>
        <a href="{{ route('chat.index') }}"     class="{{ request()->routeIs('chat.*') ? 'active' : '' }}">Messages</a>
      </div>
      @endauth

      <div class="nav-actions">
        <button class="theme-toggle" id="themeToggle" onclick="toggleTheme()" title="Toggle theme">🌙</button>

        @auth
          {{-- Notifications bell --}}
          <a href="{{ route('notifications.index') }}" style="position:relative;font-size:1.3rem;color:var(--text-2)">
            🔔
            @if(auth()->user()->notifications()->whereNull('read_at')->count() > 0)
              <span style="position:absolute;top:-2px;right:-2px;width:8px;height:8px;background:var(--gold);border-radius:50%;display:block"></span>
            @endif
          </a>
          <div class="avatar avatar-sm" style="cursor:pointer;background:linear-gradient(135deg,#2563eb,#1a3a6e)"
               onclick="location.href='{{ route('profile.show', auth()->user()) }}'">
            {{ auth()->user()->initials }}
          </div>
        @else
          <a href="{{ route('login') }}"    class="btn btn-outline btn-sm">Sign In</a>
          <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Get Started</a>
        @endauth
      </div>
    </div>
  </div>
</nav>

{{-- Flash messages --}}
@if(session('success'))
  <div style="background:var(--success);color:#fff;padding:12px 24px;font-size:0.9rem;text-align:center">
    ✓ {{ session('success') }}
  </div>
@endif
@if(session('error'))
  <div style="background:var(--danger);color:#fff;padding:12px 24px;font-size:0.9rem;text-align:center">
    ✗ {{ session('error') }}
  </div>
@endif

@yield('content')

<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>


{{-- ============================================================
FILE: resources/views/layouts/sidebar.blade.php
Authenticated pages with sidebar extend this
============================================================ --}}
{{-- SAVE AS: resources/views/layouts/sidebar.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="app-layout">
  {{-- SIDEBAR --}}
  <aside class="sidebar">
    <div class="sidebar-label">Main</div>
    <a href="{{ route('dashboard') }}"      class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><span class="icon">🏠</span> Dashboard</a>
    <a href="{{ route('mentors.index') }}"  class="sidebar-link {{ request()->routeIs('mentors.*') ? 'active' : '' }}"><span class="icon">🔍</span> Find Mentor</a>
    <a href="#"                             class="sidebar-link"><span class="icon">🤝</span> My Mentors
      @php $activeMentors = auth()->user()->menteeMentorships()->where('status','active')->count(); @endphp
      @if($activeMentors) <span class="count">{{ $activeMentors }}</span> @endif
    </a>
    <a href="{{ route('learning.index') }}" class="sidebar-link {{ request()->routeIs('learning.*') ? 'active' : '' }}"><span class="icon">🗺️</span> Learning Paths</a>
    <a href="{{ route('chat.index') }}"     class="sidebar-link {{ request()->routeIs('chat.*') ? 'active' : '' }}"><span class="icon">💬</span> Messages
      @php $unread = \App\Models\Message::whereHas('conversation.mentorship', fn($q) => $q->where('mentor_id', auth()->id())->orWhere('mentee_id', auth()->id()))->where('sender_id','!=',auth()->id())->whereNull('read_at')->count(); @endphp
      @if($unread) <span class="count">{{ $unread }}</span> @endif
    </a>
    <div class="sidebar-label">Collaborate</div>
    <a href="#" class="sidebar-link"><span class="icon">👥</span> Study Groups</a>
    <a href="#" class="sidebar-link"><span class="icon">📁</span> Resources</a>
    <a href="#" class="sidebar-link"><span class="icon">🗓️</span> Sessions</a>
    <div class="sidebar-label">My Account</div>
    <a href="#" class="sidebar-link"><span class="icon">🏅</span> Certificates</a>
    <a href="{{ route('profile.edit') }}" class="sidebar-link"><span class="icon">⚙️</span> Settings</a>
    <form method="POST" action="{{ route('logout') }}" style="margin-top:auto">
      @csrf
      <button type="submit" class="sidebar-link" style="width:100%;text-align:left;background:none;border:none;cursor:pointer"><span class="icon">🚪</span> Sign Out</button>
    </form>
  </aside>

  <main class="main-content">
    @yield('page-content')
  </main>
</div>
@endsection


{{-- ============================================================
FILE: resources/views/welcome.blade.php  (Landing page)
============================================================ --}}
{{-- NOTE: Copy your index.html content here, replacing static links
     with Laravel route() calls. Key replacements:
       href="login.html"          → href="{{ route('login') }}"
       href="login.html#register" → href="{{ route('register') }}"
       href="dashboard.html"      → href="{{ route('dashboard') }}"
     This file is already largely static HTML — just drop it in.
--}}


{{-- ============================================================
FILE: resources/views/auth/login.blade.php
============================================================ --}}
{{-- SAVE AS: resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign In — PAAUMENTOR</title>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<style>
/* (Copy auth styles from login.html here — auth-page, auth-left, auth-right etc.) */
.auth-page{min-height:100vh;display:grid;grid-template-columns:1fr 1fr;}
.auth-left{background:linear-gradient(145deg,#0a1628,#1a3a6e);padding:60px 48px;display:flex;flex-direction:column;justify-content:space-between;position:relative;overflow:hidden;}
.auth-right{background:var(--bg);padding:60px 48px;display:flex;flex-direction:column;justify-content:center;overflow-y:auto;}
.auth-right-inner{max-width:420px;margin:0 auto;width:100%;}
.auth-tabs{display:flex;gap:0;background:var(--surface-2);border-radius:12px;padding:4px;margin-bottom:32px;}
.auth-tab{flex:1;padding:10px;border-radius:9px;border:none;background:transparent;cursor:pointer;font-family:'Sora',sans-serif;font-weight:600;font-size:0.88rem;color:var(--text-3);transition:all 0.2s;}
.auth-tab.active{background:var(--surface);color:var(--blue-500);box-shadow:0 2px 8px rgba(0,0,0,0.08);}
.input-wrap{position:relative;}
.input-wrap .form-input{padding-left:42px;}
.input-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);font-size:1rem;pointer-events:none;}
.role-select{display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;}
.role-option{display:none;}
.role-label{display:flex;flex-direction:column;align-items:center;gap:6px;padding:12px 8px;border-radius:12px;border:1.5px solid var(--border);cursor:pointer;transition:all 0.2s;font-size:0.8rem;font-weight:600;color:var(--text-3);text-align:center;}
.role-option:checked + .role-label{border-color:var(--blue-500);background:var(--blue-100);color:var(--blue-500);}
@media(max-width:900px){.auth-page{grid-template-columns:1fr;}.auth-left{display:none;}.auth-right{padding:40px 24px;}}
</style>
</head>
<body>
<div class="auth-page">
  {{-- LEFT BRANDING PANEL --}}
  <div class="auth-left">
    <div style="display:flex;align-items:center;gap:12px;position:relative;z-index:1">
      <div style="width:44px;height:44px;background:rgba(255,255,255,0.15);border-radius:12px;display:flex;align-items:center;justify-content:center;font-family:'Sora',sans-serif;font-weight:800;color:#fff;font-size:1.1rem;border:1px solid rgba(255,255,255,0.2)">PM</div>
      <span style="font-family:'Sora',sans-serif;font-weight:700;color:#fff;font-size:1.2rem">PAAU<span style="color:#f5a623">MENTOR</span></span>
      <button class="theme-toggle" id="themeToggle" onclick="toggleTheme()" style="margin-left:auto;border-color:rgba(255,255,255,0.2);background:rgba(255,255,255,0.1);color:#fff">🌙</button>
    </div>
    <div style="position:relative;z-index:1">
      <h2 style="font-size:2.4rem;font-weight:800;color:#fff;line-height:1.2;margin-bottom:16px">Your <span style="color:#fcd34d">academic success</span> starts here.</h2>
      <p style="color:rgba(255,255,255,0.7);font-size:0.95rem;line-height:1.7;margin-bottom:36px">Join PAAU's official peer mentorship platform — connect with senior students and alumni who've walked your path.</p>
      @foreach([['🧠','Smart Mentor Matching','AI-powered pairing based on skills and courses'],['📡','Real-Time Sessions','Video calls, chat, and screen sharing'],['🏅','Verified Certificates','Auto-generated PDF certificates with QR codes'],['📊','Progress Tracking','Structured learning paths with milestones']] as [$icon,$title,$desc])
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
        <div style="width:36px;height:36px;background:rgba(255,255,255,0.1);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0">{{ $icon }}</div>
        <div style="font-size:0.88rem;color:rgba(255,255,255,0.8)"><strong style="color:#fff;display:block">{{ $title }}</strong>{{ $desc }}</div>
      </div>
      @endforeach
    </div>
    <div style="position:relative;z-index:1">
      <p style="font-size:0.78rem;color:rgba(255,255,255,0.5)">© {{ date('Y') }} PAAUMENTOR · Prince Abubakar Audu University, Anyigba<br>Moses Goddey Joseph (23CS1004)</p>
    </div>
  </div>

  {{-- RIGHT FORM PANEL --}}
  <div class="auth-right">
    <div class="auth-right-inner">
      <div class="auth-tabs">
        <button class="auth-tab {{ request()->is('login') ? 'active' : '' }}" id="tabLogin" onclick="switchTab('login')">Sign In</button>
        <button class="auth-tab {{ request()->is('register') ? 'active' : '' }}" id="tabRegister" onclick="switchTab('register')">Create Account</button>
      </div>

      {{-- ERRORS --}}
      @if($errors->any())
        <div style="background:#fee2e2;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:0.85rem;color:#991b1b">
          @foreach($errors->all() as $e) <div>✗ {{ $e }}</div> @endforeach
        </div>
      @endif

      {{-- LOGIN --}}
      <div id="loginPanel" style="{{ request()->is('register') ? 'display:none' : '' }}">
        <h2 style="font-size:1.7rem;font-weight:800;margin-bottom:6px">Welcome back 👋</h2>
        <p style="font-size:0.88rem;color:var(--text-3);margin-bottom:28px">Sign in to your PAAUMENTOR account to continue.</p>
        <form method="POST" action="{{ route('login') }}" style="display:flex;flex-direction:column;gap:16px">
          @csrf
          <div class="form-group">
            <label class="form-label">Email or Student ID</label>
            <div class="input-wrap">
              <span class="input-icon">📧</span>
              <input type="text" name="login" class="form-input" placeholder="e.g. 23CS1004 or you@paau.edu.ng" value="{{ old('login') }}" required>
            </div>
          </div>
          <div class="form-group">
            <div style="display:flex;justify-content:space-between;align-items:center">
              <label class="form-label">Password</label>
              <a href="#" style="font-size:0.8rem;color:var(--blue-500)">Forgot password?</a>
            </div>
            <div class="input-wrap">
              <span class="input-icon">🔒</span>
              <input type="password" name="password" id="loginPw" class="form-input" placeholder="Enter your password" required>
              <button type="button" onclick="togglePw('loginPw')" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:0.9rem;color:var(--text-3)">👁️</button>
            </div>
          </div>
          <label style="display:flex;align-items:center;gap:8px;font-size:0.85rem;cursor:pointer">
            <input type="checkbox" name="remember" style="accent-color:var(--blue-500)"> Remember me for 30 days
          </label>
          <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">Sign In to PAAUMENTOR</button>
        </form>
        <p style="font-size:0.85rem;color:var(--text-3);text-align:center;margin-top:16px">Don't have an account? <a onclick="switchTab('register')" style="color:var(--blue-500);font-weight:600;cursor:pointer">Create one free →</a></p>
      </div>

      {{-- REGISTER --}}
      <div id="registerPanel" style="{{ request()->is('register') ? '' : 'display:none' }}">
        <h2 style="font-size:1.7rem;font-weight:800;margin-bottom:6px">Join PAAUMENTOR 🎓</h2>
        <p style="font-size:0.88rem;color:var(--text-3);margin-bottom:28px">Create your free account — takes less than 2 minutes.</p>
        <form method="POST" action="{{ route('register') }}" style="display:flex;flex-direction:column;gap:16px">
          @csrf
          <div class="form-group">
            <label class="form-label">I want to join as</label>
            <div class="role-select">
              <div><input type="radio" name="role" id="roleMentee" class="role-option" value="mentee" {{ old('role','mentee')==='mentee'?'checked':'' }}><label for="roleMentee" class="role-label"><span style="font-size:1.4rem">📖</span>Mentee</label></div>
              <div><input type="radio" name="role" id="roleMentor" class="role-option" value="mentor" {{ old('role')==='mentor'?'checked':'' }}><label for="roleMentor" class="role-label"><span style="font-size:1.4rem">🎓</span>Mentor</label></div>
              <div><input type="radio" name="role" id="roleAlumni" class="role-option" value="alumni" {{ old('role')==='alumni'?'checked':'' }}><label for="roleAlumni" class="role-label"><span style="font-size:1.4rem">🏛️</span>Alumni</label></div>
            </div>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div class="form-group"><label class="form-label">First Name</label><input type="text" name="first_name" class="form-input" placeholder="Moses" value="{{ old('first_name') }}" required></div>
            <div class="form-group"><label class="form-label">Last Name</label><input type="text" name="last_name" class="form-input" placeholder="Joseph" value="{{ old('last_name') }}" required></div>
          </div>
          <div class="form-group"><label class="form-label">Student / Matric Number</label><div class="input-wrap"><span class="input-icon">🪪</span><input type="text" name="student_id" class="form-input" placeholder="e.g. 23CS1004" value="{{ old('student_id') }}" required></div></div>
          <div class="form-group"><label class="form-label">Institutional Email</label><div class="input-wrap"><span class="input-icon">📧</span><input type="email" name="email" class="form-input" placeholder="you@paau.edu.ng" value="{{ old('email') }}" required></div></div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div class="form-group"><label class="form-label">Department</label><select name="department" class="form-select" required><option>Computer Science</option><option>Mathematics</option><option>Physics</option><option>Statistics</option></select></div>
            <div class="form-group"><label class="form-label">Level</label><select name="level" class="form-select" required><option>100L</option><option>200L</option><option>300L</option><option>400L</option><option>500L</option><option>Alumni</option></select></div>
          </div>
          <div class="form-group"><label class="form-label">Password</label><div class="input-wrap"><span class="input-icon">🔒</span><input type="password" name="password" id="regPw" class="form-input" placeholder="Minimum 8 characters" required><button type="button" onclick="togglePw('regPw')" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:0.9rem;color:var(--text-3)">👁️</button></div></div>
          <input type="password" name="password_confirmation" class="form-input" placeholder="Confirm password" required>
          <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">🚀 Create My Account</button>
        </form>
        <p style="font-size:0.85rem;color:var(--text-3);text-align:center;margin-top:16px">Already have an account? <a onclick="switchTab('login')" style="color:var(--blue-500);font-weight:600;cursor:pointer">Sign in →</a></p>
      </div>
    </div>
  </div>
</div>
<script src="{{ asset('js/app.js') }}"></script>
<script>
function switchTab(t){
  document.getElementById('loginPanel').style.display    = t==='login'    ? '' : 'none';
  document.getElementById('registerPanel').style.display = t==='register' ? '' : 'none';
  document.getElementById('tabLogin').classList.toggle('active',    t==='login');
  document.getElementById('tabRegister').classList.toggle('active', t==='register');
}
function togglePw(id){ const e=document.getElementById(id); e.type=e.type==='password'?'text':'password'; }
</script>
</body>
</html>


{{-- ============================================================
FILE: resources/views/dashboard/index.blade.php
============================================================ --}}
{{-- SAVE AS: resources/views/dashboard/index.blade.php --}}
@extends('layouts.sidebar')
@section('title', 'Dashboard')

@section('page-content')
<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;flex-wrap:wrap;gap:16px">
  <div>
    <h1 style="font-size:1.6rem;font-weight:800;margin-bottom:4px">Good {{ now()->format('H') < 12 ? 'morning' : (now()->format('H') < 17 ? 'afternoon' : 'evening') }}, {{ $user->first_name }} 👋</h1>
    <p style="color:var(--text-3);font-size:0.9rem">{{ $user->level }} · {{ $user->department }} · Student ID: {{ $user->student_id }}</p>
  </div>
  <div style="display:flex;gap:10px">
    <a href="{{ route('mentors.index') }}" class="btn btn-outline btn-sm">🔍 Find Mentor</a>
    <a href="{{ route('chat.index') }}"    class="btn btn-primary btn-sm">💬 Messages</a>
  </div>
</div>

{{-- STATS --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:28px">
  <div class="stat-card">
    <div class="stat-icon" style="background:#dbeafe">🤝</div>
    <div class="stat-value">{{ $mentorships->count() }}</div>
    <div class="stat-label">Active Mentors</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:#d1fae5">✅</div>
    <div class="stat-value">{{ $sessionCount }}</div>
    <div class="stat-label">Sessions Completed</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:#fef3c7">🗺️</div>
    <div class="stat-value">{{ count($learningPaths) }}</div>
    <div class="stat-label">Learning Paths</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:#ede9fe">🏅</div>
    <div class="stat-value">{{ $certificates->count() }}</div>
    <div class="stat-label">Certificates</div>
  </div>
</div>

{{-- MAIN GRID --}}
<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;margin-bottom:24px">

  {{-- Smart Match Suggestions --}}
  <div class="card">
    <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1rem;margin-bottom:16px;display:flex;justify-content:space-between">
      🧠 Smart Mentor Matches
      <a href="{{ route('mentors.index') }}" style="font-size:0.8rem;font-weight:500;color:var(--blue-500)">View all →</a>
    </div>
    @forelse($matches as $m)
    <div style="display:flex;align-items:center;gap:14px;padding:16px;border-radius:14px;border:1px solid var(--border);margin-bottom:12px">
      <div class="avatar avatar-md">{{ $m['user']->initials }}</div>
      <div style="flex:1">
        <div style="font-weight:700;font-size:0.92rem">{{ $m['user']->full_name }}</div>
        <div style="font-size:0.78rem;color:var(--text-3)">{{ $m['user']->level }} · {{ implode(', ', $m['user']->hasSkills->take(3)->pluck('name')->toArray()) }}</div>
      </div>
      <div style="text-align:right">
        <div style="background:linear-gradient(135deg,var(--blue-500),var(--blue-700));color:#fff;border-radius:10px;padding:4px 10px;font-family:'Sora',sans-serif;font-weight:800;font-size:0.82rem">{{ $m['score'] }}%</div>
        <div style="font-size:0.7rem;color:var(--text-3);margin-top:2px">match</div>
      </div>
    </div>
    @empty
    <p style="color:var(--text-3);font-size:0.88rem">No mentor suggestions yet. Complete your profile to get matched!</p>
    @endforelse
  </div>

  {{-- Upcoming Sessions --}}
  <div class="card">
    <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1rem;margin-bottom:16px">🗓️ Upcoming Sessions</div>
    @forelse($upcomingSessions as $s)
    <div style="background:var(--surface-2);border-radius:14px;padding:16px;margin-bottom:12px;display:flex;align-items:center;gap:14px">
      <div style="background:var(--blue-500);color:#fff;border-radius:10px;padding:8px 12px;text-align:center;flex-shrink:0">
        <div style="font-size:0.7rem;font-weight:600;letter-spacing:0.06em">{{ strtoupper($s->scheduled_at->format('D')) }}</div>
        <div style="font-family:'Sora',sans-serif;font-weight:800;font-size:1.4rem;line-height:1">{{ $s->scheduled_at->format('d') }}</div>
      </div>
      <div>
        <div style="font-weight:700;font-size:0.9rem">{{ $s->title }}</div>
        <div style="font-size:0.78rem;color:var(--text-3)">{{ $s->scheduled_at->format('g:i A') }}</div>
        <span class="badge badge-blue" style="margin-top:6px">{{ ucfirst($s->type) }}</span>
      </div>
    </div>
    @empty
    <p style="color:var(--text-3);font-size:0.88rem">No upcoming sessions.</p>
    @endforelse
    <a href="{{ route('chat.index') }}" class="btn btn-outline btn-sm" style="width:100%;justify-content:center;margin-top:8px">+ Book New Session</a>
  </div>
</div>

{{-- LEARNING PATHS --}}
<div class="card" style="margin-bottom:24px">
  <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1rem;margin-bottom:16px;display:flex;justify-content:space-between">
    🗺️ My Learning Paths
    <a href="{{ route('learning.index') }}" style="font-size:0.8rem;font-weight:500;color:var(--blue-500)">All →</a>
  </div>
  @forelse($learningPaths as $lp)
  <div style="padding:14px 0;border-bottom:1px solid var(--border)">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
      <span style="font-weight:700;font-size:0.9rem">{{ $lp['path']->title }}</span>
      <span style="font-family:'Sora',sans-serif;font-weight:800;font-size:0.88rem;color:{{ $lp['progress'] == 100 ? 'var(--success)' : 'var(--blue-500)' }}">{{ $lp['progress'] }}%</span>
    </div>
    <div class="progress-bar"><div class="progress-fill {{ $lp['progress'] == 100 ? 'green' : '' }}" style="width:{{ $lp['progress'] }}%"></div></div>
    @if($lp['progress'] == 100)
      <div style="font-size:0.75rem;color:var(--success);margin-top:4px">✓ Completed — Certificate issued!</div>
    @else
      <div style="font-size:0.75rem;color:var(--text-3);margin-top:4px">Due: {{ $lp['path']->due_date?->format('M d, Y') ?? 'No deadline' }}</div>
    @endif
  </div>
  @empty
  <p style="color:var(--text-3);font-size:0.88rem">No learning paths yet. Request a mentor to get started.</p>
  @endforelse
</div>

{{-- CHART --}}
<div class="card">
  <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1rem;margin-bottom:16px">📈 My Engagement — Last 6 Months</div>
  <canvas id="engagementChart" width="900" height="200" style="max-width:100%"></canvas>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const labels = @json($engagement->keys());
  const data   = @json($engagement->values());
  if (labels.length) drawBarChart('engagementChart', labels, data, '#2563eb');
  else drawBarChart('engagementChart', ['Aug','Sep','Oct','Nov','Dec','Jan'], [0,0,0,0,0,0], '#2563eb');
});
</script>
@endpush


{{-- ============================================================
FILE: resources/views/mentors/index.blade.php
============================================================ --}}
{{-- SAVE AS: resources/views/mentors/index.blade.php --}}
@extends('layouts.sidebar')
@section('title', 'Find a Mentor')

@section('page-content')
<div style="margin-bottom:24px">
  <h1 class="section-title">Find a Mentor</h1>
  <p class="section-sub">Browse verified PAAU mentors or let our smart matching find the best fit for your goals.</p>
</div>

<form method="GET" action="{{ route('mentors.index') }}">
  <div style="display:flex;gap:12px;align-items:center;background:var(--surface);border:1.5px solid var(--border);border-radius:14px;padding:12px 16px;margin-bottom:20px">
    <span style="font-size:1.1rem">🔍</span>
    <input type="text" name="search" class="form-input" style="border:none;background:transparent;box-shadow:none;padding:0" placeholder="Search by name, skill, or course..." value="{{ request('search') }}">
    <button type="submit" class="btn btn-primary btn-sm">Search</button>
  </div>
  <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px">
    <select name="level" class="form-select" style="width:auto;padding:8px 14px">
      <option value="">All Levels</option>
      @foreach(['200L','300L','400L','500L','Alumni'] as $l)
        <option value="{{ $l }}" {{ request('level')===$l?'selected':'' }}>{{ $l }}</option>
      @endforeach
    </select>
    <select name="skill" class="form-select" style="width:auto;padding:8px 14px">
      <option value="">All Skills</option>
      @foreach($skills as $skill)
        <option value="{{ $skill->name }}" {{ request('skill')===$skill->name?'selected':'' }}>{{ $skill->name }}</option>
      @endforeach
    </select>
    <button type="submit" class="btn btn-outline btn-sm">Apply Filters</button>
    <a href="{{ route('mentors.index') }}" class="btn btn-outline btn-sm">Clear</a>
  </div>
</form>

<div style="font-size:0.9rem;color:var(--text-3);margin-bottom:16px">
  Showing <strong style="color:var(--text)">{{ $mentors->count() }} mentors</strong>
</div>

<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px">
  @forelse($mentors as $m)
  @php $mentor = $m['mentor']; $score = $m['score']; @endphp
  <div style="background:var(--surface);border:1px solid var(--border);border-radius:20px;overflow:hidden;transition:transform 0.25s,box-shadow 0.25s" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='var(--shadow-lg)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
    <div style="height:70px;background:linear-gradient(135deg,var(--blue-700),var(--blue-500));position:relative">
      @if($mentor->is_active)<span class="badge badge-green" style="position:absolute;top:12px;right:12px">● Online</span>@endif
      @if($score >= 80)<span class="badge badge-blue" style="position:absolute;top:12px;left:12px;background:rgba(255,255,255,0.2);color:#fff;border:none">{{ $score }}% Match</span>@endif
    </div>
    <div style="padding:0 20px 20px">
      <div style="margin-top:-28px;margin-bottom:12px">
        <div class="avatar avatar-lg" style="border:3px solid var(--surface)">{{ $mentor->initials }}</div>
      </div>
      <div style="font-weight:800;font-size:1rem;margin-bottom:2px">{{ $mentor->full_name }}</div>
      <div style="font-size:0.8rem;color:var(--text-3);margin-bottom:10px">{{ $mentor->level }} · {{ $mentor->department }}</div>
      <div style="display:flex;flex-wrap:wrap;gap:5px;margin-bottom:14px">
        @foreach($mentor->hasSkills->take(4) as $skill)
          <span class="badge badge-blue">{{ $skill->name }}</span>
        @endforeach
      </div>
      <div style="display:flex;gap:16px;margin-bottom:14px">
        <div style="text-align:center"><div style="font-family:'Sora',sans-serif;font-weight:800;font-size:1.1rem">{{ $mentor->average_rating }}</div><div style="font-size:0.7rem;color:var(--text-3)">Rating</div></div>
        <div style="width:1px;background:var(--border)"></div>
        <div style="text-align:center"><div style="font-family:'Sora',sans-serif;font-weight:800;font-size:1.1rem">{{ $mentor->mentorMentorships->count() }}</div><div style="font-size:0.7rem;color:var(--text-3)">Mentees</div></div>
      </div>
      @if($mentor->bio)
        <p style="font-size:0.82rem;color:var(--text-2);line-height:1.6;margin-bottom:14px">{{ Str::limit($mentor->bio, 100) }}</p>
      @endif
      <div style="display:flex;gap:8px">
        <a href="{{ route('mentors.show', $mentor) }}" class="btn btn-primary" style="flex:1;justify-content:center;font-size:0.85rem">View & Request</a>
        <a href="{{ route('chat.index') }}" class="btn btn-outline" style="font-size:0.85rem">💬</a>
      </div>
    </div>
  </div>
  @empty
  <div style="grid-column:span 3;text-align:center;padding:60px;color:var(--text-3)">
    <div style="font-size:3rem;margin-bottom:16px">🔍</div>
    <p>No mentors found matching your criteria. Try adjusting your filters.</p>
  </div>
  @endforelse
</div>
@endsection


{{-- ============================================================
FILE: resources/views/learning/index.blade.php
============================================================ --}}
{{-- SAVE AS: resources/views/learning/index.blade.php --}}
@extends('layouts.sidebar')
@section('title', 'Learning Paths')

@section('page-content')
<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px">
  <div>
    <h1 class="section-title">My Learning Paths</h1>
    <p class="section-sub">Track your mentorship programs and complete tasks to earn certificates.</p>
  </div>
</div>

<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px">
  @forelse($paths as $lp)
  @php $path = $lp['path']; $progress = $lp['progress']; @endphp
  <div style="background:var(--surface);border:1px solid var(--border);border-radius:20px;overflow:hidden;transition:transform 0.25s" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform=''">
    <div style="height:80px;background:linear-gradient(135deg,var(--blue-700),var(--blue-500));position:relative;display:flex;align-items:flex-end;padding:12px 16px">
      <span style="position:absolute;top:16px;left:16px;font-size:2rem">{{ $progress == 100 ? '✅' : '🗺️' }}</span>
      <span class="badge {{ $progress == 100 ? 'badge-green' : 'badge-blue' }}" style="margin-left:auto">{{ $progress == 100 ? '✓ Completed' : 'In Progress' }}</span>
    </div>
    <div style="padding:16px">
      <div style="font-weight:800;font-size:1rem;margin-bottom:4px">{{ $path->title }}</div>
      <div style="font-size:0.78rem;color:var(--text-3);margin-bottom:10px;display:flex;align-items:center;gap:6px">
        <div class="avatar avatar-sm">{{ $path->mentor->initials }}</div> {{ $path->mentor->full_name }}
      </div>
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
        <span style="font-size:0.82rem;color:var(--text-3)">{{ $path->modules->sum(fn($m) => $m->tasks->count()) }} tasks</span>
        <span style="font-family:'Sora',sans-serif;font-weight:800;font-size:0.9rem;color:{{ $progress==100 ? 'var(--success)' : 'var(--blue-500)' }}">{{ $progress }}%</span>
      </div>
      <div class="progress-bar"><div class="progress-fill {{ $progress==100 ? 'green' : '' }}" style="width:{{ $progress }}%"></div></div>
    </div>
    <div style="padding:12px 16px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">
      <span style="font-size:0.78rem;color:var(--text-3)">{{ $path->due_date ? '📅 Due '.$path->due_date->format('M d') : '📅 No deadline' }}</span>
      <a href="{{ route('learning.show', $path) }}" class="btn btn-primary btn-sm">{{ $progress == 100 ? 'View' : 'Continue →' }}</a>
    </div>
  </div>
  @empty
  <div style="grid-column:span 3;text-align:center;padding:60px;color:var(--text-3)">
    <div style="font-size:3rem;margin-bottom:16px">🗺️</div>
    <p>No learning paths yet. Request a mentor and ask them to create a learning path for you!</p>
    <a href="{{ route('mentors.index') }}" class="btn btn-primary" style="margin-top:16px">Find a Mentor</a>
  </div>
  @endforelse
</div>
@endsection


{{-- ============================================================
FILE: resources/views/learning/show.blade.php
============================================================ --}}
{{-- SAVE AS: resources/views/learning/show.blade.php --}}
@extends('layouts.sidebar')
@section('title', $path->title)

@section('page-content')
{{-- Cert Banner --}}
@if($path->certificate)
<div style="background:linear-gradient(135deg,var(--gold),#f97316);border-radius:16px;padding:20px 24px;display:flex;align-items:center;gap:16px;margin-bottom:24px">
  <span style="font-size:2.2rem">🏅</span>
  <div style="flex:1"><h3 style="font-size:1rem;font-weight:800;color:#fff;margin-bottom:4px">Certificate Issued!</h3><p style="font-size:0.82rem;color:rgba(255,255,255,0.8)">ID: {{ $path->certificate->certificate_id }}</p></div>
  <button class="btn btn-sm" style="background:#fff;color:var(--blue-600)">Download PDF</button>
</div>
@elseif($progress < 100)
<div style="background:linear-gradient(135deg,var(--blue-700),var(--blue-500));border-radius:16px;padding:20px 24px;display:flex;align-items:center;gap:16px;margin-bottom:24px">
  <span style="font-size:2.2rem">🏆</span>
  <div style="flex:1"><h3 style="font-size:1rem;font-weight:800;color:#fff;margin-bottom:4px">You're {{ $progress }}% toward your certificate!</h3><p style="font-size:0.82rem;color:rgba(255,255,255,0.75)">Complete all tasks to auto-generate your certificate.</p></div>
  <div style="text-align:right"><div style="font-family:'Sora',sans-serif;font-size:2rem;font-weight:800;color:#fff">{{ $progress }}%</div></div>
</div>
@endif

{{-- Header --}}
<div style="display:flex;align-items:flex-start;gap:20px;margin-bottom:28px">
  <div style="width:64px;height:64px;border-radius:16px;background:linear-gradient(135deg,var(--blue-700),var(--blue-500));display:flex;align-items:center;justify-content:center;font-size:2rem;flex-shrink:0">🌐</div>
  <div>
    <h1 style="font-size:1.4rem;font-weight:800;margin-bottom:8px">{{ $path->title }}</h1>
    <div style="display:flex;gap:16px;flex-wrap:wrap;font-size:0.82rem;color:var(--text-3)">
      <span>👩‍🏫 {{ $path->mentor->full_name }}</span>
      @if($path->due_date)<span>📅 Due: {{ $path->due_date->format('M d, Y') }}</span>@endif
    </div>
  </div>
</div>

{{-- Modules & Tasks --}}
@foreach($path->modules as $module)
<div style="font-size:0.78rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:var(--text-3);padding:10px 0 8px;border-bottom:1px solid var(--border);margin-bottom:8px;display:flex;align-items:center;gap:8px">
  📦 {{ $module->title }}
</div>
@foreach($module->tasks->sortBy('order') as $task)
@php $sub = $task->submissions->first(); $done = $sub && $sub->status === 'graded'; @endphp
<div style="display:flex;align-items:center;gap:14px;background:var(--surface-2);border-radius:14px;padding:14px 16px;border:1.5px solid {{ $done ? 'var(--success)' : ($task->is_locked ? 'var(--border)' : 'var(--blue-400)') }};margin-bottom:8px;opacity:{{ $task->is_locked ? '0.5' : '1' }}">
  <div style="width:24px;height:24px;border-radius:50%;border:2px solid {{ $done ? 'var(--success)' : 'var(--border)' }};display:flex;align-items:center;justify-content:center;background:{{ $done ? 'var(--success)' : 'transparent' }};flex-shrink:0">
    @if($done)<span style="font-size:0.7rem;color:#fff;font-weight:700">✓</span>
    @elseif(!$task->is_locked)<span style="font-size:0.7rem;color:var(--blue-500);font-weight:700">→</span>
    @endif
  </div>
  <div style="flex:1">
    <div style="font-weight:700;font-size:0.9rem;color:{{ !$done && !$task->is_locked ? 'var(--blue-500)' : '' }}">{{ $task->title }}</div>
    @if($task->description)<div style="font-size:0.75rem;color:var(--text-3);margin-top:2px">{{ $task->description }}</div>@endif
    @if($done && $sub->score)<div style="font-size:0.75rem;color:var(--success);margin-top:2px">Score: {{ $sub->score }}/{{ $task->max_score }}</div>@endif
  </div>
  @if($done)
    <span class="badge badge-green">Done</span>
  @elseif($task->is_locked)
    <span class="badge badge-gray">Locked</span>
  @elseif($sub && $sub->status === 'submitted')
    <span class="badge badge-blue">Submitted</span>
  @else
    <form method="POST" action="{{ route('learning.submit', $task) }}" enctype="multipart/form-data" style="display:flex;align-items:center;gap:6px">
      @csrf
      <input type="file" name="file" style="display:none" id="file_{{ $task->id }}">
      <button type="button" onclick="document.getElementById('file_{{ $task->id }}').click()" class="btn btn-outline btn-sm">📎</button>
      <button type="submit" class="btn btn-primary btn-sm">Submit</button>
    </form>
  @endif
</div>
@endforeach
@endforeach
@endsection


{{-- ============================================================
FILE: resources/views/admin/dashboard.blade.php
============================================================ --}}
{{-- SAVE AS: resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')
@section('title','Admin Dashboard')

@section('content')
<div class="app-layout">
  <aside class="sidebar">
    <div class="sidebar-label">Admin</div>
    <a href="{{ route('admin.dashboard') }}" class="sidebar-link active"><span class="icon">📊</span> Overview</a>
    <a href="{{ route('admin.users') }}"     class="sidebar-link"><span class="icon">👥</span> Users</a>
    <a href="#" class="sidebar-link"><span class="icon">🤝</span> Mentorships</a>
    <a href="#" class="sidebar-link"><span class="icon">🏅</span> Certificates</a>
    <div class="sidebar-label">Moderation</div>
    <a href="#" class="sidebar-link"><span class="icon">✅</span> Approvals <span class="count">{{ $pendingMentors->count() }}</span></a>
    <a href="#" class="sidebar-link"><span class="icon">⚙️</span> Settings</a>
    <form method="POST" action="{{ route('logout') }}" style="margin-top:auto">
      @csrf<button type="submit" class="sidebar-link" style="width:100%;text-align:left;background:none;border:none;cursor:pointer"><span class="icon">🚪</span> Sign Out</button>
    </form>
  </aside>
  <main class="main-content">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;flex-wrap:wrap;gap:16px">
      <div>
        <h1 style="font-size:1.6rem;font-weight:800;margin-bottom:4px">Admin Dashboard</h1>
        <p style="color:var(--text-3);font-size:0.9rem">PAAUMENTOR · Department of Computer Science, PAAU</p>
      </div>
    </div>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:28px">
      <div class="stat-card"><div class="stat-icon" style="background:#dbeafe">👥</div><div class="stat-value">{{ $stats['users'] }}</div><div class="stat-label">Total Users</div></div>
      <div class="stat-card"><div class="stat-icon" style="background:#d1fae5">🤝</div><div class="stat-value">{{ $stats['mentorships'] }}</div><div class="stat-label">Active Mentorships</div></div>
      <div class="stat-card"><div class="stat-icon" style="background:#fef3c7">📡</div><div class="stat-value">{{ $stats['sessions'] }}</div><div class="stat-label">Sessions Done</div></div>
      <div class="stat-card"><div class="stat-icon" style="background:#ede9fe">🏅</div><div class="stat-value">{{ $stats['certificates'] }}</div><div class="stat-label">Certificates</div></div>
    </div>

    {{-- Pending actions --}}
    @if($pendingMentors->count())
    <div style="background:#fef9ec;border:1px solid #fcd34d;border-radius:14px;padding:16px 20px;margin-bottom:20px;display:flex;align-items:center;gap:12px">
      <span style="font-size:1.3rem">⏳</span>
      <div style="flex:1"><div style="font-weight:700;font-size:0.9rem">{{ $pendingMentors->count() }} mentor applications awaiting approval</div></div>
      <a href="{{ route('admin.users') }}?status=pending" class="btn btn-sm btn-gold">Review Now</a>
    </div>
    @endif

    {{-- Users table --}}
    <div class="card" style="margin-bottom:24px">
      <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1rem;margin-bottom:16px;display:flex;justify-content:space-between">
        👥 Recent Users <a href="{{ route('admin.users') }}" style="font-size:0.8rem;font-weight:500;color:var(--blue-500)">View All →</a>
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
              <td>@if($u->is_verified)<span class="badge badge-green">✓ Verified</span>@else<span class="badge badge-gray">Pending</span>@endif</td>
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

    {{-- Charts --}}
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:24px">
      <div class="card">
        <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1rem;margin-bottom:16px">📈 Monthly Sessions</div>
        <canvas id="sessionsChart" width="600" height="200" style="max-width:100%"></canvas>
      </div>
      <div class="card">
        <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1rem;margin-bottom:16px">🥧 User Roles</div>
        <canvas id="rolesChart" width="260" height="200" style="max-width:100%;display:block;margin:0 auto"></canvas>
        <div style="margin-top:12px;font-size:0.82rem;display:flex;flex-direction:column;gap:6px">
          @foreach($roleData as $role => $count)
          <div style="display:flex;align-items:center;gap:8px"><span style="width:12px;height:12px;border-radius:3px;background:#2563eb;display:inline-block"></span>{{ ucfirst($role) }} — {{ $count }}</div>
          @endforeach
        </div>
      </div>
    </div>
  </main>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  drawBarChart('sessionsChart', @json($monthlyData->keys()), @json($monthlyData->values()), '#2563eb');
  const roles = @json($roleData);
  const colors = ['#2563eb','#f5a623','#10b981','#ef4444'];
  const segments = Object.entries(roles).map(([r,v],i) => ({value:v,color:colors[i]||'#666'}));
  drawDonutChart('rolesChart', segments);
});
</script>
@endpush
