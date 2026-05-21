<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PAAUMENTOR — Mentorship that knows where you're going</title>
  <meta name="description" content="Peer + alumni mentorship for Prince Abubakar Audu University, matched by AI, verified by humans.">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.18.0/dist/tabler-icons.min.css">

  <link rel="stylesheet" href="{{ asset('css/pm-tokens.css') }}">
  <link rel="stylesheet" href="{{ asset('css/pm-landing.css') }}">

  {{-- Apply saved theme before paint to avoid flash --}}
  <script>
    (function () {
      var t = localStorage.getItem('pm-theme') || 'dark';
      document.documentElement.setAttribute('data-theme', t);
    })();
  </script>
</head>
<body>

<!-- ╔══════════ NAV ══════════╗ -->
<header class="pm-nav" role="banner">
  <div class="pm-nav__inner">
    <a href="{{ route('home') }}" class="pm-nav__brand" aria-label="PAAUMENTOR home">
      <span class="pm-nav__mark" aria-hidden="true"></span>
      PAAUMENTOR
    </a>
    <nav class="pm-nav__links" aria-label="Primary">
      <a href="#features" class="pm-nav__link">Features</a>
      <a href="#match"    class="pm-nav__link">Mentor match</a>
      <a href="#paths"    class="pm-nav__link">Paths</a>
      <a href="#certificate" class="pm-nav__link">Certificates</a>
    </nav>
    <div class="pm-nav__cta-group">
      <a href="{{ route('login') }}" class="pm-nav__signin">Sign in</a>
      <button id="pmThemeToggle" class="pm-theme-toggle" aria-label="Toggle theme" title="Toggle theme">
        <i class="ti ti-sun" aria-hidden="true"></i>
      </button>
      <a href="{{ route('register') }}" class="pm-btn pm-btn--primary pm-btn--sm">
        Get started <i class="ti ti-arrow-right" aria-hidden="true"></i>
      </a>
      <button class="pm-nav__burger" aria-label="Open menu" onclick="this.closest('.pm-nav').querySelector('.pm-nav__links').classList.toggle('open')">
        <i class="ti ti-menu-2" aria-hidden="true"></i>
      </button>
    </div>
  </div>
</header>

<!-- ╔══════════ HERO ══════════╗ -->
<section class="pm-hero pm-grain" aria-labelledby="hero-title">
  <div class="pm-aurora" aria-hidden="true">
    <div class="pm-aurora__blob pm-aurora__blob--1"></div>
    <div class="pm-aurora__blob pm-aurora__blob--2"></div>
    <div class="pm-aurora__blob pm-aurora__blob--3"></div>
  </div>

  <div class="pm-container">
    <div class="pm-hero__content">
      <div class="pm-live-pill" data-reveal>
        <span class="pm-pulse" aria-hidden="true"></span>
        Live at PAAU
        <span class="pm-live-pill__divider" aria-hidden="true"></span>
        <span class="pm-live-pill__count">
          <span data-count="2340" data-fmt="comma">0</span> mentees matched
        </span>
      </div>

      <h1 class="pm-hero__title" id="hero-title" data-reveal>
        Mentorship that <br>
        <span class="pm-hero__grad">knows where you're going.</span>
      </h1>

      <p class="pm-hero__lede" data-reveal>
        Peer and alumni mentorship for Prince Abubakar Audu University —
        <strong style="color:var(--pm-text);font-weight:500">matched by AI, verified by humans</strong>,
        tracked through structured learning paths, ending in a QR-coded certificate that actually means something.
      </p>

      <div class="pm-hero__cta-wrap" data-reveal>
        <div class="pm-orbit" aria-hidden="true">
          <span class="pm-orbit__particle"></span>
          <span class="pm-orbit__particle"></span>
          <span class="pm-orbit__particle"></span>
          <span class="pm-orbit__particle"></span>
          <span class="pm-orbit__particle"></span>
        </div>
        <a href="{{ route('register') }}" class="pm-btn pm-btn--primary pm-btn--lg" data-magnetic>
          Start as a mentee
          <i class="ti ti-arrow-right" aria-hidden="true"></i>
        </a>
        <a href="{{ route('register') }}" class="pm-btn pm-btn--ghost pm-btn--lg">
          Become a mentor
        </a>
      </div>
    </div>

    <div class="pm-trustedby" data-reveal>
      <p class="pm-trustedby__label">Trusted across PAAU faculties</p>
      <div class="pm-trustedby__row">
        <span class="pm-trustedby__faculty"><span class="pm-trustedby__code">FNS</span> Natural Sciences</span>
        <span class="pm-trustedby__faculty"><span class="pm-trustedby__code">FAS</span> Arts</span>
        <span class="pm-trustedby__faculty"><span class="pm-trustedby__code">FCS</span> Computing</span>
        <span class="pm-trustedby__faculty"><span class="pm-trustedby__code">FMS</span> Management</span>
        <span class="pm-trustedby__faculty"><span class="pm-trustedby__code">FED</span> Education</span>
        <span class="pm-trustedby__faculty"><span class="pm-trustedby__code">FLW</span> Law</span>
      </div>
    </div>
  </div>
</section>

<!-- ╔══════════ BENTO FEATURES ══════════╗ -->
<section class="pm-section" id="features" aria-labelledby="features-title">
  <div class="pm-container">
    <div class="pm-section__head" data-reveal>
      <span class="pm-eyebrow">
        <span class="pm-pulse" style="background:var(--pm-purple-soft);box-shadow:0 0 8px var(--pm-purple)" aria-hidden="true"></span>
        THE PLATFORM
      </span>
      <h2 class="pm-section__title" id="features-title">
        Everything you need to grow at PAAU, in one place.
      </h2>
      <p class="pm-section__lede">
        From the first AI match to a verifiable certificate — the whole journey is structured, social, and built for real outcomes.
      </p>
    </div>

    <div class="pm-bento">
      <article class="pm-bento__card pm-bento__card--xl" data-reveal data-tilt="2">
        <div class="pm-bento__icon pm-bento__icon--purple"><i class="ti ti-sparkles" aria-hidden="true"></i></div>
        <p class="pm-bento__kicker">AI-powered</p>
        <h3 class="pm-bento__title">AI mentor match</h3>
        <p class="pm-bento__copy">
          Tell us what you want to learn. The matcher walks the mentor graph, weighs skill overlap, availability and prior outcomes, and surfaces the human most likely to actually move you forward.
        </p>
        <div class="pm-bento__visual">
          <div class="pm-constellation" aria-hidden="true">
            <span class="pm-constellation__node" style="top:30%;left:18%">CS</span>
            <span class="pm-constellation__node" style="top:70%;left:28%">UX</span>
            <span class="pm-constellation__node" style="top:22%;left:62%">ML</span>
            <span class="pm-constellation__node" style="top:78%;left:72%">BE</span>
            <span class="pm-constellation__node" style="top:42%;left:82%">FE</span>
            <span class="pm-constellation__node pm-constellation__node--match" style="top:50%;left:50%">★</span>
            <span class="pm-constellation__line" style="top:50%;left:50%;width:34%;transform:translate(-100%,-50%) rotate(160deg)"></span>
            <span class="pm-constellation__line" style="top:50%;left:50%;width:28%;transform:translate(-100%,-50%) rotate(225deg)"></span>
            <span class="pm-constellation__line" style="top:50%;left:50%;width:32%;transform:translate(0,-50%) rotate(-30deg)"></span>
            <span class="pm-constellation__line" style="top:50%;left:50%;width:30%;transform:translate(0,-50%) rotate(40deg)"></span>
          </div>
        </div>
      </article>

      <article class="pm-bento__card pm-bento__card--sm" data-reveal>
        <div class="pm-bento__icon pm-bento__icon--coral"><i class="ti ti-message-2" aria-hidden="true"></i></div>
        <p class="pm-bento__kicker">AI</p>
        <h3 class="pm-bento__title">Study Buddy</h3>
        <p class="pm-bento__copy">A Claude AI partner that explains, quizzes, and never gets tired at 2 a.m.</p>
        <div class="pm-bento__visual pm-chatmini">
          <div class="pm-chatmini__bubble pm-chatmini__bubble--user">Explain Big-O like I'm new.</div>
          <div class="pm-chatmini__bubble pm-chatmini__bubble--ai">Think of it as "how much slower does this get when the input grows…"<span class="pm-chatmini__caret"></span></div>
        </div>
      </article>

      <article class="pm-bento__card pm-bento__card--lg" data-reveal>
        <div class="pm-bento__icon pm-bento__icon--teal"><i class="ti ti-route" aria-hidden="true"></i></div>
        <p class="pm-bento__kicker">STRUCTURED</p>
        <h3 class="pm-bento__title">Learning paths</h3>
        <p class="pm-bento__copy">Modules → tasks → submissions → grading. Real coursework, not vibes.</p>
        <div class="pm-bento__visual pm-paths">
          <div class="pm-paths__row">
            <div class="pm-paths__label">Frontend Foundations <span class="pm-paths__pct">62%</span></div>
            <div class="pm-paths__bar"><div class="pm-paths__fill" style="--pct:62%"></div></div>
          </div>
          <div class="pm-paths__row">
            <div class="pm-paths__label">Data Structures II <span class="pm-paths__pct">34%</span></div>
            <div class="pm-paths__bar"><div class="pm-paths__fill pm-paths__fill--purple" style="--pct:34%"></div></div>
          </div>
          <div class="pm-paths__row">
            <div class="pm-paths__label">UX for Engineers <span class="pm-paths__pct">88%</span></div>
            <div class="pm-paths__bar"><div class="pm-paths__fill pm-paths__fill--coral" style="--pct:88%"></div></div>
          </div>
        </div>
      </article>

      <article class="pm-bento__card pm-bento__card--sm" data-reveal>
        <div class="pm-bento__icon pm-bento__icon--amber"><i class="ti ti-video" aria-hidden="true"></i></div>
        <p class="pm-bento__kicker">SCHEDULE</p>
        <h3 class="pm-bento__title">Video, voice & chat</h3>
        <p class="pm-bento__copy">Book a 1:1, set a recurring slot, or run a study group — all on platform.</p>
        <div class="pm-bento__visual">
          <div style="display:flex;flex-direction:column;gap:8px">
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 10px;border-radius:8px;background:var(--pm-bg-2);border:1px solid var(--pm-border);font-size:12.5px">
              <span style="display:inline-flex;gap:6px;align-items:center"><i class="ti ti-video" style="color:var(--pm-amber)"></i> Tomiwa A.</span>
              <span style="font-family:var(--pm-font-mono);color:var(--pm-text-3);font-size:11px">FRI 16:00</span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 10px;border-radius:8px;background:var(--pm-bg-2);border:1px solid var(--pm-border);font-size:12.5px">
              <span style="display:inline-flex;gap:6px;align-items:center"><i class="ti ti-microphone" style="color:var(--pm-teal-soft)"></i> Group · DS</span>
              <span style="font-family:var(--pm-font-mono);color:var(--pm-text-3);font-size:11px">SAT 10:30</span>
            </div>
          </div>
        </div>
      </article>

      <article class="pm-bento__card pm-bento__card--wide" data-reveal>
        <div class="pm-bento__icon pm-bento__icon--teal"><i class="ti ti-certificate" aria-hidden="true"></i></div>
        <p class="pm-bento__kicker">VERIFIED</p>
        <h3 class="pm-bento__title">Certificates that survive scrutiny</h3>
        <p class="pm-bento__copy">Every cert passes AI assessment → mentor reflection → verifier approval, then ships with a QR-coded public record.</p>
        <div class="pm-bento__visual pm-certmini">
          <div class="pm-certmini__qr" aria-hidden="true"></div>
          <div class="pm-certmini__chain">
            <span class="pm-certmini__step"><i class="ti ti-circle-check" aria-hidden="true"></i> AI assessment</span>
            <span class="pm-certmini__step"><i class="ti ti-circle-check" aria-hidden="true"></i> Mentor reflection</span>
            <span class="pm-certmini__step"><i class="ti ti-circle-check" aria-hidden="true"></i> Verifier approval</span>
            <span class="pm-certmini__step" style="color:var(--pm-text-3)"><i class="ti ti-qrcode" aria-hidden="true"></i> Public QR record</span>
          </div>
        </div>
      </article>

      <article class="pm-bento__card pm-bento__card--md" data-reveal>
        <div class="pm-bento__icon pm-bento__icon--purple"><i class="ti ti-arrows-exchange" aria-hidden="true"></i></div>
        <p class="pm-bento__kicker">PEER-TO-PEER</p>
        <h3 class="pm-bento__title">Skill exchange & resource library</h3>
        <p class="pm-bento__copy">Trade what you know for what you don't. Browse a shared library curated by mentors and alumni.</p>
        <div class="pm-bento__visual pm-skillcloud">
          <span class="pm-skillcloud__chip pm-skillcloud__chip--purple">Laravel</span>
          <span class="pm-skillcloud__chip pm-skillcloud__chip--teal">UX research</span>
          <span class="pm-skillcloud__chip">Algorithms</span>
          <span class="pm-skillcloud__chip pm-skillcloud__chip--coral">Public speaking</span>
          <span class="pm-skillcloud__chip">Figma</span>
          <span class="pm-skillcloud__chip pm-skillcloud__chip--purple">SQL</span>
          <span class="pm-skillcloud__chip pm-skillcloud__chip--teal">React</span>
          <span class="pm-skillcloud__chip">Cybersecurity</span>
          <span class="pm-skillcloud__chip pm-skillcloud__chip--coral">Mobile dev</span>
        </div>
      </article>

      <article class="pm-bento__card pm-bento__card--md" data-reveal>
        <div class="pm-bento__icon pm-bento__icon--coral"><i class="ti ti-trophy" aria-hidden="true"></i></div>
        <p class="pm-bento__kicker">COMPETITIVE</p>
        <h3 class="pm-bento__title">Hackathons</h3>
        <p class="pm-bento__copy">Form teams, build under pressure, get scored by judges, and earn hackathon certificates for placing on the podium.</p>
        <div class="pm-bento__visual">
          <div style="display:flex;flex-direction:column;gap:6px">
            <div style="display:flex;gap:8px;align-items:center;padding:8px 10px;border-radius:8px;background:var(--pm-bg-2);border:1px solid var(--pm-border);font-size:12.5px">
              <span style="width:22px;height:22px;border-radius:6px;background:linear-gradient(135deg,var(--pm-amber),#d97706);display:grid;place-items:center;color:#1a0d07;font-size:11px;font-weight:700;flex-shrink:0">1</span>
              <span style="flex:1">Team Hydra</span>
              <span style="font-family:var(--pm-font-mono);color:var(--pm-teal-soft);font-size:11px">36.8</span>
            </div>
            <div style="display:flex;gap:8px;align-items:center;padding:8px 10px;border-radius:8px;background:var(--pm-bg-2);border:1px solid var(--pm-border);font-size:12.5px">
              <span style="width:22px;height:22px;border-radius:6px;background:rgba(255,255,255,0.08);display:grid;place-items:center;color:var(--pm-text-2);font-size:11px;font-weight:700;flex-shrink:0">2</span>
              <span style="flex:1;color:var(--pm-text-2)">DevStorm</span>
              <span style="font-family:var(--pm-font-mono);color:var(--pm-text-3);font-size:11px">34.1</span>
            </div>
          </div>
        </div>
      </article>
    </div>
  </div>
</section>

<!-- ╔══════════ MENTOR-MATCH DEMO ══════════╗ -->
<section class="pm-section" id="match" aria-labelledby="match-title">
  <div class="pm-container">
    <div class="pm-demo">
      <div class="pm-demo__copy" data-reveal>
        <span class="pm-eyebrow">◆ MATCHING ENGINE</span>
        <h2 class="pm-demo__title" id="match-title">Watch AI find your match.</h2>
        <p>
          Pick what you need help with. We orbit through the mentor graph — weighing skill overlap, availability and prior outcomes — until the right human floats to the top.
        </p>
        <ol class="pm-demo__steps">
          <li class="pm-demo__step">
            <span class="pm-demo__num">01</span>
            <div><b>You declare intent</b><span>Tap a few skills. We pick up faculty, year and history from your profile.</span></div>
          </li>
          <li class="pm-demo__step">
            <span class="pm-demo__num">02</span>
            <div><b>AI scores the graph</b><span>Claude ranks every active mentor against your goal in milliseconds.</span></div>
          </li>
          <li class="pm-demo__step">
            <span class="pm-demo__num">03</span>
            <div><b>You approve the human</b><span>We surface the top 3. You schedule the first session in one tap.</span></div>
          </li>
        </ol>
      </div>

      <div class="pm-matchcard" data-match data-reveal role="region" aria-label="Live mentor match demo">
        <div class="pm-matchcard__head">
          <span class="pm-matchcard__title">◆ pm.match()</span>
          <span class="pm-matchcard__status"><span class="pm-pulse"></span> live</span>
        </div>

        <div class="pm-matchcard__field">
          <p class="pm-matchcard__label">What do you want help with?</p>
          <div class="pm-chipgroup" role="group" aria-label="Pick skills to match against">
            <button class="pm-chip" type="button" data-skill="Laravel"        aria-pressed="true">Laravel</button>
            <button class="pm-chip" type="button" data-skill="React"          aria-pressed="false">React</button>
            <button class="pm-chip" type="button" data-skill="Data Structures" aria-pressed="false">Data Structures</button>
            <button class="pm-chip" type="button" data-skill="UX Research"    aria-pressed="false">UX Research</button>
            <button class="pm-chip" type="button" data-skill="Mobile"         aria-pressed="false">Mobile</button>
            <button class="pm-chip" type="button" data-skill="AI / ML"        aria-pressed="false">AI / ML</button>
            <button class="pm-chip" type="button" data-skill="Cybersecurity"  aria-pressed="false">Cybersecurity</button>
            <button class="pm-chip" type="button" aria-label="Add custom skill"><span class="pm-chip__plus">+ custom</span></button>
          </div>
        </div>

        <div class="pm-matchcard__thinking" aria-live="polite">
          <span class="pm-dots" aria-hidden="true"><span></span><span></span><span></span></span>
          <span>Walking the mentor graph<span class="pm-chatmini__caret"></span></span>
        </div>

        <div class="pm-mentorhit" aria-live="polite">
          <div class="pm-mentorhit__avatar" aria-hidden="true">TA</div>
          <div>
            <p class="pm-mentorhit__name">Tomiwa A.</p>
            <p class="pm-mentorhit__meta">4th yr · Computer Science</p>
            <span class="pm-mentorhit__verified"><i class="ti ti-rosette-discount-check" aria-hidden="true"></i> Verified mentor</span>
          </div>
          <div class="pm-mentorhit__score">
            <span class="pm-mentorhit__pct">92%</span>
            <span class="pm-mentorhit__pct-lbl">match</span>
          </div>
        </div>

        <p class="pm-matchcard__rationale">
          <b>Why this person:</b> Strong overlap on Laravel + React. 14 prior mentees, 91% completion rate, free Friday afternoons.
        </p>

        <button class="pm-matchcard__reset" type="button">
          <i class="ti ti-refresh" aria-hidden="true"></i> Reset
        </button>
      </div>
    </div>
  </div>
</section>

<!-- ╔══════════ CERTIFICATE SHOWCASE ══════════╗ -->
<section class="pm-section" id="certificate" aria-labelledby="cert-title">
  <div class="pm-container">
    <div class="pm-cert">
      <div class="pm-cert__card" data-reveal data-tilt="5">
        <div class="pm-cert__head">
          <span>PAAU · CERTIFICATE OF COMPLETION</span>
          <span class="pm-cert__seal" aria-hidden="true">P</span>
        </div>
        <div class="pm-cert__body">
          <p class="pm-cert__recipient-lbl">Awarded to</p>
          <h3 class="pm-cert__recipient">Adaeze Okwu</h3>
          <p class="pm-cert__path">for completing the Frontend Foundations path with distinction.</p>
        </div>
        <div class="pm-cert__foot">
          <div class="pm-cert__hash">
            <span>ID</span> PAAU-CERT-00482<br>
            <span>HASH</span> 0xa19f…c7d2<br>
            <span>Issued {{ now()->format('j M Y') }}</span>
          </div>
          <div class="pm-cert__qr" aria-label="QR code linking to public verification">
            <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <rect width="64" height="64" fill="#fff"></rect>
              <g fill="#0b0b14">
                <rect x="4"  y="4"  width="16" height="16"></rect>
                <rect x="44" y="4"  width="16" height="16"></rect>
                <rect x="4"  y="44" width="16" height="16"></rect>
                <rect x="8"  y="8"  width="8" height="8" fill="#fff"></rect>
                <rect x="48" y="8"  width="8" height="8" fill="#fff"></rect>
                <rect x="8"  y="48" width="8" height="8" fill="#fff"></rect>
                <rect x="24" y="4"  width="4" height="4"></rect>
                <rect x="32" y="8"  width="4" height="4"></rect>
                <rect x="28" y="12" width="4" height="4"></rect>
                <rect x="36" y="16" width="4" height="4"></rect>
                <rect x="24" y="20" width="4" height="4"></rect>
                <rect x="4"  y="24" width="4" height="4"></rect>
                <rect x="12" y="28" width="4" height="4"></rect>
                <rect x="20" y="32" width="4" height="4"></rect>
                <rect x="28" y="28" width="4" height="4"></rect>
                <rect x="36" y="32" width="4" height="4"></rect>
                <rect x="44" y="28" width="4" height="4"></rect>
                <rect x="52" y="32" width="4" height="4"></rect>
                <rect x="40" y="40" width="4" height="4"></rect>
                <rect x="48" y="44" width="4" height="4"></rect>
                <rect x="32" y="48" width="4" height="4"></rect>
                <rect x="24" y="52" width="4" height="4"></rect>
                <rect x="44" y="52" width="4" height="4"></rect>
                <rect x="56" y="48" width="4" height="4"></rect>
                <rect x="32" y="36" width="4" height="4"></rect>
              </g>
            </svg>
          </div>
        </div>
      </div>

      <div class="pm-chain" data-reveal>
        <div class="pm-chain__head">
          <span class="pm-eyebrow">◆ VERIFICATION CHAIN</span>
          <h2 class="pm-section__title" id="cert-title" style="font-size:clamp(28px,3.5vw,40px);margin-top:var(--pm-3)">
            A certificate worth the bytes it's printed on.
          </h2>
          <p style="color:var(--pm-text-2);margin:var(--pm-3) 0 0;max-width:480px">
            Each PAAUMENTOR certificate carries a three-step provenance chain — anyone can scan the QR and audit the full path that produced it.
          </p>
        </div>

        <div class="pm-chain__step">
          <div class="pm-chain__icon pm-chain__icon--purple"><i class="ti ti-sparkles" aria-hidden="true"></i></div>
          <div class="pm-chain__body">
            <h3 class="pm-chain__title">AI assessment <i class="ti ti-circle-check pm-chain__check" aria-hidden="true"></i></h3>
            <p>Claude grades every submission against the path rubric and flags weak spots before a human ever looks.</p>
            <p class="pm-chain__meta">avg. 4.2s per submission</p>
          </div>
        </div>

        <div class="pm-chain__step">
          <div class="pm-chain__icon pm-chain__icon--coral"><i class="ti ti-message-circle-2" aria-hidden="true"></i></div>
          <div class="pm-chain__body">
            <h3 class="pm-chain__title">Mentor reflection <i class="ti ti-circle-check pm-chain__check" aria-hidden="true"></i></h3>
            <p>The mentor writes a short, signed reflection — what the mentee did, what they need next.</p>
            <p class="pm-chain__meta">required · countersigned</p>
          </div>
        </div>

        <div class="pm-chain__step">
          <div class="pm-chain__icon pm-chain__icon--teal"><i class="ti ti-shield-check" aria-hidden="true"></i></div>
          <div class="pm-chain__body">
            <h3 class="pm-chain__title">Verifier approval <i class="ti ti-circle-check pm-chain__check" aria-hidden="true"></i></h3>
            <p>A faculty verifier signs off. The QR-coded record becomes publicly auditable.</p>
            <p class="pm-chain__meta">PAAU · faculty role</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ╔══════════ ROLES ══════════╗ -->
<section class="pm-section" id="paths" aria-labelledby="paths-title">
  <div class="pm-container">
    <div class="pm-section__head" data-reveal>
      <span class="pm-eyebrow">◆ CHOOSE YOUR PATH</span>
      <h2 class="pm-section__title" id="paths-title">Five ways to show up.</h2>
      <p class="pm-section__lede">PAAUMENTOR works because every role has a clear job. Pick yours.</p>
    </div>

    <div class="pm-roles">
      <a href="{{ route('register') }}" class="pm-role pm-role--mentee" data-reveal>
        <div class="pm-role__icon"><i class="ti ti-school" aria-hidden="true"></i></div>
        <h3 class="pm-role__name">Mentee</h3>
        <p class="pm-role__copy">Learn faster with a matched mentor, structured paths and a real cert at the end.</p>
        <span class="pm-role__cta">Get matched <i class="ti ti-arrow-right" aria-hidden="true"></i></span>
      </a>

      <a href="{{ route('register') }}" class="pm-role pm-role--mentor" data-reveal>
        <div class="pm-role__icon"><i class="ti ti-users" aria-hidden="true"></i></div>
        <h3 class="pm-role__name">Mentor</h3>
        <p class="pm-role__copy">Teach the path you wish you'd had. Track your mentees, grade work, write reflections.</p>
        <span class="pm-role__cta">Apply to mentor <i class="ti ti-arrow-right" aria-hidden="true"></i></span>
      </a>

      <a href="{{ route('register') }}" class="pm-role pm-role--alumni" data-reveal>
        <div class="pm-role__icon"><i class="ti ti-stars" aria-hidden="true"></i></div>
        <h3 class="pm-role__name">Alumni</h3>
        <p class="pm-role__copy">Give back without giving up your weekends. Open a single slot a month, change someone's year.</p>
        <span class="pm-role__cta">Join the alumni circle <i class="ti ti-arrow-right" aria-hidden="true"></i></span>
      </a>

      <a href="{{ route('login') }}" class="pm-role pm-role--verifier" data-reveal>
        <div class="pm-role__icon"><i class="ti ti-shield-check" aria-hidden="true"></i></div>
        <h3 class="pm-role__name">Verifier</h3>
        <p class="pm-role__copy">Faculty reviewers who approve certificates — the human checkpoint on the chain.</p>
        <span class="pm-role__cta">Verifier login <i class="ti ti-arrow-right" aria-hidden="true"></i></span>
      </a>

      <a href="{{ route('login') }}" class="pm-role pm-role--admin" data-reveal>
        <div class="pm-role__icon"><i class="ti ti-settings" aria-hidden="true"></i></div>
        <h3 class="pm-role__name">Admin</h3>
        <p class="pm-role__copy">Operate PAAU at platform scale — pipelines, funnels, completion rates and trust.</p>
        <span class="pm-role__cta">Admin console <i class="ti ti-arrow-right" aria-hidden="true"></i></span>
      </a>
    </div>
  </div>
</section>

<!-- ╔══════════ FOOTER ══════════╗ -->
<footer class="pm-foot pm-grain" role="contentinfo">
  <div class="pm-container">
    <div class="pm-foot__top">
      <div>
        <div class="pm-foot__brand">
          <span class="pm-nav__mark" aria-hidden="true"></span>
          PAAUMENTOR
        </div>
        <p class="pm-foot__tag">Peer + alumni mentorship for Prince Abubakar Audu University — matched by AI, verified by humans.</p>
        <form class="pm-newsletter" data-newsletter onsubmit="event.preventDefault()">
          <input type="email" name="email" placeholder="your@paau.edu.ng" aria-label="Email address" required>
          <button type="submit">Notify me <i class="ti ti-arrow-right" aria-hidden="true"></i></button>
        </form>
      </div>
      <div class="pm-foot__col">
        <h4>Product</h4>
        <ul>
          <li><a href="#features">Features</a></li>
          <li><a href="#match">Mentor match</a></li>
          <li><a href="#certificate">Certificates</a></li>
          <li><a href="#paths">For mentors</a></li>
        </ul>
      </div>
      <div class="pm-foot__col">
        <h4>Resources</h4>
        <ul>
          <li><a href="{{ route('login') }}">Resource library</a></li>
          <li><a href="{{ route('login') }}">Study groups</a></li>
          <li><a href="{{ route('login') }}">Skill exchange</a></li>
          <li><a href="{{ route('login') }}">Hackathons</a></li>
        </ul>
      </div>
      <div class="pm-foot__col">
        <h4>Account</h4>
        <ul>
          <li><a href="{{ route('login') }}">Sign in</a></li>
          <li><a href="{{ route('register') }}">Register</a></li>
          <li><a href="{{ route('certificates.verify', 'demo') }}">Verify a cert</a></li>
          <li><a href="{{ route('login') }}">Contact</a></li>
        </ul>
      </div>
    </div>

    <div class="pm-foot__bottom">
      <span>© {{ date('Y') }} PAAU · Department of Computer Science</span>
      <span class="pm-foot__status"><span class="pm-pulse"></span> All systems operational</span>
    </div>
  </div>
</footer>

<script src="{{ asset('js/pm-landing.js') }}" defer></script>
</body>
</html>
