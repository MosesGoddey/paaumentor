<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $groupSession->title }} — PAAUMENTOR</title>
<link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
<style>
*, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

body {
  background: #0a0f1e;
  font-family: 'Sora', -apple-system, BlinkMacSystemFont, sans-serif;
  height: 100vh;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  color: #fff;
}

/* ============ TOP BAR ============ */
#call-header {
  height: 62px;
  background: linear-gradient(90deg, #0f172a 0%, #1a2540 100%);
  border-bottom: 1px solid rgba(255,255,255,0.07);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
  flex-shrink: 0;
  z-index: 200;
  gap: 12px;
}

.hd-brand { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
.hd-logo {
  width: 36px; height: 36px;
  background: linear-gradient(135deg, #7c3aed, #1d4ed8);
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  font-weight: 800; font-size: 0.82rem; color: #fff;
  letter-spacing: -0.5px;
}
.hd-name { font-weight: 700; font-size: 0.95rem; color: #fff; white-space: nowrap; }
.hd-name span { color: #fbbf24; }
.hd-sep { width: 1px; height: 22px; background: rgba(255,255,255,0.12); margin: 0 4px; }
.hd-session { font-size: 0.8rem; color: rgba(255,255,255,0.55); max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

.hd-centre { display: flex; align-items: center; gap: 10px; }
.live-pill {
  display: flex; align-items: center; gap: 5px;
  background: rgba(239,68,68,0.14);
  border: 1px solid rgba(239,68,68,0.28);
  border-radius: 999px; padding: 4px 10px;
  font-size: 0.68rem; font-weight: 700; color: #f87171;
  letter-spacing: 0.08em; text-transform: uppercase;
}
.live-dot {
  width: 6px; height: 6px; border-radius: 50%; background: #ef4444;
  animation: lBlink 1.3s ease-in-out infinite;
}
@keyframes lBlink { 0%,100%{opacity:1} 50%{opacity:0.25} }
#hd-timer { font-size: 1rem; font-weight: 700; letter-spacing: 0.06em; color: #fff; min-width: 60px; text-align: center; }

/* Participant count pill */
.hd-count-pill {
  display: flex; align-items: center; gap: 6px;
  background: rgba(124,58,237,0.18);
  border: 1px solid rgba(124,58,237,0.3);
  border-radius: 999px; padding: 4px 12px;
  font-size: 0.75rem; font-weight: 700; color: #a78bfa;
}

.hd-right { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }

/* Host badge */
.host-chip {
  background: rgba(251,191,36,0.15);
  border: 1px solid rgba(251,191,36,0.25);
  border-radius: 999px; padding: 4px 10px;
  font-size: 0.68rem; font-weight: 700; color: #fbbf24;
  text-transform: uppercase; letter-spacing: 0.06em;
}

#btn-end {
  display: flex; align-items: center; gap: 7px;
  background: #ef4444;
  border: none; color: #fff; border-radius: 10px;
  padding: 9px 18px; font-weight: 700; font-size: 0.83rem;
  font-family: inherit; cursor: pointer;
  transition: background 0.15s, transform 0.15s;
  white-space: nowrap;
}
#btn-end:hover { background: #dc2626; transform: scale(1.03); }

/* ============ CALL AREA ============ */
#call-area { flex: 1; position: relative; overflow: hidden; }
#jitsi-container { width: 100%; height: 100%; }

/* ============ PAAU WATERMARK ============ */
#wm {
  position: absolute; top: 14px; left: 14px;
  z-index: 100; pointer-events: none; user-select: none;
  display: flex; align-items: center; gap: 9px; opacity: 0.32;
}
.wm-box {
  width: 38px; height: 38px;
  background: rgba(255,255,255,0.9); border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  font-weight: 900; font-size: 0.72rem; color: #7c3aed;
  letter-spacing: -0.5px; flex-shrink: 0;
}
.wm-text-block { line-height: 1.25; }
.wm-title { font-weight: 800; font-size: 0.82rem; color: #fff; }
.wm-sub   { font-size: 0.63rem; color: rgba(255,255,255,0.75); }

/* ============ SESSION INFO BADGE (bottom-left) ============ */
#session-badge {
  position: absolute; bottom: 16px; left: 16px;
  z-index: 100; pointer-events: none;
  background: rgba(10,15,30,0.72); backdrop-filter: blur(10px);
  border: 1px solid rgba(255,255,255,0.1); border-radius: 12px;
  padding: 8px 14px; font-size: 0.75rem; color: rgba(255,255,255,0.7);
  display: flex; align-items: center; gap: 8px;
}
.sb-type-pill {
  background: #7c3aed; color: #fff;
  font-size: 0.62rem; font-weight: 700; padding: 2px 8px;
  border-radius: 999px; text-transform: uppercase; letter-spacing: 0.06em;
}
.sb-title { color: #fff; font-weight: 600; }

/* ============ END CALL MODAL ============ */
#modal-overlay {
  display: none; position: fixed; inset: 0; z-index: 9999;
  background: rgba(0,0,0,0.75); backdrop-filter: blur(4px);
  align-items: center; justify-content: center;
}
#modal-overlay.open { display: flex; }
.modal-box {
  background: #1e293b; border: 1px solid #334155;
  border-radius: 22px; padding: 36px 40px;
  text-align: center; max-width: 380px; width: 90%;
  animation: modalPop 0.22s cubic-bezier(.22,.68,0,1.2);
}
@keyframes modalPop { from{transform:scale(0.9);opacity:0} to{transform:scale(1);opacity:1} }
.modal-title { font-size: 1.2rem; font-weight: 800; color: #fff; margin-bottom: 8px; }
.modal-sub   { font-size: 0.84rem; color: #94a3b8; line-height: 1.6; margin-bottom: 28px; }
.modal-actions { display: flex; gap: 10px; justify-content: center; }
.btn-stay {
  background: #334155; color: #fff; border: none; border-radius: 11px;
  padding: 11px 28px; font-weight: 600; font-size: 0.88rem;
  font-family: inherit; cursor: pointer; transition: background 0.15s;
}
.btn-stay:hover { background: #475569; }
.btn-end-confirm {
  background: #ef4444; color: #fff; border: none; border-radius: 11px;
  padding: 11px 28px; font-weight: 700; font-size: 0.88rem;
  font-family: inherit; cursor: pointer; transition: background 0.15s;
}
.btn-end-confirm:hover { background: #dc2626; }

@media (max-width: 640px) {
  .hd-session, .hd-sep { display: none; }
  #call-header { padding: 0 12px; }
}
</style>
</head>
<body>

{{-- ======== CUSTOM TOP BAR ======== --}}
<header id="call-header">

  {{-- Left: branding + session title --}}
  <div class="hd-brand">
    <div class="hd-logo">PM</div>
    <div class="hd-name">PAAU<span>MENTOR</span></div>
    <div class="hd-sep"></div>
    <div class="hd-session">{{ $groupSession->title }}</div>
  </div>

  {{-- Centre: LIVE badge + timer + participant count --}}
  <div class="hd-centre">
    <div class="live-pill">
      <div class="live-dot"></div>
      Live
    </div>
    <div id="hd-timer">00:00</div>
    <div class="hd-count-pill">
      <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
      </svg>
      <span id="participant-count">{{ $groupSession->participantCount() }}</span>
    </div>
  </div>

  {{-- Right: host indicator + end button --}}
  <div class="hd-right">
    @if($isHost)
    <div class="host-chip">Host</div>
    @endif
    <button id="btn-end" onclick="openEndModal()">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"
           stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
        <path d="M10.68 13.31a16 16 0 0 0 3.41 2.6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7 2 2 0 0 1 1.72 2v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07C9.44 17.25 7.76 15.57 6.46 13.78A19.79 19.79 0 0 1 3.39 5.15 2 2 0 0 1 5.36 3h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L9.34 10.9"/>
        <line x1="23" y1="1" x2="1" y2="23"/>
      </svg>
      {{ $isHost ? 'End for All' : 'Leave Call' }}
    </button>
  </div>

</header>

{{-- ======== CALL AREA ======== --}}
<div id="call-area">
  <div id="jitsi-container"></div>

  {{-- PAAU watermark --}}
  <div id="wm">
    <div class="wm-box">PM</div>
    <div class="wm-text-block">
      <div class="wm-title">PAAUMENTOR</div>
      <div class="wm-sub">Group Session</div>
    </div>
  </div>

  {{-- Session info badge --}}
  <div id="session-badge">
    <span class="sb-type-pill">GROUP · {{ strtoupper($groupSession->type) }}</span>
    <span class="sb-title">{{ $groupSession->title }}</span>
  </div>
</div>

{{-- ======== END CALL MODAL ======== --}}
<div id="modal-overlay">
  <div class="modal-box">
    <div style="margin-bottom:14px;color:#f87171"><x-icon :name="$isHost ? 'phone-off' : 'log-out'" :size="38" :stroke="1.6" /></div>
    <div class="modal-title">{{ $isHost ? 'End session for everyone?' : 'Leave this session?' }}</div>
    <div class="modal-sub">
      @if($isHost)
        This will end the call for <strong style="color:#fff">all {{ $groupSession->participantCount() }} participants</strong>.<br>
        The session will be marked as completed.
      @else
        You will leave the call. Other participants will remain.<br>
        You can rejoin from the Group Sessions page.
      @endif
    </div>
    <div class="modal-actions">
      <button class="btn-stay" onclick="closeEndModal()">Stay in Call</button>
      <button class="btn-end-confirm" onclick="confirmEndCall()">{{ $isHost ? 'End for All' : 'Leave Call' }}</button>
    </div>
  </div>
</div>

<script src="https://meet.jit.si/external_api.js"></script>
<script>
const ROOM          = '{{ $groupSession->room }}';
const DISPLAY_NAME  = @json($user->full_name);
const USER_EMAIL    = @json($user->email);
const IS_HOST       = {{ $isHost ? 'true' : 'false' }};
const COMPLETE_URL  = '{{ route("group-sessions.complete", $groupSession) }}';
const INDEX_URL     = '{{ route("group-sessions.index") }}';
const CSRF          = '{{ csrf_token() }}';

const api = new JitsiMeetExternalAPI('meet.jit.si', {
  roomName:   ROOM,
  width:      '100%',
  height:     '100%',
  parentNode: document.getElementById('jitsi-container'),

  configOverwrite: {
    disableWatermark:        true,
    prejoinPageEnabled:      false,
    startWithVideoMuted:     {{ $groupSession->type === 'voice' ? 'true' : 'false' }},
    startWithAudioMuted:     false,
    enableWelcomePage:       false,
    disableDeepLinking:      true,
    disableInviteFunctions:  true,
    enableClosePage:         false,
    toolbarButtons: [
      'microphone', 'camera', 'desktop', 'chat',
      'raisehand', 'tileview', 'fullscreen', 'settings', 'participants-pane',
    ],
  },

  interfaceConfigOverwrite: {
    SHOW_JITSI_WATERMARK:         false,
    SHOW_WATERMARK_FOR_GUESTS:    false,
    SHOW_BRAND_WATERMARK:         false,
    SHOW_POWERED_BY:              false,
    APP_NAME:                     'PAAUMENTOR',
    NATIVE_APP_NAME:              'PAAUMENTOR',
    PROVIDER_NAME:                'PAAUMENTOR',
    DEFAULT_REMOTE_DISPLAY_NAME:  'Participant',
    TOOLBAR_ALWAYS_VISIBLE:       false,
  },

  userInfo: { displayName: DISPLAY_NAME, email: USER_EMAIL },
});

/* Update participant count from Jitsi events */
api.addEventListener('participantJoined',  updateCount);
api.addEventListener('participantLeft',    updateCount);
function updateCount() {
  const count = api.getNumberOfParticipants();
  document.getElementById('participant-count').textContent = count;
}

api.addEventListener('readyToClose', function () { confirmEndCall(); });

/* Timer */
let elapsed = 0;
const timerEl = document.getElementById('hd-timer');
const timerInterval = setInterval(function () {
  elapsed++;
  const h = Math.floor(elapsed / 3600);
  const m = Math.floor((elapsed % 3600) / 60);
  const s = elapsed % 60;
  timerEl.textContent = (h > 0 ? String(h).padStart(2, '0') + ':' : '')
    + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
}, 1000);

function openEndModal()  { document.getElementById('modal-overlay').classList.add('open'); }
function closeEndModal() { document.getElementById('modal-overlay').classList.remove('open'); }

async function confirmEndCall() {
  clearInterval(timerInterval);
  try { api.executeCommand('hangup'); } catch (e) {}

  if (IS_HOST) {
    try {
      await fetch(COMPLETE_URL, {
        method:  'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
      });
    } catch (e) {}
  }

  window.location.href = INDEX_URL;
}

window.addEventListener('beforeunload', function (e) {
  e.preventDefault();
  e.returnValue = '';
});
</script>
</body>
</html>
