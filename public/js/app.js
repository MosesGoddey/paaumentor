/* ============================================================
   PAAUMENTOR — App JS
   ============================================================ */

// ---------- Theme ----------
function toggleTheme() {
  const html = document.documentElement;
  const next = html.dataset.theme === 'dark' ? 'light' : 'dark';
  html.dataset.theme = next;
  localStorage.setItem('paaumentor_theme', next);
  document.getElementById('themeToggle').textContent = next === 'dark' ? '☀️' : '🌙';
}

(function initTheme() {
  const saved = localStorage.getItem('paaumentor_theme');
  if (saved) {
    document.documentElement.dataset.theme = saved;
    const btn = document.getElementById('themeToggle');
    if (btn) btn.textContent = saved === 'dark' ? '☀️' : '🌙';
  }
})();

// ---------- Bar chart (vanilla canvas) ----------
function drawBarChart(canvasId, labels, data, color) {
  const canvas = document.getElementById(canvasId);
  if (!canvas) return;
  const ctx = canvas.getContext('2d');
  const W = canvas.width, H = canvas.height;
  const pad = { top: 20, right: 20, bottom: 30, left: 36 };
  const max = Math.max(...data, 1);

  ctx.clearRect(0, 0, W, H);

  const barW = (W - pad.left - pad.right) / labels.length;
  const isDark = document.documentElement.dataset.theme === 'dark';
  const textColor = isDark ? '#94a3b8' : '#64748b';
  const gridColor = isDark ? '#334155' : '#e2e8f0';

  // Grid lines
  ctx.strokeStyle = gridColor;
  ctx.lineWidth = 1;
  for (let i = 0; i <= 4; i++) {
    const y = pad.top + (H - pad.top - pad.bottom) * (i / 4);
    ctx.beginPath(); ctx.moveTo(pad.left, y); ctx.lineTo(W - pad.right, y); ctx.stroke();
  }

  // Bars
  data.forEach((v, i) => {
    const bh = ((v / max) * (H - pad.top - pad.bottom)) || 2;
    const x  = pad.left + i * barW + barW * 0.15;
    const y  = H - pad.bottom - bh;
    const bw = barW * 0.7;

    const grad = ctx.createLinearGradient(0, y, 0, H - pad.bottom);
    grad.addColorStop(0, color);
    grad.addColorStop(1, color + '66');
    ctx.fillStyle = grad;
    ctx.beginPath();
    ctx.roundRect(x, y, bw, bh, 4);
    ctx.fill();

    // Label
    ctx.fillStyle = textColor;
    ctx.font = '11px Inter, sans-serif';
    ctx.textAlign = 'center';
    ctx.fillText(labels[i], x + bw / 2, H - 8);
  });
}

// ---------- Donut chart ----------
function drawDonutChart(canvasId, segments) {
  const canvas = document.getElementById(canvasId);
  if (!canvas) return;
  const ctx = canvas.getContext('2d');
  const W = canvas.width, H = canvas.height;
  const cx = W / 2, cy = H / 2;
  const r = Math.min(W, H) / 2 - 10;
  const inner = r * 0.55;
  const total = segments.reduce((s, seg) => s + (seg.value || 0), 0) || 1;

  ctx.clearRect(0, 0, W, H);
  let angle = -Math.PI / 2;
  segments.forEach(seg => {
    const slice = (seg.value / total) * 2 * Math.PI;
    ctx.beginPath();
    ctx.moveTo(cx, cy);
    ctx.arc(cx, cy, r, angle, angle + slice);
    ctx.closePath();
    ctx.fillStyle = seg.color;
    ctx.fill();
    angle += slice;
  });

  // Hole
  ctx.beginPath();
  ctx.arc(cx, cy, inner, 0, 2 * Math.PI);
  ctx.fillStyle = getComputedStyle(document.documentElement).getPropertyValue('--surface').trim() || '#fff';
  ctx.fill();

  // Centre text
  const isDark = document.documentElement.dataset.theme === 'dark';
  ctx.fillStyle = isDark ? '#f1f5f9' : '#0f172a';
  ctx.font = 'bold 18px Sora, sans-serif';
  ctx.textAlign = 'center';
  ctx.textBaseline = 'middle';
  ctx.fillText(total, cx, cy);
}

// ---------- Flash messages auto-dismiss ----------
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-autohide]').forEach(el => {
    setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.remove(), 400); }, 4000);
  });
});
