<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PAAUMENTOR | Peer Mentorship Platform</title>
<meta name="description" content="Peer and alumni mentorship for Prince Abubakar Audu University. Matched by AI, verified by humans.">
<link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<style>
/* Transparent navbar over hero image; turns solid once the page scrolls */
.navbar { transition: background 0.25s, border-color 0.25s, box-shadow 0.25s; }
.navbar:not(.is-scrolled) {
  background: rgba(255,255,255,0.15) !important;
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-bottom: 1px solid rgba(255,255,255,0.2) !important;
}
.navbar:not(.is-scrolled) .nav-brand-text,
.navbar:not(.is-scrolled) .nav-brand-text span,
.navbar:not(.is-scrolled) .nav-brand,
.navbar:not(.is-scrolled) .nav-link { color: #fff !important; }
.navbar:not(.is-scrolled) .btn-outline { color: #fff !important; border-color: rgba(255,255,255,0.6) !important; }
.navbar:not(.is-scrolled) .btn-outline:hover { background: rgba(255,255,255,0.15) !important; }
.navbar.is-scrolled {
  background: color-mix(in srgb, var(--surface) 88%, transparent);
  backdrop-filter: blur(14px) saturate(160%);
  -webkit-backdrop-filter: blur(14px) saturate(160%);
  box-shadow: 0 6px 24px rgba(15,23,42,0.08);
}

.nav-links { display: flex; gap: 4px; flex: 0 1 auto; }
.nav-links .nav-link {
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--text-2);
  padding: 6px 14px;
  border: none;
  border-radius: 999px;
  transition: background 0.2s, color 0.2s;
}
.nav-links .nav-link:hover { color: var(--text); background: color-mix(in srgb, var(--text) 8%, transparent); }
.navbar:not(.is-scrolled) .nav-links .nav-link:hover { background: rgba(255,255,255,0.16); }

/*  Scroll reveal  */
[data-reveal] { opacity: 0; transform: translateY(18px); transition: opacity 0.7s cubic-bezier(.2,.7,.2,1), transform 0.7s cubic-bezier(.2,.7,.2,1); }
[data-reveal].is-visible { opacity: 1; transform: none; }
@media (prefers-reduced-motion: reduce) {
  [data-reveal] { opacity: 1; transform: none; transition: none; }
  .pulse, .typing-caret, .scroll-hint { animation: none !important; }
}

/* Live pulse dot */
.pulse {
  display: inline-block;
  width: 8px; height: 8px;
  border-radius: 50%;
  background: #22c55e;
  box-shadow: 0 0 0 0 rgba(34,197,94,0.6);
  animation: pulse 2s infinite;
  flex-shrink: 0;
}
@keyframes pulse { 0%{box-shadow:0 0 0 0 rgba(34,197,94,0.55)} 70%{box-shadow:0 0 0 8px rgba(34,197,94,0)} 100%{box-shadow:0 0 0 0 rgba(34,197,94,0)} }

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

/* Background image: sized to full height so no vertical cropping */
.hero-bg {
  position: absolute;
  inset: 0;
  background-image: url('{{ asset('images/hero-bg.jpg') }}');
  background-size: auto 118%;
  background-position: right 52%;
  background-repeat: no-repeat;
  filter: brightness(0.85);
}

/* Gradient overlay: darkens left side for card readability, fades out on the right */
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
  max-width: 480px;
  width: 100%;
  text-align: center;
  color: #fff;
  box-shadow: 0 8px 32px rgba(0,0,0,0.2), inset 0 1px 0 rgba(255,255,255,0.4);
  text-shadow: 0 1px 4px rgba(0,0,0,0.4);
}

.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(255,255,255,0.15);
  border: 1px solid rgba(255,255,255,0.3);
  color: #fff;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  border-radius: 999px;
  padding: 5px 14px;
  margin-bottom: 14px;
}

.hero-card h1 {
  font-family: 'Sora', sans-serif;
  font-size: 1.95rem;
  font-weight: 800;
  line-height: 1.15;
  margin-bottom: 12px;
  text-shadow: 0 2px 12px rgba(0,0,0,0.3);
}

.hero-card h1 span {
  background: linear-gradient(90deg, #93c5fd 0%, #c4b5fd 55%, #f9a8d4 100%);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
  text-shadow: none;
}

.hero-card p {
  font-size: 0.88rem;
  line-height: 1.6;
  margin-bottom: 20px;
  opacity: 0.9;
}
.hero-card p strong { font-weight: 700; opacity: 1; }

.hero-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }

.hero-live {
  margin-top: 18px;
  padding-top: 14px;
  border-top: 1px solid rgba(255,255,255,0.22);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-size: 0.78rem;
  opacity: 0.9;
}
.hero-live b { font-weight: 800; }

.btn-hero-primary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #fff;
  color: #1d4ed8;
  padding: 12px 28px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 0.95rem;
  text-decoration: none;
  transition: all 0.2s;
  box-shadow: 0 4px 16px rgba(0,0,0,0.15);
  text-shadow: none;
}
.btn-hero-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.2); }
.btn-hero-primary svg { transition: transform 0.2s; }
.btn-hero-primary:hover svg { transform: translateX(3px); }

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
  color: rgba(255,255,255,0.6);
  font-size: 0.72rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  animation: bounce 2s infinite;
}
.scroll-hint span { width: 1px; height: 22px; background: linear-gradient(to bottom, transparent, rgba(255,255,255,0.7)); }
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
[data-theme="dark"] .stat-value { color: #93c5fd; }
.stat-label { font-size: 0.82rem; color: var(--text-3); margin-top: 4px; }

/*  Section heads  */
.eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--text-3);
  margin-bottom: 12px;
}
.eyebrow::before { content: ''; width: 18px; height: 2px; border-radius: 2px; background: linear-gradient(90deg, #3b82f6, #7c3aed); }
.section-head { text-align: center; max-width: 640px; margin: 0 auto 48px; }
.section-heading {
  text-align: center;
  font-family: 'Sora', sans-serif;
  font-size: 2rem;
  font-weight: 800;
  line-height: 1.2;
  margin-bottom: 10px;
}
.section-sub-heading {
  text-align: center;
  color: var(--text-3);
  font-size: 0.95rem;
  line-height: 1.65;
  margin-bottom: 48px;
}
.section-head .section-sub-heading { margin-bottom: 0; }

/*  Features: bento grid  */
.features { padding: 88px 20px; background: var(--bg); }
.bento {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 18px;
  max-width: 1100px;
  margin: 0 auto;
}
.bento-card {
  grid-column: span 3;
  position: relative;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 20px;
  padding: 26px 24px;
  transition: transform 0.25s, box-shadow 0.25s, border-color 0.25s;
}
.bento-card--4 { grid-column: span 4; }
.bento-card--2 { grid-column: span 2; }
.bento-card::before {
  content: '';
  position: absolute;
  inset: 0;
  pointer-events: none;
  background: radial-gradient(400px circle at var(--mx, 50%) var(--my, 0%), color-mix(in srgb, var(--accent, #3b82f6) 10%, transparent), transparent 60%);
  opacity: 0;
  transition: opacity 0.3s;
}
.bento-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); border-color: color-mix(in srgb, var(--accent, #3b82f6) 45%, var(--border)); }
.bento-card:hover::before { opacity: 1; }
.bento-icon {
  width: 46px; height: 46px;
  border-radius: 13px;
  display: flex; align-items: center; justify-content: center;
  color: #fff;
  background: var(--accent, #3b82f6);
  box-shadow: 0 6px 18px color-mix(in srgb, var(--accent, #3b82f6) 35%, transparent);
  margin-bottom: 16px;
}
.bento-kicker {
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--accent, #3b82f6);
  margin-bottom: 4px;
}
[data-theme="dark"] .bento-kicker { color: color-mix(in srgb, var(--accent) 60%, #fff); }
.bento-card h3 { font-family: 'Sora', sans-serif; font-size: 1.08rem; font-weight: 800; margin-bottom: 8px; }
.bento-card > p { font-size: 0.85rem; color: var(--text-3); line-height: 1.65; }
.bento-visual { margin-top: auto; padding-top: 20px; }

/* Matching constellation */
.constellation { position: relative; height: 150px; border-radius: 14px; background: var(--surface-2); border: 1px solid var(--border); overflow: hidden; }
.constellation svg { position: absolute; inset: 0; width: 100%; height: 100%; }
.constellation line { stroke: var(--accent); stroke-width: 1.5; stroke-dasharray: 4 5; opacity: 0.5; animation: dash 18s linear infinite; }
@keyframes dash { to { stroke-dashoffset: -200; } }
.cnode {
  position: absolute;
  transform: translate(-50%, -50%);
  font-size: 0.68rem;
  font-weight: 700;
  padding: 4px 9px;
  border-radius: 999px;
  background: var(--surface);
  border: 1px solid var(--border);
  color: var(--text-2);
  white-space: nowrap;
}
.cnode--you { background: var(--accent); color: #fff; border-color: transparent; box-shadow: 0 0 0 6px color-mix(in srgb, var(--accent) 18%, transparent); }
.cnode--match { border-color: #22c55e; color: #15803d; }
[data-theme="dark"] .cnode--match { color: #86efac; }

/* Chat mini */
.chatmini { display: flex; flex-direction: column; gap: 8px; }
.chat-bubble { font-size: 0.78rem; line-height: 1.5; padding: 9px 12px; border-radius: 14px; max-width: 92%; }
.chat-bubble--user { align-self: flex-end; background: var(--accent); color: #fff; border-bottom-right-radius: 4px; }
.chat-bubble--ai { align-self: flex-start; background: var(--surface-2); color: var(--text-2); border: 1px solid var(--border); border-bottom-left-radius: 4px; }
.typing-caret { display: inline-block; width: 2px; height: 0.95em; background: currentColor; vertical-align: -2px; margin-left: 2px; animation: blink 1s steps(1) infinite; }
@keyframes blink { 50% { opacity: 0; } }

/* Path progress */
.paths { display: flex; flex-direction: column; gap: 12px; }
.path-label { display: flex; justify-content: space-between; font-size: 0.78rem; font-weight: 600; color: var(--text-2); margin-bottom: 5px; }
.path-label span { font-variant-numeric: tabular-nums; color: var(--text-3); }
.path-bar { height: 7px; border-radius: 999px; background: var(--surface-2); overflow: hidden; }
.path-fill { height: 100%; width: 0; border-radius: inherit; background: var(--fill, var(--accent)); transition: width 1.4s cubic-bezier(.2,.7,.2,1) 0.2s; }
.is-visible .path-fill { width: var(--pct); }

/* Session rows / notification rows */
.rows { display: flex; flex-direction: column; gap: 8px; }
.row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  padding: 9px 12px;
  border-radius: 10px;
  background: var(--surface-2);
  border: 1px solid var(--border);
  font-size: 0.8rem;
  font-weight: 600;
  color: var(--text-2);
}
.row-left { display: inline-flex; align-items: center; gap: 8px; }
.row-time { font-size: 0.7rem; font-weight: 700; letter-spacing: 0.05em; color: var(--text-3); font-variant-numeric: tabular-nums; }

/* Chain chips */
.chain { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
.chain-step {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 0.76rem;
  font-weight: 600;
  padding: 6px 11px;
  border-radius: 999px;
  background: color-mix(in srgb, var(--accent) 10%, var(--surface));
  border: 1px solid color-mix(in srgb, var(--accent) 30%, var(--border));
  color: var(--text-2);
}
.chain-step svg { color: var(--accent); }
.chain-arrow { color: var(--text-3); font-size: 0.8rem; }

/* Skill cloud */
.skillcloud { display: flex; flex-wrap: wrap; gap: 7px; }
.skill-chip { font-size: 0.74rem; font-weight: 600; padding: 5px 11px; border-radius: 999px; background: var(--surface-2); border: 1px solid var(--border); color: var(--text-2); }
.skill-chip:nth-child(3n+1) { background: color-mix(in srgb, #7c3aed 10%, var(--surface)); border-color: color-mix(in srgb, #7c3aed 30%, var(--border)); }
.skill-chip:nth-child(3n+2) { background: color-mix(in srgb, #0d9488 10%, var(--surface)); border-color: color-mix(in srgb, #0d9488 30%, var(--border)); }

/*  How it works  */
.how-section { padding: 88px 20px; background: var(--surface); border-top: 1px solid var(--border); }
.how-steps {
  list-style: none;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 18px;
  max-width: 1100px;
  margin: 0 auto;
  counter-reset: step;
}
.how-step {
  position: relative;
  padding: 24px 22px;
  border-radius: 18px;
  background: var(--bg);
  border: 1px solid var(--border);
}
.how-num {
  font-family: 'Sora', sans-serif;
  font-size: 0.8rem;
  font-weight: 800;
  letter-spacing: 0.06em;
  color: #fff;
  width: 38px; height: 38px;
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  background: linear-gradient(135deg, #3b82f6, #7c3aed);
  margin-bottom: 14px;
}
.how-step h3 { font-size: 0.98rem; font-weight: 700; margin-bottom: 6px; }
.how-step p { font-size: 0.84rem; color: var(--text-3); line-height: 1.6; }
.how-step:not(:last-child)::after {
  content: '';
  position: absolute;
  top: 43px;
  right: -14px;
  width: 10px; height: 10px;
  border-top: 2px solid var(--border);
  border-right: 2px solid var(--border);
  transform: rotate(45deg);
}

/*  Certificate showcase  */
.cert-section { padding: 88px 20px; background: var(--bg); border-top: 1px solid var(--border); }
.cert-wrap {
  max-width: 1100px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 56px;
  align-items: center;
}
.cert-card {
  position: relative;
  border-radius: 20px;
  padding: 28px;
  color: #0f172a;
  background: linear-gradient(160deg, #fffdf7 0%, #f5f1e6 100%);
  border: 1px solid #e7dfc8;
  box-shadow: 0 30px 60px -20px rgba(15,23,42,0.35), 0 0 0 8px color-mix(in srgb, var(--surface) 70%, transparent);
  transform: rotate(-2deg);
  transition: transform 0.4s cubic-bezier(.2,.7,.2,1);
}
.cert-card:hover { transform: rotate(0deg) translateY(-4px); }
.cert-card::before {
  content: '';
  position: absolute;
  inset: 10px;
  border: 1.5px solid #d6c48f;
  border-radius: 12px;
  pointer-events: none;
}
.cert-head { display: flex; justify-content: space-between; align-items: center; font-size: 0.66rem; font-weight: 800; letter-spacing: 0.14em; color: #8a6d1f; padding: 6px 8px 0; }
.cert-seal {
  width: 44px; height: 44px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  background: radial-gradient(circle at 30% 30%, #fcd34d, #b45309);
  color: #fff; box-shadow: 0 4px 12px rgba(180,83,9,0.35);
}
.cert-body { text-align: center; padding: 26px 10px 22px; }
.cert-awarded { font-size: 0.72rem; letter-spacing: 0.12em; text-transform: uppercase; color: #64748b; margin-bottom: 6px; }
.cert-name { font-family: 'Sora', sans-serif; font-size: 1.7rem; font-weight: 800; color: #1e3a8a; margin-bottom: 8px; }
.cert-path { font-size: 0.84rem; color: #475569; }
.cert-foot { display: flex; justify-content: space-between; align-items: flex-end; gap: 12px; padding: 0 8px 4px; }
.cert-meta { font-size: 0.7rem; line-height: 1.7; color: #64748b; font-variant-numeric: tabular-nums; }
.cert-meta b { color: #334155; }
.cert-qr {
  width: 64px; height: 64px; border-radius: 8px; padding: 6px;
  background: #fff; border: 1px solid #e2e8f0; color: #0f172a;
  display: flex; align-items: center; justify-content: center;
}

.chain-list { list-style: none; display: flex; flex-direction: column; gap: 4px; margin-top: 28px; }
.chain-item { display: flex; gap: 16px; position: relative; padding-bottom: 20px; }
.chain-item:not(:last-child)::before {
  content: '';
  position: absolute;
  left: 21px; top: 46px; bottom: 0;
  width: 2px;
  background: linear-gradient(to bottom, var(--border), transparent);
}
.chain-icon {
  flex-shrink: 0;
  width: 44px; height: 44px;
  border-radius: 13px;
  display: flex; align-items: center; justify-content: center;
  color: #fff;
  background: var(--accent);
}
.chain-item h3 { font-size: 0.98rem; font-weight: 700; margin-bottom: 3px; display: flex; align-items: center; gap: 6px; }
.chain-item h3 svg { color: #22c55e; }
.chain-item p { font-size: 0.85rem; color: var(--text-3); line-height: 1.6; }
.cert-section .section-heading, .cert-section .section-sub-heading { text-align: left; }
.cert-section .section-sub-heading { margin-bottom: 0; }
.verify-link { display: inline-flex; align-items: center; gap: 6px; font-size: 0.85rem; font-weight: 700; color: var(--blue-500); margin-top: 6px; }
[data-theme="dark"] .verify-link { color: #93c5fd; }

/*  Featured Mentors  */
.mentors-section { padding: 88px 20px; background: var(--surface); border-top: 1px solid var(--border); }
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
.mentor-verified {
  position: absolute;
  top: 14px; left: 14px;
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 0.66rem;
  font-weight: 700;
  color: #15803d;
}
[data-theme="dark"] .mentor-verified { color: #86efac; }
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

/*  FAQ  */
.faq-section { padding: 88px 20px; background: var(--bg); border-top: 1px solid var(--border); }
.faq-list { max-width: 760px; margin: 0 auto; display: flex; flex-direction: column; gap: 12px; }
.faq-item {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 14px;
  overflow: hidden;
  transition: border-color 0.2s;
}
.faq-item[open] { border-color: var(--blue-400); }
.faq-item summary {
  list-style: none;
  cursor: pointer;
  padding: 18px 22px;
  font-weight: 700;
  font-size: 0.95rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  user-select: none;
}
.faq-item summary::-webkit-details-marker { display: none; }
.faq-item summary::after {
  content: '+';
  font-size: 1.3rem;
  font-weight: 400;
  color: var(--blue-500);
  transition: transform 0.2s;
  flex-shrink: 0;
}
.faq-item[open] summary::after { transform: rotate(45deg); }
.faq-item p {
  padding: 0 22px 18px;
  font-size: 0.87rem;
  color: var(--text-3);
  line-height: 1.7;
  margin: 0;
}

/*  CTA  */
.cta-section {
  position: relative;
  overflow: hidden;
  padding: 88px 20px;
  text-align: center;
  background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 100%);
  color: #fff;
}
.cta-section::before, .cta-section::after {
  content: '';
  position: absolute;
  border-radius: 50%;
  filter: blur(60px);
  opacity: 0.45;
}
.cta-section::before { width: 420px; height: 420px; background: #7c3aed; top: -180px; right: -80px; }
.cta-section::after  { width: 320px; height: 320px; background: #0d9488; bottom: -160px; left: -60px; }
.cta-section h2 { font-family:'Sora',sans-serif; font-size: 2.2rem; font-weight: 800; margin-bottom: 16px; position:relative;z-index:1; }
.cta-section p  { font-size: 1rem; opacity: 0.85; max-width: 500px; margin: 0 auto 36px; line-height:1.7; position:relative;z-index:1; }
.cta-buttons { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; position:relative;z-index:1; }

/*  Footer  */
.site-footer { background: var(--surface); border-top: 1px solid var(--border); padding: 56px 20px 28px; color: var(--text-3); font-size: 0.85rem; }
.footer-top {
  max-width: 1100px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr;
  gap: 32px;
  padding-bottom: 32px;
  border-bottom: 1px solid var(--border);
}
.footer-brand { display: flex; align-items: center; gap: 10px; font-family: 'Sora', sans-serif; font-weight: 800; color: var(--text); margin-bottom: 10px; }
.footer-tag { max-width: 320px; line-height: 1.65; }
.footer-col h4 { font-size: 0.72rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; color: var(--text-2); margin-bottom: 12px; }
.footer-col ul { list-style: none; display: flex; flex-direction: column; gap: 8px; }
.footer-col a:hover { color: var(--text); }
.footer-bottom {
  max-width: 1100px;
  margin: 0 auto;
  padding-top: 22px;
  display: flex;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
  font-size: 0.78rem;
}

@media (max-width: 960px) {
  .nav-links { display: none; }
  .bento-card, .bento-card--4, .bento-card--2 { grid-column: span 6; }
  .how-steps { grid-template-columns: repeat(2, 1fr); }
  .how-step::after { display: none; }
  .cert-wrap { grid-template-columns: 1fr; gap: 40px; }
  .cert-card { max-width: 460px; margin: 0 auto; width: 100%; }
  .footer-top { grid-template-columns: 1fr 1fr; }
  .footer-top > :first-child { grid-column: span 2; }
}

@media (max-width: 768px) {
  .hero { justify-content: center; padding: 40px 20px; padding-top: 120px; }
  .hero-overlay { background: rgba(15,23,42,0.75); }
  .hero-card { padding: 36px 24px; }
  .hero-card h1 { font-size: 2rem; }
  .stat-item { padding: 16px 24px; border-right: none; border-bottom: 1px solid var(--border); width: 100%; }
  .stat-item:last-child { border-bottom: none; }
  .section-heading { font-size: 1.6rem; }
  .cta-section h2 { font-size: 1.6rem; }
  .how-steps { grid-template-columns: 1fr; }
  .footer-top { grid-template-columns: 1fr; }
  .footer-top > :first-child { grid-column: auto; }
}
</style>
</head>
<body>

<nav class="navbar" id="landingNav">
  <div class="container">
    <div class="nav-inner">
      <a href="{{ route('home') }}" class="nav-brand">
        <div class="nav-logo">PM</div>
        <span class="nav-brand-text">PAAU<span>MENTOR</span></span>
      </a>
      <div class="nav-links">
        <a href="#features"    class="nav-link">Features</a>
        <a href="#how"         class="nav-link">How it works</a>
        <a href="#certificate" class="nav-link">Certificates</a>
        <a href="#faq"         class="nav-link">FAQ</a>
      </div>
      <div class="nav-actions" style="margin-left:auto">
        <button class="theme-toggle" id="themeToggle" onclick="toggleTheme()" title="Toggle theme">
          <x-icon name="moon" :size="16" class="icon-moon" />
          <x-icon name="sun"  :size="16" class="icon-sun" />
        </button>
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

  <div class="hero-card" data-reveal>
    <div class="hero-badge"><span class="pulse"></span> Prince Abubakar Audu University, Anyigba</div>
    <h1>Your Academic Success <span>Starts Here</span></h1>
    <p>Connect with senior students and alumni who've walked your path. <strong>Matched by AI, verified by humans</strong>, guided through structured learning paths, and finished with a certificate anyone can verify.</p>
    <div class="hero-actions">
      @auth
        <a href="{{ route('dashboard') }}" class="btn-hero-primary">
          Go to Dashboard
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </a>
      @else
        <a href="{{ route('register') }}" class="btn-hero-primary">
          Get Started Free
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </a>
        <a href="{{ route('login') }}"    class="btn-hero-secondary">Sign In</a>
      @endauth
    </div>
    @if($stats['mentorships'] > 0)
      <div class="hero-live">
        <span class="pulse"></span>
        <span><b>{{ number_format($stats['mentorships']) }}</b> {{ Str::plural('mentorship', $stats['mentorships']) }} started · <b>{{ number_format($stats['mentors']) }}</b> active {{ Str::plural('mentor', $stats['mentors']) }}</span>
      </div>
    @endif
  </div>

  <div class="scroll-hint">
    <span></span>
    scroll
  </div>
</div>

{{--  Stats  --}}
<div class="stats">
  <div class="stat-item">
    <div class="stat-value count-up" data-target="{{ $stats['mentors'] }}">0</div>
    <div class="stat-label">Verified Mentors</div>
  </div>
  <div class="stat-item">
    <div class="stat-value count-up" data-target="{{ $stats['students'] }}">0</div>
    <div class="stat-label">Students on Board</div>
  </div>
  <div class="stat-item">
    <div class="stat-value count-up" data-target="{{ $stats['skills'] }}">0</div>
    <div class="stat-label">Skills Available</div>
  </div>
  <div class="stat-item">
    <div class="stat-value count-up" data-target="{{ $stats['certificates'] }}">0</div>
    <div class="stat-label">Certificates Issued</div>
  </div>
  <div class="stat-item">
    <div class="stat-value count-up" data-target="100" data-suffix="%">0</div>
    <div class="stat-label">Free to Use</div>
  </div>
</div>

{{--  Features: bento grid  --}}
<section class="features" id="features">
  <div class="section-head" data-reveal>
    <div class="eyebrow">The Platform</div>
    <h2 class="section-heading">Everything You Need to Grow, in One Place</h2>
    <p class="section-sub-heading">From your first AI match to a verifiable certificate, the whole journey is structured, social, and built for real outcomes.</p>
  </div>

  <div class="bento">
    {{-- Smart matching --}}
    <article class="bento-card bento-card--4" style="--accent:#3b82f6" data-reveal>
      <div class="bento-icon"><x-icon name="sparkles" :size="22" /></div>
      <div class="bento-kicker">AI-assisted</div>
      <h3>Smart Mentor Matching</h3>
      <p>Tell us what you want to learn. We weigh skill overlap, department, level and availability, and AI reads your goals in plain words to surface the people most likely to move you forward.</p>
      <div class="bento-visual">
        <div class="constellation" aria-hidden="true">
          <svg viewBox="0 0 100 100" preserveAspectRatio="none">
            <line x1="50" y1="50" x2="16" y2="28" />
            <line x1="50" y1="50" x2="26" y2="76" />
            <line x1="50" y1="50" x2="72" y2="22" />
            <line x1="50" y1="50" x2="84" y2="70" />
          </svg>
          <span class="cnode" style="left:16%;top:28%">Web Dev</span>
          <span class="cnode" style="left:26%;top:76%">UI/UX</span>
          <span class="cnode cnode--match" style="left:72%;top:22%">✓ 92% match</span>
          <span class="cnode" style="left:84%;top:70%">Data Science</span>
          <span class="cnode cnode--you" style="left:50%;top:50%">You</span>
        </div>
      </div>
    </article>

    {{-- Mentor AI --}}
    <article class="bento-card bento-card--2" style="--accent:#7c3aed" data-reveal>
      <div class="bento-icon"><x-icon name="message-circle" :size="22" /></div>
      <div class="bento-kicker">Always on</div>
      <h3>Mentor AI & Study Buddy</h3>
      <p>An AI partner that explains, quizzes, and never gets tired at 2 a.m.</p>
      <div class="bento-visual chatmini" aria-hidden="true">
        <div class="chat-bubble chat-bubble--user">Explain Big-O like I'm new.</div>
        <div class="chat-bubble chat-bubble--ai">Think of it as "how much slower does this get when the input grows…"<span class="typing-caret"></span></div>
      </div>
    </article>

    {{-- Learning paths --}}
    <article class="bento-card" style="--accent:#0d9488" data-reveal>
      <div class="bento-icon"><x-icon name="book-open" :size="22" /></div>
      <div class="bento-kicker">Structured</div>
      <h3>Learning Paths</h3>
      <p>Modules → tasks → submissions → grading. Your mentor builds the path (AI can draft one in seconds) and you both track real progress.</p>
      <div class="bento-visual paths" aria-hidden="true">
        <div>
          <div class="path-label">Frontend Foundations <span>62%</span></div>
          <div class="path-bar"><div class="path-fill" style="--pct:62%"></div></div>
        </div>
        <div>
          <div class="path-label">Data Structures <span>34%</span></div>
          <div class="path-bar"><div class="path-fill" style="--pct:34%;--fill:#7c3aed"></div></div>
        </div>
        <div>
          <div class="path-label">UI/UX for Beginners <span>88%</span></div>
          <div class="path-bar"><div class="path-fill" style="--pct:88%;--fill:#ec4899"></div></div>
        </div>
      </div>
    </article>

    {{-- Sessions --}}
    <article class="bento-card" style="--accent:#ec4899" data-reveal>
      <div class="bento-icon"><x-icon name="video" :size="22" /></div>
      <div class="bento-kicker">Real-time</div>
      <h3>Chat, Voice & Video Sessions</h3>
      <p>Message your mentor, share files, and schedule video calls, voice calls or chat-only sessions, with a full history of every meeting.</p>
      <div class="bento-visual rows" aria-hidden="true">
        <div class="row">
          <span class="row-left"><x-icon name="video" :size="15" style="color:#ec4899" /> 1:1 with your mentor</span>
          <span class="row-time">FRI 16:00</span>
        </div>
        <div class="row">
          <span class="row-left"><x-icon name="mic" :size="15" style="color:#0d9488" /> Study group call</span>
          <span class="row-time">SAT 10:30</span>
        </div>
      </div>
    </article>

    {{-- Certificates --}}
    <article class="bento-card bento-card--4" style="--accent:#f59e0b" data-reveal>
      <div class="bento-icon"><x-icon name="award" :size="22" /></div>
      <div class="bento-kicker">Verified</div>
      <h3>Certificates That Survive Scrutiny</h3>
      <p>Every certificate passes an assessment, a mentor's sign-off and a verifier's approval, then ships with a QR code anyone can scan to confirm it's genuine.</p>
      <div class="bento-visual chain" aria-hidden="true">
        <span class="chain-step"><x-icon name="check" :size="14" :stroke="3" /> Assessment</span>
        <span class="chain-arrow">→</span>
        <span class="chain-step"><x-icon name="check" :size="14" :stroke="3" /> Mentor sign-off</span>
        <span class="chain-arrow">→</span>
        <span class="chain-step"><x-icon name="check" :size="14" :stroke="3" /> Verifier approval</span>
        <span class="chain-arrow">→</span>
        <span class="chain-step"><x-icon name="qr-code" :size="14" /> Public QR record</span>
      </div>
    </article>

    {{-- Skill exchange --}}
    <article class="bento-card bento-card--2" style="--accent:#f97316" data-reveal>
      <div class="bento-icon"><x-icon name="arrow-right-left" :size="22" /></div>
      <div class="bento-kicker">Peer-to-peer</div>
      <h3>Skill Exchange</h3>
      <p>Teach what you know, learn what you don't.</p>
      <div class="bento-visual skillcloud" aria-hidden="true">
        @forelse($skillCloud as $skill)
          <span class="skill-chip">{{ $skill }}</span>
        @empty
          @foreach(['Web Dev', 'Python', 'UI/UX', 'Data Science', 'Mobile Dev', 'Cybersecurity'] as $skill)
            <span class="skill-chip">{{ $skill }}</span>
          @endforeach
        @endforelse
      </div>
    </article>

    {{-- Community --}}
    <article class="bento-card" style="--accent:#6366f1" data-reveal>
      <div class="bento-icon"><x-icon name="users-round" :size="22" /></div>
      <div class="bento-kicker">Community</div>
      <h3>Study Groups, Hackathons & Resources</h3>
      <p>Join study groups with group chat and calls, compete in hackathons, climb the leaderboards, and browse a resource library shared by mentors and peers.</p>
    </article>

    {{-- Mentor upgrade --}}
    <article class="bento-card" style="--accent:#16a34a" data-reveal>
      <div class="bento-icon"><x-icon name="circle-arrow-up" :size="22" /></div>
      <div class="bento-kicker">Grow into it</div>
      <h3>Mentor Upgrade</h3>
      <p>Every student starts as a mentee. Prove a skill through a short assessment, get verified, and start mentoring others across campus.</p>
    </article>
  </div>
</section>

{{--  How it works  --}}
<section class="how-section" id="how">
  <div class="section-head" data-reveal>
    <div class="eyebrow">How it works</div>
    <h2 class="section-heading">From Sign-up to Certificate in Four Steps</h2>
    <p class="section-sub-heading">A clear path for every student, and a way to give back once you've made it.</p>
  </div>
  <ol class="how-steps">
    <li class="how-step" data-reveal>
      <div class="how-num">01</div>
      <h3>Create your free account</h3>
      <p>Add your department, level and the skills you want to learn. That's what we match on.</p>
    </li>
    <li class="how-step" data-reveal>
      <div class="how-num">02</div>
      <h3>Get matched with a mentor</h3>
      <p>Browse ranked mentors or describe your goals to the AI matcher, then send a request.</p>
    </li>
    <li class="how-step" data-reveal>
      <div class="how-num">03</div>
      <h3>Learn on a structured path</h3>
      <p>Complete modules and tasks, get graded feedback, and meet in chat, voice or video sessions.</p>
    </li>
    <li class="how-step" data-reveal>
      <div class="how-num">04</div>
      <h3>Earn your certificate</h3>
      <p>Pass the assessment, get approved, and receive a QR-verified certificate, then mentor others.</p>
    </li>
  </ol>
</section>

{{--  Certificate showcase  --}}
<section class="cert-section" id="certificate">
  <div class="cert-wrap">
    <div class="cert-card" data-reveal aria-label="Sample certificate preview">
      <div class="cert-head">
        <span>PAAUMENTOR · CERTIFICATE OF COMPLETION</span>
        <span class="cert-seal"><x-icon name="award" :size="22" /></span>
      </div>
      <div class="cert-body">
        <div class="cert-awarded">Awarded to</div>
        <div class="cert-name">Your Name Here</div>
        <div class="cert-path">for completing the <b>Frontend Foundations</b> learning path.</div>
      </div>
      <div class="cert-foot">
        <div class="cert-meta">
          <b>ID</b> PAAU-CERT-XXXXX<br>
          <b>Mentor</b> Verified PAAU mentor<br>
          Scan to verify
        </div>
        <div class="cert-qr"><x-icon name="qr-code" :size="48" :stroke="1.6" /></div>
      </div>
    </div>

    <div data-reveal>
      <div class="eyebrow">Verification chain</div>
      <h2 class="section-heading">A Certificate Worth Showing Off</h2>
      <p class="section-sub-heading">Each PAAUMENTOR certificate goes through three checks before it's issued, and anyone can scan its QR code to confirm it's real.</p>

      <ol class="chain-list">
        <li class="chain-item">
          <div class="chain-icon" style="--accent:#7c3aed"><x-icon name="sparkles" :size="20" /></div>
          <div>
            <h3>Assessment <x-icon name="check" :size="16" :stroke="3" /></h3>
            <p>An AI-generated assessment on what your learning path covered. You need to pass to move on.</p>
          </div>
        </li>
        <li class="chain-item">
          <div class="chain-icon" style="--accent:#ec4899"><x-icon name="user-check" :size="20" /></div>
          <div>
            <h3>Mentor sign-off <x-icon name="check" :size="16" :stroke="3" /></h3>
            <p>Your mentor, who graded your work all along, confirms you've earned it.</p>
          </div>
        </li>
        <li class="chain-item">
          <div class="chain-icon" style="--accent:#0d9488"><x-icon name="shield-check" :size="20" /></div>
          <div>
            <h3>Verifier approval <x-icon name="check" :size="16" :stroke="3" /></h3>
            <p>An independent verifier reviews the request, and your QR-coded certificate is issued with a public verification page.</p>
          </div>
        </li>
      </ol>
    </div>
  </div>
</section>

{{--  Featured Mentors  --}}
@if(isset($featuredMentors) && $featuredMentors->count())
<section class="mentors-section" id="mentors">
  <div class="section-head" data-reveal>
    <div class="eyebrow">Meet the mentors</div>
    <h2 class="section-heading">Learn From People Who've Walked Your Path</h2>
    <p class="section-sub-heading">Verified senior students and alumni, ready to help.</p>
  </div>
  <div class="mentors-grid">
    @foreach($featuredMentors as $mentor)
      <div class="mentor-card" data-reveal>
        <span class="mentor-verified"><x-icon name="shield-check" :size="13" :stroke="2.4" /> Verified</span>
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
          <x-icon name="star" :size="13" fill="currentColor" style="vertical-align:-2px" /> {{ number_format($mentor->average_rating, 1) }}
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
</section>
@endif

{{--  FAQ  --}}
<section class="faq-section" id="faq">
  <div class="section-head" data-reveal>
    <div class="eyebrow">FAQ</div>
    <h2 class="section-heading">Frequently Asked Questions</h2>
    <p class="section-sub-heading">Everything you need to know before getting started.</p>
  </div>
  <div class="faq-list" data-reveal>
    <details class="faq-item">
      <summary>What is PAAUMENTOR and who is it for?</summary>
      <p>PAAUMENTOR is the peer-mentorship platform for students of Prince Abubakar Audu University, Anyigba. It connects students who want to learn a skill (mentees) with fellow students who already have it (mentors), for academic support, tech skills, and career growth. It is completely free.</p>
    </details>
    <details class="faq-item">
      <summary>How do I find and connect with a mentor?</summary>
      <p>Create a free account, open <strong>Find Mentor</strong>, and browse mentors by skill, department, or rating. Send a mentorship request describing what you want to learn. Once the mentor accepts, you can chat, share files, schedule sessions, and even make voice or video calls right inside the platform.</p>
    </details>
    <details class="faq-item">
      <summary>How do I become a mentor?</summary>
      <p>Every student starts as a mentee. Once you can demonstrate a skill, apply for a mentor upgrade from your dashboard. You'll take a short assessment on your chosen skill, and a verifier reviews your application. When approved, your profile becomes visible to mentees across campus.</p>
    </details>
    <details class="faq-item">
      <summary>What are learning paths?</summary>
      <p>A learning path is a structured programme your mentor creates for you: modules and practical tasks with deadlines. You submit each task on the platform, your mentor grades it and gives feedback, so both of you can track real progress instead of just having conversations.</p>
    </details>
    <details class="faq-item">
      <summary>Do I get a certificate?</summary>
      <p>Yes. After completing a learning path, you can request a certificate. You'll take an assessment to prove what you've learned, and once you pass, your mentor signs off and a verifier approves it, PAAUMENTOR issues you a certificate with a QR code that anyone can scan to verify.</p>
    </details>
    <details class="faq-item">
      <summary>What else can I do besides one-on-one mentorship?</summary>
      <p>Quite a lot! Join <strong>study groups</strong> with group chat and group calls, trade skills with other students through <strong>skill exchange</strong> (teach what you know, learn what you don't), compete in <strong>hackathons</strong>, browse shared <strong>resources</strong>, and ask <strong>Mentor AI</strong> anything, any time.</p>
    </details>
    <details class="faq-item">
      <summary>Is my data safe?</summary>
      <p>Your conversations are private between you and your mentor, and your account is protected by your password. We only use your academic details (department, level) to match you with relevant mentors. Nothing is shared outside the platform.</p>
    </details>
  </div>
</section>

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
<footer class="site-footer">
  <div class="footer-top">
    <div>
      <div class="footer-brand"><div class="nav-logo">PM</div> PAAUMENTOR</div>
      <p class="footer-tag">Peer and alumni mentorship for Prince Abubakar Audu University, Anyigba. Matched by AI, verified by humans.</p>
    </div>
    <div class="footer-col">
      <h4>Platform</h4>
      <ul>
        <li><a href="#features">Features</a></li>
        <li><a href="#how">How it works</a></li>
        <li><a href="#certificate">Certificates</a></li>
        <li><a href="#mentors">Mentors</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Get started</h4>
      <ul>
        <li><a href="{{ route('register') }}">Create account</a></li>
        <li><a href="{{ route('login') }}">Sign in</a></li>
        <li><a href="#faq">FAQ</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Built by</h4>
      <ul>
        <li>Moses Goddey Joseph</li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <span>© {{ date('Y') }} PAAUMENTOR · Prince Abubakar Audu University, Anyigba</span>
    <span>Final Year Project · Department of Computer Science</span>
  </div>
</footer>

@include('partials.mentor-ai-widget')
<script src="{{ asset('js/app.js') }}"></script>
<script>
// Count-up stats: replays every time the stats scroll back into view
const observer = new IntersectionObserver(entries => {
  entries.forEach(entry => {
    const el     = entry.target;
    const suffix = el.dataset.suffix || '';
    if (!entry.isIntersecting) {
      cancelAnimationFrame(el._countFrame);
      el.textContent = '0' + suffix;
      return;
    }
    const target = parseInt(el.dataset.target, 10);
    const duration = 1400;
    const start  = performance.now();
    function step(now) {
      const progress = Math.min((now - start) / duration, 1);
      const ease     = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.floor(ease * target) + suffix;
      if (progress < 1) el._countFrame = requestAnimationFrame(step);
      else el.textContent = target + suffix;
    }
    el._countFrame = requestAnimationFrame(step);
  });
}, { threshold: 0.5 });

document.querySelectorAll('.count-up').forEach(el => observer.observe(el));

// Scroll reveal
const revealObserver = new IntersectionObserver(entries => {
  entries.forEach(entry => {
    if (!entry.isIntersecting) return;
    entry.target.classList.add('is-visible');
    revealObserver.unobserve(entry.target);
  });
}, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

document.querySelectorAll('[data-reveal]').forEach((el, i) => {
  el.style.transitionDelay = (i % 3) * 80 + 'ms';
  revealObserver.observe(el);
});

// Solid navbar once the hero is scrolled past
const nav = document.getElementById('landingNav');
function syncNav() { nav.classList.toggle('is-scrolled', window.scrollY > 40); }
window.addEventListener('scroll', syncNav, { passive: true });
syncNav();

// Spotlight follows the cursor on bento cards
document.querySelectorAll('.bento-card').forEach(card => {
  card.addEventListener('pointermove', e => {
    const r = card.getBoundingClientRect();
    card.style.setProperty('--mx', (e.clientX - r.left) + 'px');
    card.style.setProperty('--my', (e.clientY - r.top) + 'px');
  });
});
</script>
</body>
</html>
