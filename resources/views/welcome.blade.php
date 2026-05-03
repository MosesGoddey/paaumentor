<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PAAUMENTOR — Peer Mentorship Platform</title>
<link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<style>
/* ── Hero ─────────────────────────────────────────────────────── */
.hero {
  position: relative;
  min-height: calc(100vh - 64px);
  display: flex;
  align-items: center;
  justify-content: flex-end;
  overflow: hidden;
  padding: 60px 72px;
}

/* Background image layer — no blur so faces are clear */
.hero-bg {
  position: absolute;
  inset: 0;
  background-image: url('{{ asset('images/hero-bg.jpg') }}');
  background-size: cover;
  background-position: left center;
  filter: brightness(0.72);
}

/* Gradient overlay — stronger on the right where the card sits */
.hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to right, rgba(15,23,42,0.15) 0%, rgba(15,23,42,0.80) 55%, rgba(15,23,42,0.92) 100%);
}

/* Frosted-glass content card — sits on the right */
.hero-card {
  position: relative;
  z-index: 2;
  background: rgba(255, 255, 255, 0.08);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.18);
  border-radius: 28px;
  padding: 52px 56px;
  max-width: 520px;
  width: 100%;
  text-align: center;
  color: #fff;
  box-shadow: 0 24px 64px rgba(0,0,0,0.4);
}

.hero-badge {
  display: inline-block;
  background: rgba(255,255,255,0.15);
  border: 1px solid rgba(255,255,255,0.3);
  color: #fff;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  border-radius: 999px;
  padding: 5px 14px;
  margin-bottom: 20px;
}

.hero-card h1 {
  font-family: 'Sora', sans-serif;
  font-size: 3rem;
  font-weight: 800;
  line-height: 1.15;
  margin-bottom: 18px;
  text-shadow: 0 2px 12px rgba(0,0,0,0.3);
}

.hero-card h1 span { color: #93c5fd; }

.hero-card p {
  font-size: 1.05rem;
  line-height: 1.75;
  margin-bottom: 34px;
  opacity: 0.88;
}

.hero-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }

.btn-hero-primary {
  background: #fff;
  color: #1d4ed8;
  padding: 13px 30px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 0.92rem;
  text-decoration: none;
  transition: all 0.2s;
  box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}
.btn-hero-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.2); }

.btn-hero-secondary {
  background: transparent;
  color: #fff;
  border: 2px solid rgba(255,255,255,0.6);
  padding: 13px 30px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 0.92rem;
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

/* ── Stats ──────────────────────────────────────────────────────── */
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

/* ── Features ───────────────────────────────────────────────────── */
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

/* ── CTA ────────────────────────────────────────────────────────── */
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
  .hero-overlay { background: rgba(15,23,42,0.70); }
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
        <button class="theme-toggle" id="themeToggle" onclick="toggleTheme()" title="Toggle theme">🌙</button>
        <a href="{{ route('login') }}"    class="btn btn-outline btn-sm">Sign In</a>
        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Get Started</a>
      </div>
    </div>
  </div>
</nav>

{{-- ── Hero ──────────────────────────────────────────────────────── --}}
<div class="hero">
  <div class="hero-bg"></div>
  <div class="hero-overlay"></div>

  <div class="hero-card">
    <div class="hero-badge">🎓 Prince Abubakar Audu University, Anyigba</div>
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
    <span>↓</span>
    scroll
  </div>
</div>

{{-- ── Stats ─────────────────────────────────────────────────────── --}}
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

{{-- ── Features ──────────────────────────────────────────────────── --}}
<div class="features">
  <h2 class="section-heading">Everything You Need to Grow</h2>
  <p class="section-sub-heading">Powerful features built for PAAU students and mentors.</p>
  <div class="features-grid">
    <div class="feature-card">
      <div class="feature-icon">🧠</div>
      <h3>Smart Matching</h3>
      <p>Our algorithm pairs you with mentors based on your skills, goals, and academic level for the best fit.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">💬</div>
      <h3>Real-Time Chat</h3>
      <p>Direct messaging with your mentor for instant support, advice, and file sharing.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">🗺️</div>
      <h3>Learning Paths</h3>
      <p>Structured courses with modules, tasks, and progress tracking designed by your mentor.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">📡</div>
      <h3>Video Sessions</h3>
      <p>Schedule video calls, voice calls, or chat-only sessions and keep a full history of every meeting.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">🔄</div>
      <h3>Skill Exchange</h3>
      <p>Trade skills with peers — you teach what you know, they teach what you need.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">⬆️</div>
      <h3>Mentor Upgrade</h3>
      <p>High-performing mentees can apply to become mentors after meeting qualifications and earning a recommendation.</p>
    </div>
  </div>
</div>

{{-- ── CTA ───────────────────────────────────────────────────────── --}}
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

{{-- ── Footer ────────────────────────────────────────────────────── --}}
<footer style="background:var(--surface);border-top:1px solid var(--border);padding:28px 20px;text-align:center;color:var(--text-3);font-size:0.83rem">
  <p style="font-weight:600;color:var(--text-2);margin-bottom:6px">PAAUMENTOR · Prince Abubakar Audu University, Anyigba</p>
  <p>Final Year Project — Moses Goddey Joseph (23CS1004) · Supervisor: Mr. Richard Akomodi</p>
</footer>

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
