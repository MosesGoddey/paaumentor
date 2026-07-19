/* ============================================================
   PAAUMENTOR — App JS
   ============================================================ */

// ---------- Theme ----------
function toggleTheme() {
  const html = document.documentElement;
  const next = html.dataset.theme === 'dark' ? 'light' : 'dark';
  html.dataset.theme = next;
  localStorage.setItem('paaumentor_theme', next);
  // Sun/moon icons inside #themeToggle are swapped by CSS via [data-theme]
}

(function initTheme() {
  const saved = localStorage.getItem('paaumentor_theme');
  if (saved) {
    document.documentElement.dataset.theme = saved;
  }
})();

// ---------- Chart tooltip (shared) ----------
function chartTooltip() {
  let tip = document.getElementById('chart-tooltip');
  if (!tip) {
    tip = document.createElement('div');
    tip.id = 'chart-tooltip';
    tip.style.cssText =
      'position:fixed;z-index:10000;pointer-events:none;display:none;' +
      'background:#0f1b33;color:#fff;font:600 12px Inter,sans-serif;' +
      'padding:6px 10px;border-radius:6px;box-shadow:0 4px 14px rgba(0,0,0,0.25);white-space:nowrap';
    document.body.appendChild(tip);
  }
  return tip;
}
function showChartTip(e, html) {
  const tip = chartTooltip();
  tip.innerHTML = html;
  tip.style.display = 'block';
  tip.style.left = (e.clientX + 12) + 'px';
  tip.style.top  = (e.clientY - 32) + 'px';
}
function hideChartTip() {
  const tip = document.getElementById('chart-tooltip');
  if (tip) tip.style.display = 'none';
}

// Map mouse position to internal canvas coordinates (canvas may be CSS-scaled)
function canvasPos(canvas, e) {
  const rect = canvas.getBoundingClientRect();
  return {
    x: (e.clientX - rect.left) * (canvas.width / rect.width),
    y: (e.clientY - rect.top) * (canvas.height / rect.height),
  };
}

// ---------- Bar chart (vanilla canvas, hover-interactive) ----------
function drawBarChart(canvasId, labels, data, color, unit) {
  const canvas = document.getElementById(canvasId);
  if (!canvas) return;
  const ctx = canvas.getContext('2d');
  const W = canvas.width, H = canvas.height;
  const pad = { top: 20, right: 20, bottom: 30, left: 36 };
  const max = Math.max(...data, 1);
  const barW = (W - pad.left - pad.right) / labels.length;

  function render(hoverIdx) {
    const isDark = document.documentElement.dataset.theme === 'dark';
    const textColor = isDark ? '#94a3b8' : '#64748b';
    const gridColor = isDark ? '#334155' : '#e2e8f0';

    ctx.clearRect(0, 0, W, H);

    // Grid lines + y-axis tick values
    ctx.strokeStyle = gridColor;
    ctx.lineWidth = 1;
    for (let i = 0; i <= 4; i++) {
      const y = pad.top + (H - pad.top - pad.bottom) * (i / 4);
      ctx.beginPath(); ctx.moveTo(pad.left, y); ctx.lineTo(W - pad.right, y); ctx.stroke();
      ctx.fillStyle = textColor;
      ctx.font = '10px Inter, sans-serif';
      ctx.textAlign = 'right';
      ctx.textBaseline = 'middle';
      ctx.fillText(Math.round(max * (1 - i / 4)), pad.left - 6, y);
    }
    ctx.textBaseline = 'alphabetic';

    // Bars
    canvas._bars = [];
    data.forEach((v, i) => {
      const bh = ((v / max) * (H - pad.top - pad.bottom)) || 2;
      const x  = pad.left + i * barW + barW * 0.15;
      const y  = H - pad.bottom - bh;
      const bw = barW * 0.7;
      canvas._bars.push({ x, y: pad.top, w: bw, h: H - pad.top - pad.bottom, barTop: y, value: v, label: labels[i] });

      const hovered = i === hoverIdx;
      const grad = ctx.createLinearGradient(0, y, 0, H - pad.bottom);
      grad.addColorStop(0, color);
      grad.addColorStop(1, color + (hovered ? 'cc' : '66'));
      ctx.fillStyle = grad;
      ctx.globalAlpha = (hoverIdx !== undefined && hoverIdx !== null && !hovered) ? 0.45 : 1;
      ctx.beginPath();
      ctx.roundRect(x, y, bw, bh, 4);
      ctx.fill();
      ctx.globalAlpha = 1;

      // Value above the hovered bar
      if (hovered) {
        ctx.fillStyle = isDark ? '#f1f5f9' : '#0f172a';
        ctx.font = 'bold 12px Inter, sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText(v, x + bw / 2, Math.max(y - 6, 12));
      }

      // X label
      ctx.fillStyle = textColor;
      ctx.font = '11px Inter, sans-serif';
      ctx.textAlign = 'center';
      ctx.fillText(labels[i], x + bw / 2, H - 8);
    });
  }

  render(null);

  if (!canvas._barEvents) {
    canvas._barEvents = true;
    canvas.addEventListener('mousemove', e => {
      const p = canvasPos(canvas, e);
      const idx = (canvas._bars || []).findIndex(b => p.x >= b.x && p.x <= b.x + b.w && p.y >= b.y && p.y <= b.y + b.h);
      canvas.style.cursor = idx >= 0 ? 'pointer' : 'default';
      render(idx >= 0 ? idx : null);
      if (idx >= 0) {
        const b = canvas._bars[idx];
        showChartTip(e, b.label + ' — <b>' + b.value + '</b>' + (unit ? ' ' + unit : ''));
      } else {
        hideChartTip();
      }
    });
    canvas.addEventListener('mouseleave', () => { render(null); hideChartTip(); });
  }
}

// ---------- Donut chart (hover-interactive) ----------
function drawDonutChart(canvasId, segments) {
  const canvas = document.getElementById(canvasId);
  if (!canvas) return;
  const ctx = canvas.getContext('2d');
  const W = canvas.width, H = canvas.height;
  const cx = W / 2, cy = H / 2;
  const r = Math.min(W, H) / 2 - 10;
  const inner = r * 0.55;
  const total = segments.reduce((s, seg) => s + (seg.value || 0), 0) || 1;

  function render(hoverIdx) {
    ctx.clearRect(0, 0, W, H);
    let angle = -Math.PI / 2;
    canvas._segs = [];
    segments.forEach((seg, i) => {
      const slice = (seg.value / total) * 2 * Math.PI;
      canvas._segs.push({ start: angle, end: angle + slice, seg });
      const rad = i === hoverIdx ? r + 4 : r;
      ctx.globalAlpha = (hoverIdx !== undefined && hoverIdx !== null && i !== hoverIdx) ? 0.55 : 1;
      ctx.beginPath();
      ctx.moveTo(cx, cy);
      ctx.arc(cx, cy, rad, angle, angle + slice);
      ctx.closePath();
      ctx.fillStyle = seg.color;
      ctx.fill();
      ctx.globalAlpha = 1;
      angle += slice;
    });

    // Hole
    ctx.beginPath();
    ctx.arc(cx, cy, inner, 0, 2 * Math.PI);
    ctx.fillStyle = getComputedStyle(document.documentElement).getPropertyValue('--surface').trim() || '#fff';
    ctx.fill();

    // Centre text — hovered segment value, otherwise the total
    const isDark = document.documentElement.dataset.theme === 'dark';
    ctx.fillStyle = isDark ? '#f1f5f9' : '#0f172a';
    ctx.font = 'bold 18px Sora, sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText(hoverIdx !== null && hoverIdx !== undefined ? segments[hoverIdx].value : total, cx, cy);
  }

  render(null);

  if (!canvas._donutEvents) {
    canvas._donutEvents = true;
    canvas.addEventListener('mousemove', e => {
      const p = canvasPos(canvas, e);
      const dx = p.x - cx, dy = p.y - cy;
      const dist = Math.sqrt(dx * dx + dy * dy);
      let idx = -1;
      if (dist >= inner && dist <= r + 4) {
        let a = Math.atan2(dy, dx);
        if (a < -Math.PI / 2) a += 2 * Math.PI; // normalise to segment range
        idx = (canvas._segs || []).findIndex(s => a >= s.start && a < s.end);
      }
      canvas.style.cursor = idx >= 0 ? 'pointer' : 'default';
      render(idx >= 0 ? idx : null);
      if (idx >= 0) {
        const seg = canvas._segs[idx].seg;
        const pct = Math.round((seg.value / total) * 100);
        showChartTip(e, (seg.label ? seg.label + ' — ' : '') + '<b>' + seg.value + '</b> (' + pct + '%)');
      } else {
        hideChartTip();
      }
    });
    canvas.addEventListener('mouseleave', () => { render(null); hideChartTip(); });
  }
}

// ---------- Flash messages auto-dismiss ----------
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-autohide]').forEach(el => {
    setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.remove(), 400); }, 4000);
  });
});
