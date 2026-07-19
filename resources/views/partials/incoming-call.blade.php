{{-- ============================================================
     Global incoming-call banner
     Included in the authenticated layout so calls ring on EVERY
     page, not just the Messages screen.
     ============================================================ --}}
<script>
(function () {
  if (window._incomingCallPollerActive) return;
  window._incomingCallPollerActive = true;

  const CSRF = '{{ csrf_token() }}';
  // Identify polls as AJAX so an expired session gets a silent 401 instead of
  // a login redirect that pollutes Laravel's intended-URL (which would land
  // the user on this JSON endpoint after their next login).
  const AJAX = {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'};
  let _callBannerNotifId = null;

  function showCallBanner(notif) {
    if (document.getElementById('callBanner')) return;
    _callBannerNotifId = notif.id;
    const data = notif.data || {};
    const typeLabels = {video: 'Video Call', voice: 'Voice Call', screen: 'Screen Share'};
    const typeLabel = typeLabels[data.call_type] || 'Incoming Call';
    const banner = document.createElement('div');
    banner.id = 'callBanner';
    banner.dataset.sessionId = data.session_id || '';
    banner.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:20000;background:#1e293b;color:#fff;border-radius:16px;padding:18px 22px;box-shadow:0 8px 32px rgba(0,0,0,0.4);display:flex;flex-direction:column;gap:10px;min-width:280px;max-width:320px;animation:callSlideUp 0.3s ease';
    banner.innerHTML = `
      <style>@keyframes callSlideUp{from{transform:translateY(20px);opacity:0}to{transform:translateY(0);opacity:1}}</style>
      <div style="display:flex;align-items:center;gap:10px">
        <div style="background:rgba(255,255,255,0.12);border-radius:8px;padding:6px 10px;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;flex-shrink:0">${typeLabel}</div>
        <div>
          <div style="font-weight:700;font-size:0.95rem">${notif.title}</div>
          <div style="font-size:0.78rem;opacity:0.7;margin-top:2px">${notif.body}</div>
        </div>
      </div>
      <div style="display:flex;gap:8px">
        <button onclick="joinIncomingCall('${data.room}','${data.call_type}','${data.session_id || ''}')"
                style="flex:1;background:#2563eb;color:#fff;border:none;padding:8px 0;border-radius:8px;cursor:pointer;font-weight:700;font-size:0.88rem">Join</button>
        <button onclick="dismissCallBanner()"
                style="flex:1;background:rgba(255,255,255,0.12);color:#fff;border:none;padding:8px 0;border-radius:8px;cursor:pointer;font-size:0.88rem">Dismiss</button>
      </div>`;
    document.body.appendChild(banner);
    // Caller gives up at 60s — ring slightly longer, never more.
    setTimeout(() => dismissCallBanner(), 65000);
  }

  window.joinIncomingCall = function (room, type, sessionId) {
    if (sessionId) {
      fetch('/sessions/' + sessionId + '/answered', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': CSRF},
      });
    }
    let url = `https://meet.jit.si/${room}#config.prejoinPageEnabled=false&config.startWithVideoMuted=${type !== 'video'}&config.startWithAudioMuted=false&userInfo.displayName="{{ urlencode(auth()->user()->full_name) }}"`;
    if (type === 'screen') url += '&config.startScreenSharing=true';
    window.open(url, '_blank');
    _dismissBanner(false);
  };

  window.dismissCallBanner = function () {
    _dismissBanner(true);
  };

  function _dismissBanner(markMissed) {
    const banner = document.getElementById('callBanner');
    const sid = banner ? banner.dataset.sessionId : null;
    if (banner) banner.remove();
    if (markMissed && sid) {
      fetch('/sessions/' + sid + '/missed', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': CSRF},
      });
    }
    if (_callBannerNotifId) {
      fetch('/notifications/' + _callBannerNotifId + '/read', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': CSRF},
      });
      _callBannerNotifId = null;
    }
  }

  async function pollForIncomingCall() {
    try {
      const res = await fetch('{{ route("notifications.pendingCall") }}', {headers: AJAX});
      if (!res.ok) return;
      const data = await res.json();
      if (data.call && !document.getElementById('callBanner')) {
        showCallBanner(data.call);
      }
      const banner = document.getElementById('callBanner');
      if (banner && banner.dataset.sessionId) {
        const sr = await fetch('/sessions/' + banner.dataset.sessionId + '/status', {headers: AJAX});
        const sd = await sr.json();
        if (sd.status === 'cancelled') {
          _dismissBanner(false);
        }
      }
    } catch (_) {}
  }

  setInterval(pollForIncomingCall, 5000);
  pollForIncomingCall();
})();
</script>
