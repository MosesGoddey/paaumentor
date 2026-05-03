<!DOCTYPE html>
<html lang="en" data-theme="{{ session('theme','light') }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Mentor Upgrade Assessment</title>
<link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<style>
body { background:#0f172a; margin:0; font-family:'Sora',sans-serif; }
.asmnt-wrap { min-height:100vh; display:flex; flex-direction:column; align-items:center; padding:24px 16px 40px; }
.asmnt-header { width:100%; max-width:780px; display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; flex-wrap:wrap; gap:10px; }
.asmnt-title { color:rgba(255,255,255,0.6); font-size:0.82rem; font-weight:600; letter-spacing:0.04em; }
.asmnt-timer { font-size:1.5rem; font-weight:800; color:#fff; letter-spacing:0.02em; font-variant-numeric:tabular-nums; transition:color 0.3s; }
.asmnt-timer.warn   { color:#fbbf24; }
.asmnt-timer.danger { color:#ef4444; animation:timerPulse 0.5s ease infinite alternate; }
@keyframes timerPulse { from{transform:scale(1)} to{transform:scale(1.06)} }
.asmnt-progress-track { width:100%; max-width:780px; height:5px; background:rgba(255,255,255,0.1); border-radius:99px; margin-bottom:18px; overflow:hidden; }
.asmnt-progress-fill { height:100%; background:linear-gradient(90deg,#f97316,#8b5cf6); border-radius:99px; transition:width 0.3s ease; }
.q-grid { width:100%; max-width:780px; display:flex; flex-wrap:wrap; gap:8px; margin-bottom:18px; }
.q-dot { width:36px; height:36px; border-radius:10px; border:2px solid rgba(255,255,255,0.15); background:rgba(255,255,255,0.05); color:rgba(255,255,255,0.5); font-size:0.75rem; font-weight:700; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all 0.15s; }
.q-dot:hover { border-color:rgba(249,115,22,0.6); color:#fff; }
.q-dot.active { border-color:#f97316; background:rgba(249,115,22,0.2); color:#fff; }
.q-dot.answered { border-color:#22c55e; background:rgba(34,197,94,0.15); color:#86efac; }
.q-dot.answered.active { border-color:#22c55e; background:rgba(34,197,94,0.25); }
.q-card { width:100%; max-width:780px; background:#1e293b; border-radius:24px; padding:36px 40px; border:1px solid rgba(255,255,255,0.08); box-shadow:0 24px 64px rgba(0,0,0,0.5); }
.q-meta { font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:rgba(255,255,255,0.3); margin-bottom:18px; }
.q-text { font-size:1.05rem; font-weight:600; color:#f1f5f9; line-height:1.7; margin-bottom:26px; }
.q-options { display:flex; flex-direction:column; gap:10px; }
.q-option { display:flex; align-items:center; gap:14px; padding:13px 18px; border-radius:14px; border:2px solid rgba(255,255,255,0.1); cursor:pointer; transition:all 0.15s; color:#cbd5e1; font-size:0.92rem; user-select:none; }
.q-option:hover { border-color:rgba(249,115,22,0.6); background:rgba(249,115,22,0.08); color:#fff; }
.q-option.selected { border-color:#f97316; background:rgba(249,115,22,0.15); color:#fff; }
.q-option-letter { width:28px; height:28px; border-radius:8px; background:rgba(255,255,255,0.08); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:0.78rem; flex-shrink:0; transition:background 0.15s; }
.q-option.selected .q-option-letter { background:#f97316; }
.asmnt-nav { display:flex; gap:12px; margin-top:24px; }
.asmnt-btn { flex:1; padding:13px; border-radius:14px; border:none; font-family:'Sora',sans-serif; font-weight:700; font-size:0.92rem; cursor:pointer; transition:all 0.18s; }
.asmnt-btn-prev { background:rgba(255,255,255,0.07); color:rgba(255,255,255,0.7); border:1.5px solid rgba(255,255,255,0.12); }
.asmnt-btn-prev:hover { background:rgba(255,255,255,0.12); color:#fff; }
.asmnt-btn-prev:disabled { opacity:0.3; cursor:not-allowed; }
.asmnt-btn-next { background:linear-gradient(135deg,#f97316,#ea580c); color:#fff; }
.asmnt-btn-next:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(249,115,22,0.35); }
.asmnt-btn-next:disabled { opacity:0.3; cursor:not-allowed; }
.asmnt-btn-submit { width:100%; max-width:780px; margin-top:14px; padding:14px; border-radius:14px; border:none; font-family:'Sora',sans-serif; font-weight:700; font-size:0.95rem; cursor:pointer; background:linear-gradient(135deg,#059669,#10b981); color:#fff; transition:all 0.18s; }
.asmnt-btn-submit:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(16,185,129,0.35); }
.q-legend { width:100%; max-width:780px; display:flex; gap:18px; flex-wrap:wrap; margin-bottom:14px; }
.q-legend-item { display:flex; align-items:center; gap:6px; font-size:0.72rem; color:rgba(255,255,255,0.4); }
.q-legend-dot { width:12px; height:12px; border-radius:4px; }
.tab-modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.8); z-index:9999; align-items:center; justify-content:center; }
.tab-modal.show { display:flex; }
.tab-modal-box { background:#1e293b; border:2px solid #fbbf24; border-radius:20px; padding:36px 40px; max-width:440px; text-align:center; color:#fff; }
.tab-modal-box h3 { font-size:1.2rem; margin:0 0 12px; }
.tab-modal-box p  { font-size:0.88rem; opacity:0.75; margin:0 0 24px; }
.confirm-modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.8); z-index:9998; align-items:center; justify-content:center; }
.confirm-modal.show { display:flex; }
.confirm-modal-box { background:#1e293b; border:2px solid #f97316; border-radius:20px; padding:36px 40px; max-width:460px; text-align:center; color:#fff; }
.confirm-modal-box h3 { font-size:1.15rem; margin:0 0 10px; }
.confirm-modal-box p  { font-size:0.85rem; opacity:0.7; margin:0 0 6px; }
.confirm-modal-actions { display:flex; gap:12px; margin-top:24px; }
.confirm-modal-actions button { flex:1; padding:12px; border-radius:12px; font-weight:700; font-size:0.88rem; cursor:pointer; border:none; font-family:'Sora',sans-serif; }
@media(max-width:600px){ .q-card{padding:22px 18px} .asmnt-timer{font-size:1.2rem} .q-dot{width:30px;height:30px;font-size:0.7rem} }
</style>
</head>
<body>

<div class="asmnt-wrap">

  <div class="asmnt-header">
    <div>
      <div class="asmnt-title">🧠 Mentor Upgrade Assessment</div>
      <div style="font-size:0.7rem;color:rgba(255,255,255,0.3);margin-top:2px">Prove your teaching-level knowledge</div>
    </div>
    <div style="display:flex;align-items:center;gap:14px">
      <div style="font-size:0.78rem;color:rgba(255,255,255,0.35)">
        <span id="answeredCount">0</span>/{{ $orderedQuestions->count() }} answered
      </div>
      <div class="asmnt-timer" id="examTimer">--:--</div>
    </div>
  </div>

  <div class="asmnt-progress-track">
    <div class="asmnt-progress-fill" id="progressBar" style="width:0%"></div>
  </div>

  <div class="q-legend">
    <div class="q-legend-item"><div class="q-legend-dot" style="background:#f97316;border:2px solid #f97316"></div> Current</div>
    <div class="q-legend-item"><div class="q-legend-dot" style="background:rgba(34,197,94,0.2);border:2px solid #22c55e"></div> Answered</div>
    <div class="q-legend-item"><div class="q-legend-dot" style="background:rgba(255,255,255,0.05);border:2px solid rgba(255,255,255,0.15)"></div> Unanswered</div>
  </div>

  <div class="q-grid" id="qGrid">
    @foreach($orderedQuestions as $index => $question)
    <div class="q-dot {{ $index === 0 ? 'active' : '' }}" id="dot-{{ $index }}"
         onclick="goToQuestion({{ $index }})">{{ $index + 1 }}</div>
    @endforeach
  </div>

  <form id="submitForm" method="POST" action="{{ route('upgrade-assessment.submit', $attempt) }}" style="display:none">
    @csrf
    <div id="hiddenAnswers"></div>
  </form>

  <div class="q-card" id="questionCard">
    @foreach($orderedQuestions as $index => $question)
    <div class="q-slide" id="q-{{ $index }}" style="{{ $index > 0 ? 'display:none' : '' }}"
         data-qid="{{ $question->id }}" data-index="{{ $index }}">
      <div class="q-meta">Question {{ $index + 1 }} of {{ $orderedQuestions->count() }}</div>
      <div class="q-text">{{ $question->question }}</div>
      <div class="q-options">
        @foreach($question->options as $oi => $opt)
        <div class="q-option" onclick="selectOption(this, {{ $index }}, {{ $oi }})" data-index="{{ $oi }}">
          <div class="q-option-letter">{{ ['A','B','C','D'][$oi] }}</div>
          <div>{{ $opt }}</div>
        </div>
        @endforeach
      </div>
    </div>
    @endforeach

    <div class="asmnt-nav">
      <button class="asmnt-btn asmnt-btn-prev" id="prevBtn" disabled onclick="prevQuestion()">← Previous</button>
      <button class="asmnt-btn asmnt-btn-next" id="nextBtn" onclick="nextQuestion()">Next →</button>
    </div>
  </div>

  <button class="asmnt-btn-submit" onclick="confirmSubmit()">✓ Submit Test</button>
</div>

<div class="tab-modal" id="tabModal">
  <div class="tab-modal-box">
    <div style="font-size:2.5rem;margin-bottom:12px">⚠️</div>
    <h3>Tab Switch Detected!</h3>
    <p id="tabModalMsg">This is your first warning. One more tab switch will automatically submit your test.</p>
    <button onclick="closeTabModal()" style="background:#fbbf24;color:#1e293b;border:none;padding:12px 28px;border-radius:10px;font-weight:700;cursor:pointer;font-size:0.92rem">
      I understand — return to test
    </button>
  </div>
</div>

<div class="confirm-modal" id="confirmModal">
  <div class="confirm-modal-box">
    <div style="font-size:2.5rem;margin-bottom:12px">📋</div>
    <h3>Ready to Submit?</h3>
    <p id="confirmSummary"></p>
    <p style="margin-top:8px !important;font-size:0.78rem;color:#f87171" id="unansweredWarning"></p>
    <div class="confirm-modal-actions">
      <button onclick="closeConfirmModal()" style="background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.7);border:1.5px solid rgba(255,255,255,0.15)">Keep Reviewing</button>
      <button onclick="submitTest()" style="background:linear-gradient(135deg,#059669,#10b981);color:#fff">Submit Now</button>
    </div>
  </div>
</div>

<script>
const TOTAL      = {{ $orderedQuestions->count() }};
const TOTAL_TIME = {{ $assessment->time_per_question * $orderedQuestions->count() }};
const ATTEMPT_ID = {{ $attempt->id }};
const CSRF       = document.querySelector('meta[name="csrf-token"]').content;

let currentIndex    = 0;
let selectedAnswers = {};
let timeLeft        = TOTAL_TIME;
let examInterval    = null;
let tabSwitches     = 0;
let submitting      = false;

const timerEl = document.getElementById('examTimer');

function formatTime(s) {
  const m = Math.floor(s / 60).toString().padStart(2, '0');
  const sec = (s % 60).toString().padStart(2, '0');
  return `${m}:${sec}`;
}

function startExamTimer() {
  timerEl.textContent = formatTime(timeLeft);
  examInterval = setInterval(() => {
    timeLeft--;
    timerEl.textContent = formatTime(timeLeft);
    const pct = timeLeft / TOTAL_TIME;
    timerEl.className = 'asmnt-timer' + (pct <= 0.1 ? ' danger' : pct <= 0.25 ? ' warn' : '');
    if (timeLeft <= 0) { clearInterval(examInterval); submitTest(); }
  }, 1000);
}

function selectOption(el, qIndex, optIndex) {
  document.querySelectorAll(`#q-${qIndex} .q-option`).forEach(o => o.classList.remove('selected'));
  el.classList.add('selected');
  selectedAnswers[qIndex] = optIndex;
  updateGrid();
  updateProgress();
}

function goToQuestion(index) {
  if (index === currentIndex) return;
  document.getElementById(`q-${currentIndex}`).style.display = 'none';
  document.getElementById(`dot-${currentIndex}`).classList.remove('active');
  currentIndex = index;
  document.getElementById(`q-${currentIndex}`).style.display = '';
  document.getElementById(`dot-${currentIndex}`).classList.add('active');
  document.getElementById('prevBtn').disabled = currentIndex === 0;
  document.getElementById('nextBtn').disabled = currentIndex === TOTAL - 1;
  document.getElementById('nextBtn').textContent = currentIndex === TOTAL - 1 ? 'Last Question' : 'Next →';
}

function prevQuestion() { if (currentIndex > 0) goToQuestion(currentIndex - 1); }
function nextQuestion() { if (currentIndex < TOTAL - 1) goToQuestion(currentIndex + 1); }

function updateGrid() {
  document.querySelectorAll('.q-dot').forEach((dot, i) => {
    dot.classList.toggle('answered', i in selectedAnswers);
  });
}

function updateProgress() {
  const answered = Object.keys(selectedAnswers).length;
  document.getElementById('answeredCount').textContent = answered;
  document.getElementById('progressBar').style.width = Math.round((answered / TOTAL) * 100) + '%';
}

function confirmSubmit() {
  const answered   = Object.keys(selectedAnswers).length;
  const unanswered = TOTAL - answered;
  document.getElementById('confirmSummary').textContent = `You have answered ${answered} of ${TOTAL} questions.`;
  document.getElementById('unansweredWarning').textContent = unanswered > 0
    ? `⚠️ ${unanswered} question(s) unanswered — they will be marked incorrect.` : '';
  document.getElementById('confirmModal').classList.add('show');
}

function closeConfirmModal() { document.getElementById('confirmModal').classList.remove('show'); }

function submitTest() {
  if (submitting) return;
  submitting = true;
  clearInterval(examInterval);
  closeConfirmModal();
  const container = document.getElementById('hiddenAnswers');
  container.innerHTML = '';
  document.querySelectorAll('.q-slide').forEach((slide) => {
    const qid = slide.dataset.qid;
    const idx = parseInt(slide.dataset.index);
    const val = selectedAnswers[idx] !== undefined ? selectedAnswers[idx] : -1;
    const input = document.createElement('input');
    input.type = 'hidden'; input.name = `answers[${qid}]`; input.value = val;
    container.appendChild(input);
  });
  document.getElementById('submitForm').submit();
}

document.addEventListener('visibilitychange', () => {
  if (document.hidden && !submitting) {
    tabSwitches++;
    fetch(`/upgrade-assessment/attempt/${ATTEMPT_ID}/tab-switch`, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
      if (data.action === 'submit') submitTest();
      else showTabModal(tabSwitches);
    });
  }
});

function showTabModal(n) {
  document.getElementById('tabModalMsg').textContent = n >= 2
    ? 'This is your final warning. Your test has been submitted.'
    : 'This is your first warning. One more tab switch will automatically submit your test.';
  document.getElementById('tabModal').classList.add('show');
}
function closeTabModal() { document.getElementById('tabModal').classList.remove('show'); }

document.addEventListener('contextmenu', e => e.preventDefault());

startExamTimer();
updateProgress();
</script>
</body>
</html>
