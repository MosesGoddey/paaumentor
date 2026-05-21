/* ────────────────────────────────────────────────────────────────
   PAAUMENTOR · Landing interactions
   - Scroll-reveal observer
   - Nav scroll state
   - Animated counters
   - Mentor-match demo state machine
   - Magnetic CTA
   - Tilt on cards (subtle)
   All animations respect prefers-reduced-motion.
   ──────────────────────────────────────────────────────────────── */
(function () {
  "use strict";

  const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  /* ── 1. Scroll reveal ── */
  const revealEls = document.querySelectorAll("[data-reveal]");
  if (revealEls.length) {
    const io = new IntersectionObserver(
      (entries) => {
        entries.forEach((e) => {
          if (e.isIntersecting) {
            e.target.classList.add("is-visible");
            io.unobserve(e.target);
          }
        });
      },
      { threshold: 0.12, rootMargin: "0px 0px -40px 0px" }
    );
    revealEls.forEach((el) => io.observe(el));
  }

  /* ── 2. Nav scroll state ── */
  const nav = document.querySelector(".pm-nav");
  if (nav) {
    const onScroll = () => {
      nav.classList.toggle("pm-nav--scrolled", window.scrollY > 12);
    };
    window.addEventListener("scroll", onScroll, { passive: true });
    onScroll();
  }

  /* ── 3. Animated counters ── */
  const counters = document.querySelectorAll("[data-count]");
  if (counters.length) {
    const cio = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;
          const el = entry.target;
          const target = parseFloat(el.dataset.count);
          const duration = reduceMotion ? 0 : 1400;
          const start = performance.now();
          const fmt = el.dataset.fmt || "int";
          const step = (now) => {
            const t = Math.min(1, (now - start) / Math.max(duration, 1));
            const eased = 1 - Math.pow(1 - t, 3);
            const value = target * eased;
            el.textContent = fmt === "comma"
              ? Math.round(value).toLocaleString()
              : Math.round(value).toString();
            if (t < 1) requestAnimationFrame(step);
          };
          requestAnimationFrame(step);
          cio.unobserve(el);
        });
      },
      { threshold: 0.5 }
    );
    counters.forEach((el) => cio.observe(el));
  }

  /* ── 4. Mentor-match demo ── */
  const matchCard = document.querySelector("[data-match]");
  if (matchCard) {
    const chips = matchCard.querySelectorAll(".pm-chip[data-skill]");
    const thinking = matchCard.querySelector(".pm-matchcard__thinking");
    const reset = matchCard.querySelector(".pm-matchcard__reset");
    let timeout;

    const pickedSkills = () =>
      Array.from(chips).filter((c) => c.getAttribute("aria-pressed") === "true").map((c) => c.dataset.skill);

    const trigger = () => {
      const picks = pickedSkills();
      if (!picks.length) return;
      matchCard.classList.remove("is-resolved");
      thinking.style.display = "";
      clearTimeout(timeout);
      timeout = setTimeout(() => {
        matchCard.classList.add("is-resolved");
      }, reduceMotion ? 0 : 1600);
    };

    chips.forEach((chip) => {
      chip.addEventListener("click", () => {
        const pressed = chip.getAttribute("aria-pressed") === "true";
        chip.setAttribute("aria-pressed", String(!pressed));
        trigger();
      });
    });

    if (reset) {
      reset.addEventListener("click", () => {
        chips.forEach((c) => c.setAttribute("aria-pressed", "false"));
        matchCard.classList.remove("is-resolved");
        clearTimeout(timeout);
      });
    }
  }

  /* ── 5. Magnetic CTA ── */
  if (!reduceMotion) {
    document.querySelectorAll("[data-magnetic]").forEach((btn) => {
      const strength = 14;
      btn.addEventListener("mousemove", (e) => {
        const r = btn.getBoundingClientRect();
        const x = ((e.clientX - r.left) / r.width - 0.5) * strength;
        const y = ((e.clientY - r.top) / r.height - 0.5) * strength;
        btn.style.transform = `translate(${x}px, ${y}px)`;
      });
      btn.addEventListener("mouseleave", () => {
        btn.style.transform = "";
      });
    });
  }

  /* ── 6. Subtle card tilt ── */
  if (!reduceMotion) {
    document.querySelectorAll("[data-tilt]").forEach((card) => {
      let raf;
      card.addEventListener("mousemove", (e) => {
        const r = card.getBoundingClientRect();
        const x = (e.clientX - r.left) / r.width - 0.5;
        const y = (e.clientY - r.top) / r.height - 0.5;
        const max = parseFloat(card.dataset.tilt) || 4;
        cancelAnimationFrame(raf);
        raf = requestAnimationFrame(() => {
          card.style.transform = `perspective(1000px) rotateX(${-y * max}deg) rotateY(${x * max}deg)`;
        });
      });
      card.addEventListener("mouseleave", () => {
        cancelAnimationFrame(raf);
        card.style.transform = "";
      });
    });
  }

  /* ── 7. Newsletter (demo) ── */
  const news = document.querySelector("[data-newsletter]");
  if (news) {
    news.addEventListener("submit", (e) => {
      e.preventDefault();
      const btn = news.querySelector("button");
      const input = news.querySelector("input");
      if (!input.value) return;
      btn.textContent = "Subscribed";
      input.value = "";
      setTimeout(() => { btn.innerHTML = 'Notify me <i class="ti ti-arrow-right"></i>'; }, 2200);
    });
  }
})();

  /* ── 8. Theme toggle ── */
  (function () {
    const KEY = 'pm-theme';
    const html = document.documentElement;
    const btn  = document.getElementById('pmThemeToggle');

    const saved = localStorage.getItem(KEY) || 'dark';
    html.setAttribute('data-theme', saved);

    function sync() {
      const isDark = html.getAttribute('data-theme') !== 'light';
      if (btn) {
        btn.innerHTML = isDark
          ? '<i class="ti ti-sun" aria-hidden="true"></i>'
          : '<i class="ti ti-moon" aria-hidden="true"></i>';
        btn.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');
      }
    }

    if (btn) {
      btn.addEventListener('click', function () {
        const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-theme', next);
        localStorage.setItem(KEY, next);
        sync();
      });
    }

    sync();
  })();
