{{-- ============================================================
     Mentor AI Floating Widget
     Powered by Google Gemini · Available to guests and users
     ============================================================ --}}
<style>
/* wrapper — position: fixed, isolated from page layout */
#mai-wrap {
  position: fixed;
  bottom: 28px;
  right: 28px;
  z-index: 9998;
  display: flex;
  align-items: center;
  justify-content: center;
  /* prevent any child overflow from leaking into the page scroll */
  overflow: visible;
  pointer-events: none;
}
#mai-wrap.mai-open { display: none; }

#mai-btn {
  pointer-events: all;
  position: relative;
  display: flex;
  align-items: center;
  gap: 8px;
  background: #1e3a8a;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 11px 18px 11px 15px;
  font-size: 0.84rem;
  font-weight: 600;
  font-family: 'Inter', sans-serif;
  cursor: pointer;
  letter-spacing: 0.01em;
  box-shadow: 0 4px 16px rgba(15,27,51,0.28);
  transition: background 0.15s, box-shadow 0.15s;
}
#mai-btn:hover {
  background: #172554;
  box-shadow: 0 6px 20px rgba(15,27,51,0.35);
}

#mai-panel {
  position: fixed;
  bottom: 28px;
  right: 28px;
  z-index: 9999;
  width: 370px;
  height: 560px;
  border-radius: 12px;
  box-shadow: 0 24px 64px rgba(0,0,0,0.18), 0 4px 16px rgba(0,0,0,0.08);
  display: none;
  flex-direction: column;
  overflow: hidden;
  background: #fff;
  border: 1px solid #e5e7eb;
  animation: maiSlideUp 0.28s cubic-bezier(.22,.68,0,1.2);
}
[data-theme="dark"] #mai-panel { background: #1e293b; border-color: #334155; }
#mai-panel.mai-visible { display: flex; }
@keyframes maiSlideUp {
  from { opacity: 0; transform: translateY(20px) scale(0.97); }
  to   { opacity: 1; transform: translateY(0)   scale(1); }
}

/* ---- Header ---- */
#mai-header {
  background: #1e3a8a;
  padding: 14px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-shrink: 0;
}
.mai-avatar {
  width: 38px; height: 38px;
  border-radius: 11px;
  background: rgba(255,255,255,0.18);
  border: 1.5px solid rgba(255,255,255,0.3);
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.mai-header-name { color: #fff; font-weight: 700; font-size: 0.92rem; line-height: 1.2; }
.mai-header-sub  { color: rgba(255,255,255,0.72); font-size: 0.72rem; margin-top: 1px; }
#mai-close-btn {
  background: rgba(255,255,255,0.15);
  border: none; color: #fff;
  border-radius: 8px;
  width: 30px; height: 30px;
  cursor: pointer; font-size: 1.05rem; line-height: 1;
  display: flex; align-items: center; justify-content: center;
  transition: background 0.15s; flex-shrink: 0;
}
#mai-close-btn:hover { background: rgba(255,255,255,0.28); }

/* ---- Messages area ---- */
#mai-messages {
  flex: 1;
  overflow-y: auto;
  padding: 14px 14px 6px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  scroll-behavior: smooth;
}
#mai-messages::-webkit-scrollbar { width: 4px; }
#mai-messages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

.mai-row { display: flex; align-items: flex-end; gap: 7px; }
.mai-row.user { flex-direction: row-reverse; }

.mai-msg-icon {
  width: 26px; height: 26px; border-radius: 6px;
  background: #1e3a8a;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0; margin-bottom: 2px;
}

.mai-bubble {
  max-width: 80%;
  background: #f1f5f9;
  border-radius: 16px 16px 16px 4px;
  padding: 9px 13px;
  font-size: 0.82rem;
  line-height: 1.58;
  color: #1e293b;
  word-break: break-word;
}
[data-theme="dark"] .mai-bubble { background: #334155; color: #e2e8f0; }
.mai-bubble p { margin: 0 0 5px; }
.mai-bubble p:last-child { margin-bottom: 0; }
.mai-bubble ul, .mai-bubble ol { margin: 4px 0 4px 16px; padding: 0; }
.mai-bubble li { margin-bottom: 2px; }
.mai-bubble strong { color: #1e3a8a; }
.mai-bubble code { background: #e2e8f0; border-radius: 4px; padding: 1px 5px; font-size: 0.78rem; }
[data-theme="dark"] .mai-bubble strong { color: #93c5fd; }
[data-theme="dark"] .mai-bubble code { background: #475569; }

.mai-row.user .mai-bubble {
  background: #1e3a8a;
  color: #fff;
  border-radius: 16px 16px 4px 16px;
}
.mai-row.user .mai-bubble strong { color: #bfdbfe; }

/* ---- Typing dots ---- */
.mai-typing-bubble {
  display: flex; gap: 5px; align-items: center;
  background: #f1f5f9; border-radius: 16px 16px 16px 4px;
  padding: 10px 14px;
}
[data-theme="dark"] .mai-typing-bubble { background: #334155; }
.mai-typing-bubble span {
  width: 7px; height: 7px; border-radius: 50%;
  background: #94a3b8;
  animation: maiDot 1.3s infinite ease-in-out;
}
.mai-typing-bubble span:nth-child(2) { animation-delay: 0.18s; }
.mai-typing-bubble span:nth-child(3) { animation-delay: 0.36s; }
@keyframes maiDot {
  0%,60%,100% { transform: translateY(0); opacity: 0.5; }
  30%          { transform: translateY(-5px); opacity: 1; }
}

/* ---- Smart reply chips ---- */
#mai-chips {
  padding: 6px 14px 4px;
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  flex-shrink: 0;
}
.mai-chip {
  background: #f1f5f9;
  border: 1px solid #cbd5e1;
  color: #1e3a8a;
  font-size: 0.75rem;
  font-weight: 600;
  padding: 5px 12px;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.15s, border-color 0.15s;
  white-space: nowrap;
  max-width: 180px;
  overflow: hidden;
  text-overflow: ellipsis;
}
.mai-chip:hover { background: #e9edf5; border-color: #8fa3c8; }
[data-theme="dark"] .mai-chip { background: #263450; border-color: #475569; color: #cbd5e1; }

/* ---- Input area ---- */
#mai-input-bar {
  padding: 10px 12px;
  border-top: 1px solid #e5e7eb;
  display: flex;
  gap: 8px;
  align-items: flex-end;
  flex-shrink: 0;
  background: #fff;
}
[data-theme="dark"] #mai-input-bar { background: #1e293b; border-color: #334155; }
#mai-input {
  flex: 1;
  border: 1.5px solid #e2e8f0;
  border-radius: 12px;
  padding: 9px 13px;
  font-size: 0.83rem;
  font-family: inherit;
  resize: none;
  outline: none;
  max-height: 80px;
  min-height: 38px;
  line-height: 1.45;
  color: #0f172a;
  background: #f8fafc;
  transition: border-color 0.15s;
  overflow-y: auto;
}
[data-theme="dark"] #mai-input { background: #334155; border-color: #475569; color: #e2e8f0; }
#mai-input:focus { border-color: #1e3a8a; }
#mai-send-btn {
  width: 38px; height: 38px; border-radius: 8px;
  background: #1e3a8a;
  border: none; color: #fff; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  transition: background 0.15s;
  flex-shrink: 0;
}
#mai-send-btn:hover { background: #172554; }
#mai-send-btn:disabled { background: #e2e8f0; cursor: not-allowed; transform: none; }

@media (max-width: 480px) {
  #mai-panel { width: calc(100vw - 24px); right: 12px; bottom: 80px; height: 500px; }
  #mai-btn   { right: 12px; bottom: 16px; }
}
</style>

{{-- Floating trigger button --}}
<div id="mai-wrap">
  <button id="mai-btn" onclick="maiToggle()" aria-label="Open PAAUMENTOR AI">
    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
      <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
      <circle cx="8.5" cy="10.5" r="0.7" fill="currentColor" stroke="none"/>
      <circle cx="12"  cy="10.5" r="0.7" fill="currentColor" stroke="none"/>
      <circle cx="15.5" cy="10.5" r="0.7" fill="currentColor" stroke="none"/>
    </svg>
    PaauMentor AI
  </button>
</div>

{{-- Chat panel --}}
<div id="mai-panel" role="dialog" aria-label="Mentor AI chat">

  {{-- Header --}}
  <div id="mai-header">
    <div style="display:flex;align-items:center;gap:10px">
      <div class="mai-avatar">
        <svg width="20" height="20" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
          <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
        </svg>
      </div>
      <div>
        <div class="mai-header-name">Mentor AI</div>
        <div class="mai-header-sub">Mentorship &amp; learning assistant</div>
      </div>
    </div>
    <button id="mai-close-btn" onclick="maiToggle()" aria-label="Close"><x-icon name="x" :size="16" /></button>
  </div>

  {{-- Messages --}}
  <div id="mai-messages"></div>

  {{-- Smart reply chips --}}
  <div id="mai-chips"></div>

  {{-- Input --}}
  <div id="mai-input-bar">
    <textarea id="mai-input" placeholder="Ask about mentorship, learning paths…" rows="1"></textarea>
    <button id="mai-send-btn" onclick="maiSend()" aria-label="Send">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
        <line x1="22" y1="2" x2="11" y2="13"/>
        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
      </svg>
    </button>
  </div>
</div>

{{-- Load marked.js only if not already present --}}
@once
<script>
/* Load marked.js asynchronously so it never blocks page rendering.
   The chat code already falls back to plain text if marked isn't ready yet. */
if (typeof window.marked === 'undefined') {
  var _maiMarked = document.createElement('script');
  _maiMarked.src   = '{{ asset('vendor/marked/marked.min.js') }}';
  _maiMarked.async = true;
  document.head.appendChild(_maiMarked);
}
</script>
@endonce

<script>
(function () {
  var _maiOpen    = false;
  var _maiStarted = false;
  var CHAT_URL    = '{{ route("mentor.ai.chat") }}';
  var CSRF        = '{{ csrf_token() }}';

  var INIT_CHIPS = [
    'How do I find a mentor?',
    'How does certification work?',
    'What is a learning path?',
    'How do I schedule a session?',
  ];

  /* ---- toggle open/close ---- */
  window.maiToggle = function () {
    _maiOpen = !_maiOpen;
    var panel = document.getElementById('mai-panel');
    var wrap  = document.getElementById('mai-wrap');
    if (_maiOpen) {
      panel.classList.add('mai-visible');
      wrap.classList.add('mai-open');
      if (!_maiStarted) { _maiStarted = true; maiWelcome(); }
      setTimeout(function(){ document.getElementById('mai-input').focus(); }, 300);
    } else {
      panel.classList.remove('mai-visible');
      wrap.classList.remove('mai-open');
    }
  };

  /* ---- welcome message ---- */
  function maiWelcome() {
    maiAppendAi(
      "Hi there! I'm **Mentor AI** — your PAAUMENTOR assistant.\n\nI can help you with:\n- Finding and requesting a mentor\n- Understanding learning paths and tasks\n- The certificate and assessment process\n- Scheduling sessions\n- Academic questions\n\nWhat would you like to know?",
      INIT_CHIPS
    );
  }

  /* ---- append user bubble ---- */
  function maiAppendUser(text) {
    var msgs = document.getElementById('mai-messages');
    var row  = document.createElement('div');
    row.className = 'mai-row user';
    row.innerHTML = '<div class="mai-bubble">' + maiEsc(text) + '</div>';
    msgs.appendChild(row);
    msgs.scrollTop = msgs.scrollHeight;
  }

  /* ---- append AI bubble ---- */
  function maiAppendAi(text, chips) {
    var msgs = document.getElementById('mai-messages');
    var row  = document.createElement('div');
    row.className = 'mai-row ai';
    var parsed = (typeof marked !== 'undefined') ? marked.parse(text) : maiEsc(text).replace(/\n/g,'<br>');
    row.innerHTML =
      '<div class="mai-msg-icon"><svg width="14" height="14" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg></div>' +
      '<div class="mai-bubble">' + parsed + '</div>';
    msgs.appendChild(row);
    msgs.scrollTop = msgs.scrollHeight;
    if (chips && chips.length) maiSetChips(chips);
  }

  /* ---- smart reply chips ---- */
  function maiSetChips(chips) {
    var container = document.getElementById('mai-chips');
    container.innerHTML = '';
    chips.slice(0, 4).forEach(function (c) {
      var btn = document.createElement('button');
      btn.className   = 'mai-chip';
      btn.textContent = c;
      btn.title       = c;
      btn.onclick = function () {
        document.getElementById('mai-input').value = c;
        maiSend();
      };
      container.appendChild(btn);
    });
  }

  /* ---- typing indicator ---- */
  function maiShowTyping() {
    var msgs = document.getElementById('mai-messages');
    var row  = document.createElement('div');
    row.className = 'mai-row ai';
    row.id = 'mai-typing-row';
    row.innerHTML =
      '<div class="mai-msg-icon"><svg width="14" height="14" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg></div>' +
      '<div class="mai-typing-bubble"><span></span><span></span><span></span></div>';
    msgs.appendChild(row);
    msgs.scrollTop = msgs.scrollHeight;
  }
  function maiHideTyping() {
    var el = document.getElementById('mai-typing-row');
    if (el) el.remove();
  }

  /* ---- send ---- */
  window.maiSend = async function () {
    var inp  = document.getElementById('mai-input');
    var send = document.getElementById('mai-send-btn');
    var msg  = inp.value.trim();
    if (!msg) return;

    inp.value = '';
    inp.style.height = 'auto';
    document.getElementById('mai-chips').innerHTML = '';
    maiAppendUser(msg);
    maiShowTyping();
    send.disabled = true;

    try {
      var res  = await fetch(CHAT_URL, {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body:    JSON.stringify({ message: msg }),
      });
      var data = await res.json();
      maiHideTyping();
      if (data.error) {
        maiAppendAi('Sorry, I ran into an issue: ' + data.error + ' Please try again.', INIT_CHIPS);
      } else {
        maiAppendAi(data.reply, data.suggestions && data.suggestions.length ? data.suggestions : INIT_CHIPS);
      }
    } catch (err) {
      maiHideTyping();
      maiAppendAi('Connection error. Please check your internet and try again.', INIT_CHIPS);
    } finally {
      send.disabled = false;
      inp.focus();
    }
  };

  /* ---- HTML escape ---- */
  function maiEsc(t) {
    return String(t).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }

  /* ---- textarea auto-resize + Enter to send ---- */
  document.addEventListener('DOMContentLoaded', function () {
    var inp = document.getElementById('mai-input');
    if (!inp) return;
    inp.addEventListener('input', function () {
      this.style.height = 'auto';
      this.style.height = Math.min(this.scrollHeight, 80) + 'px';
    });
    inp.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); maiSend(); }
    });
  });
})();
</script>
