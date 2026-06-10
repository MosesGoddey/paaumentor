<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PAAUMENTOR — Peer Mentorship Platform</title>
<link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<style>
/* Transparent navbar over hero image */
.navbar {
  background: rgba(255,255,255,0.15) !important;
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-bottom: 1px solid rgba(255,255,255,0.2) !important;
}
.nav-brand-text, .nav-brand-text span, .nav-brand { color: #fff !important; }
.btn-outline { color: #fff !important; border-color: rgba(255,255,255,0.6) !important; }
.btn-outline:hover { background: rgba(255,255,255,0.15) !important; }

/*  Hero  */
.hero {
  position: relative;
  min-height: 100vh;
  margin-top: -64px;
  padding-top: 160px;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  overflow: hidden;
  padding-left: 72px;
  padding-right: 72px;
  padding-bottom: 32px;
  background-color: #1a3a5c;
}

/* Background image — sized to full height so no vertical cropping */
.hero-bg {
  position: absolute;
  inset: 0;
  background-image: url('{{ asset('images/hero-bg.jpg') }}');
  background-size: auto 118%;
  background-position: right 52%;
  background-repeat: no-repeat;
  filter: brightness(0.85);
}

/* Gradient overlay — darkens left side for card readability, fades out on the right */
.hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to right, rgba(0,0,0,0.55) 0%, rgba(0,0,0,0.15) 55%, rgba(0,0,0,0) 100%);
}

/* Glassmorphism card */
.hero-card {
  position: relative;
  z-index: 2;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(20px) saturate(160%);
  -webkit-backdrop-filter: blur(20px) saturate(160%);
  border: 1.5px solid rgba(255, 255, 255, 0.45);
  border-radius: 20px;
  padding: 28px 36px;
  max-width: 460px;
  width: 100%;
  text-align: center;
  color: #fff;
  box-shadow: 0 8px 32px rgba(0,0,0,0.2), inset 0 1px 0 rgba(255,255,255,0.4);
  text-shadow: 0 1px 4px rgba(0,0,0,0.4);
}

.hero-badge {
  display: inline-block;
  background: rgba(255,255,255,0.15);
  border: 1px solid rgba(255,255,255,0.3);
  color: #fff;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  border-radius: 999px;
  padding: 4px 14px;
  margin-bottom: 12px;
}

.hero-card h1 {
  font-family: 'Sora', sans-serif;
  font-size: 1.85rem;
  font-weight: 800;
  line-height: 1.15;
  margin-bottom: 12px;
  text-shadow: 0 2px 12px rgba(0,0,0,0.3);
}

.hero-card h1 span { color: #93c5fd; }

.hero-card p {
  font-size: 0.88rem;
  line-height: 1.6;
  margin-bottom: 20px;
  opacity: 0.88;
}

.hero-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }

.btn-hero-primary {
  background: #fff;
  color: #1d4ed8;
  padding: 12px 28px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 0.95rem;
  text-decoration: none;
  transition: all 0.2s;
  box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}
.btn-hero-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.2); }

.btn-hero-secondary {
  background: transparent;
  color: #fff;
  border: 2px solid rgba(255,255,255,0.6);
  padding: 12px 28px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 0.95rem;
  text-decoration: none;
  transition: all 0.2s;
}
.btn-hero-secondary:hover { background: rgba(255,255,255,0.12); border-color: #fff; }

/* Scroll hint */
.scroll-hint {
  position: absolute;
  bottom: 28px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 2;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  color: rgba(255,255,255,0.5);
  font-size: 0.72rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  animation: bounce 2s infinite;
}
.scroll-hint span { font-size: 1.1rem; }
@keyframes bounce { 0%,100%{transform:translateX(-50%) translateY(0)} 50%{transform:translateX(-50%) translateY(6px)} }

/*  Stats  */
.stats {
  padding: 44px 20px;
  background: var(--surface);
  border-bottom: 1px solid var(--border);
  display: flex;
  justify-content: center;
  gap: 0;
  flex-wrap: wrap;
}
.stat-item {
  text-align: center;
  padding: 0 48px;
  border-right: 1px solid var(--border);
}
.stat-item:last-child { border-right: none; }
.stat-value { font-family: 'Sora', sans-serif; font-size: 2.2rem; font-weight: 800; color: var(--blue-500); }
.stat-label { font-size: 0.82rem; color: var(--text-3); margin-top: 4px; }

/*  Features  */
.features { padding: 80px 20px; background: var(--bg); }
.section-heading {
  text-align: center;
  font-family: 'Sora', sans-serif;
  font-size: 2rem;
  font-weight: 800;
  margin-bottom: 8px;
}
.section-sub-heading {
  text-align: center;
  color: var(--text-3);
  font-size: 0.92rem;
  margin-bottom: 48px;
}
.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
  max-width: 1100px;
  margin: 0 auto;
}
.feature-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 18px;
  padding: 28px 24px;
  transition: all 0.2s;
}
.feature-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); border-color: var(--blue-400); }
.feature-icon {
  width: 52px; height: 52px;
  border-radius: 14px;
  background: var(--surface-2);
  display: flex; align-items: center; justify-content: center;
  font-size: 1.6rem;
  margin-bottom: 16px;
}
.feature-card h3 { font-size: 1rem; font-weight: 700; margin-bottom: 8px; }
.feature-card p  { font-size: 0.84rem; color: var(--text-3); line-height: 1.65; }

/*  Featured Mentors  */
.mentors-section { padding: 80px 20px; background: var(--surface); border-top: 1px solid var(--border); }
.mentors-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 20px;
  max-width: 1100px;
  margin: 0 auto;
}
.mentor-card {
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 18px;
  padding: 26px 22px;
  text-align: center;
  transition: all 0.2s;
  position: relative;
}
.mentor-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); border-color: var(--blue-400); }
.mentor-tier-badge {
  position: absolute;
  top: 14px; right: 14px;
  font-size: 0.66rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 3px 9px;
  border-radius: 999px;
  background: var(--surface-2);
  color: var(--text-2);
}
.mentor-avatar, .mentor-avatar-fallback {
  width: 84px; height: 84px;
  border-radius: 50%;
  margin: 0 auto 14px;
  object-fit: cover;
  border: 3px solid var(--blue-400);
}
.mentor-avatar-fallback {
  display: flex; align-items: center; justify-content: center;
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: #fff;
  font-family: 'Sora', sans-serif;
  font-weight: 800;
  font-size: 1.7rem;
}
.mentor-name { font-family: 'Sora', sans-serif; font-size: 1.05rem; font-weight: 800; margin-bottom: 2px; }
.mentor-role { font-size: 0.8rem; color: var(--text-3); margin-bottom: 10px; }
.mentor-rating { font-size: 0.82rem; font-weight: 700; color: #f59e0b; margin-bottom: 12px; }
.mentor-rating span { color: var(--text-3); font-weight: 500; }
.mentor-skills { display: flex; flex-wrap: wrap; gap: 6px; justify-content: center; margin-bottom: 16px; min-height: 26px; }
.mentor-skill-tag {
  font-size: 0.7rem;
  font-weight: 600;
  padding: 3px 10px;
  border-radius: 999px;
  background: var(--surface-2);
  color: var(--text-2);
}
.mentor-card .btn { width: 100%; }

/*  CTA  */
.cta-section {
  position: relative;
  overflow: hidden;
  padding: 80px 20px;
  text-align: center;
  background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 100%);
  color: #fff;
}
.cta-section::before {
  content: '';
  position: absolute;
  width: 500px; height: 500px;
  border-radius: 50%;
  background: rgba(255,255,255,0.04);
  top: -150px; right: -100px;
}
.cta-section::after {
  content: '';
  position: absolute;
  width: 300px; height: 300px;
  border-radius: 50%;
  background: rgba(255,255,255,0.04);
  bottom: -80px; left: -60px;
}
.cta-section h2 { font-family:'Sora',sans-serif; font-size: 2.2rem; font-weight: 800; margin-bottom: 16px; position:relative;z-index:1; }
.cta-section p  { font-size: 1rem; opacity: 0.85; max-width: 500px; margin: 0 auto 36px; line-height:1.7; position:relative;z-index:1; }
.cta-buttons { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; position:relative;z-index:1; }

@media (max-width: 768px) {
  .hero { justify-content: center; padding: 40px 20px; }
  .hero-overlay { background: rgba(15,23,42,0.75); }
  .hero-card { padding: 36px 24px; }
  .hero-card h1 { font-size: 2rem; }
  .stat-item { padding: 16px 24px; border-right: none; border-bottom: 1px solid var(--border); width: 100%; }
  .stat-item:last-child { border-bottom: none; }
  .cta-section h2 { font-size: 1.6rem; }
}
</style>
</head>
<body>

<nav class="navbar">
  <div class="container">
    <div class="nav-inner">
      <a href="{{ route('home') }}" class="nav-brand">
        <div class="nav-logo">PM</div>
        <span class="nav-brand-text">PAAU<span>MENTOR</span></span>
      </a>
      <div class="nav-actions" style="margin-left:auto">
        <button class="theme-toggle" id="themeToggle" onclick="toggleTheme()" title="Toggle theme"></button>
        <a href="{{ route('login') }}"    class="btn btn-outline btn-sm">Sign In</a>
        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Get Started</a>
      </div>
    </div>
  </div>
</nav>

{{--  Hero  --}}
<div class="hero">
  <div class="hero-bg"></div>
  <div class="hero-overlay"></div>

  <div class="hero-card">
    <div class="hero-badge"> Prince Abubakar Audu University, Anyigba</div>
    <h1>Your Academic Success <span>Starts Here</span></h1>
    <p>Connect with senior students and alumni who've walked your path. Get guided learning, real-time mentorship, and the tools to grow — all in one place.</p>
    <div class="hero-actions">
      @auth
        <a href="{{ route('dashboard') }}" class="btn-hero-primary">Go to Dashboard</a>
      @else
        <a href="{{ route('register') }}" class="btn-hero-primary">Get Started Free</a>
        <a href="{{ route('login') }}"    class="btn-hero-secondary">Sign In</a>
      @endauth
    </div>
  </div>

  <div class="scroll-hint">
    <span></span>
    scroll
  </div>
</div>

{{--  Stats  --}}
<div class="stats">
  <div class="stat-item">
    <div class="stat-value count-up" data-target="4" data-suffix="+">0</div>
    <div class="stat-label">Verified Mentors</div>
  </div>
  <div class="stat-item">
    <div class="stat-value count-up" data-target="15" data-suffix="+">0</div>
    <div class="stat-label">Skills Available</div>
  </div>
  <div class="stat-item">
    <div class="stat-value count-up" data-target="100" data-suffix="%">0</div>
    <div class="stat-label">Free to Use</div>
  </div>
  <div class="stat-item">
    <div class="stat-value">PAAU</div>
    <div class="stat-label">Exclusive Community</div>
  </div>
</div>

{{--  Features  --}}
<div class="features">
  <h2 class="section-heading">Everything You Need to Grow</h2>
  <p class="section-sub-heading">Powerful features built for PAAU students and mentors.</p>
  <div class="features-grid">
    <div class="feature-card">
      <div class="feature-icon" style="background:#3b82f6">
        <svg width="26" height="26" fill="none" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      </div>
      <h3>Smart Matching</h3>
      <p>Our algorithm pairs you with mentors based on your skills, goals, and academic level for the best fit.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon" style="background:#0d9488">
        <svg width="26" height="26" fill="none" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
      </div>
      <h3>Real-Time Chat</h3>
      <p>Direct messaging with your mentor for instant support, advice, and file sharing.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon" style="background:#7c3aed">
        <svg width="26" height="26" fill="none" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
      </div>
      <h3>Learning Paths</h3>
      <p>Structured courses with modules, tasks, and progress tracking designed by your mentor.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon" style="background:#ec4899">
        <svg width="26" height="26" fill="none" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
      </div>
      <h3>Video Sessions</h3>
      <p>Schedule video calls, voice calls, or chat-only sessions and keep a full history of every meeting.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon" style="background:#f97316">
        <svg width="26" height="26" fill="none" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
      </div>
      <h3>Skill Exchange</h3>
      <p>Trade skills with peers — you teach what you know, they teach what you need.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon" style="background:#16a34a">
        <svg width="26" height="26" fill="none" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
      </div>
      <h3>Mentor Upgrade</h3>
      <p>High-performing mentees can apply to become mentors after meeting qualifications and earning a recommendation.</p>
    </div>
  </div>
</div>

{{--  Featured Mentors  --}}
@if(isset($featuredMentors) && $featuredMentors->count())
<div class="mentors-section">
  <h2 class="section-heading">Meet Our Mentors</h2>
  <p class="section-sub-heading">Learn from verified senior students and alumni who've walked your path.</p>
  <div class="mentors-grid">
    @foreach($featuredMentors as $mentor)
      <div class="mentor-card">
        <span class="mentor-tier-badge">{{ $mentor->mentor_tier_label }}</span>

        @if($mentor->avatar_url)
          <img src="{{ $mentor->avatar_url }}" alt="{{ $mentor->full_name }}" class="mentor-avatar">
        @else
          <div class="mentor-avatar-fallback">{{ $mentor->initials }}</div>
        @endif

        <div class="mentor-name">{{ $mentor->full_name }}</div>
        <div class="mentor-role">
          {{ ucfirst($mentor->role) }}@if($mentor->department) · {{ $mentor->department }}@endif
        </div>

        <div class="mentor-rating">
          ★ {{ number_format($mentor->average_rating, 1) }}
          <span>({{ $mentor->ratings->count() }} {{ $mentor->ratings->count() === 1 ? 'review' : 'reviews' }})</span>
        </div>

        <div class="mentor-skills">
          @foreach($mentor->hasSkills->take(3) as $skill)
            <span class="mentor-skill-tag">{{ $skill->name }}</span>
          @endforeach
        </div>

        @auth
          <a href="{{ route('mentors.show', $mentor) }}" class="btn btn-primary btn-sm">View Profile</a>
        @else
          <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Connect</a>
        @endauth
      </div>
    @endforeach
  </div>
</div>
@endif

{{--  CTA  --}}
<div class="cta-section">
  <h2>Ready to Start Your Mentorship Journey?</h2>
  <p>Join PAAU students already learning from mentors in our community. It is completely free.</p>
  <div class="cta-buttons">
    @auth
      <a href="{{ route('dashboard') }}" class="btn-hero-primary">Open Dashboard</a>
    @else
      <a href="{{ route('register') }}" class="btn-hero-primary">Create Free Account</a>
      <a href="{{ route('login') }}"    class="btn-hero-secondary">Sign In</a>
    @endauth
  </div>
</div>

{{--  Footer  --}}
<footer style="background:var(--surface);border-top:1px solid var(--border);padding:28px 20px;text-align:center;color:var(--text-3);font-size:0.83rem">
  <p style="font-weight:600;color:var(--text-2);margin-bottom:6px">PAAUMENTOR · Prince Abubakar Audu University, Anyigba</p>
  <p>Final Year Project — Moses Goddey Joseph (23CS1004) · Supervisor: Mr. Richard Akomodi</p>
</footer>

@include('partials.mentor-ai-widget')
<script src="{{ asset('js/app.js') }}"></script>
<script>
const observer = new IntersectionObserver(entries => {
  entries.forEach(entry => {
    if (!entry.isIntersecting) return;
    const el     = entry.target;
    const target = parseInt(el.dataset.target, 10);
    const suffix = el.dataset.suffix || '';
    const duration = 1400;
    const start  = performance.now();
    function step(now) {
      const progress = Math.min((now - start) / duration, 1);
      const ease     = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.floor(ease * target) + suffix;
      if (progress < 1) requestAnimationFrame(step);
      else el.textContent = target + suffix;
    }
    requestAnimationFrame(step);
    observer.unobserve(el);
  });
}, { threshold: 0.5 });

document.querySelectorAll('.count-up').forEach(el => observer.observe(el));
</script>
</body>
</html>
