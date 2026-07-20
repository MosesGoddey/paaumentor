@extends('layouts.sidebar')
@section('title', 'AI Study Buddy')

@section('page-content')
<style>
.sb-wrap{display:flex;flex-direction:column;height:calc(100vh - 130px);max-width:800px}
.sb-messages{flex:1;overflow-y:auto;display:flex;flex-direction:column;gap:14px;padding:4px 0 12px}
.msg-user{display:flex;justify-content:flex-end}
.msg-ai{display:flex;justify-content:flex-start;gap:10px;align-items:flex-start}
.msg-bubble{max-width:78%;padding:11px 15px;font-size:0.875rem;line-height:1.6;word-break:break-word}
.msg-bubble-user{background:var(--blue-500);color:#fff;border-radius:18px 18px 4px 18px}
.msg-bubble-ai{background:var(--surface-2);color:var(--text);border-radius:18px 18px 18px 4px}
.msg-bubble-ai p{margin:0 0 8px}.msg-bubble-ai p:last-child{margin-bottom:0}
.msg-bubble-ai pre{background:rgba(0,0,0,0.1);border-radius:6px;padding:10px;overflow-x:auto;font-size:0.8rem;margin:6px 0}
.msg-bubble-ai code:not(pre code){background:rgba(0,0,0,0.1);border-radius:3px;padding:1px 4px;font-size:0.82rem}
.msg-bubble-ai ul,.msg-bubble-ai ol{padding-left:18px;margin:4px 0 8px}
.msg-bubble-ai h1,.msg-bubble-ai h2,.msg-bubble-ai h3{font-size:0.92rem;font-weight:700;margin:8px 0 4px}
.sb-avatar{width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#2563eb,#7c3aed);display:flex;align-items:center;justify-content:center;color:#fff;font-size:0.7rem;font-weight:800;flex-shrink:0;margin-top:2px}
@keyframes bounce{0%,60%,100%{transform:translateY(0)}30%{transform:translateY(-5px)}}
.dot{width:6px;height:6px;border-radius:50%;background:var(--text-3);display:inline-block}
</style>

<div class="sb-wrap">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;flex-shrink:0">
    <div>
      <h1 class="section-title" style="margin-bottom:3px">AI Study Buddy</h1>
      <p class="section-sub" style="margin:0">Ask anything about your academics at PAAU</p>
    </div>
    <button onclick="clearChat()" class="btn btn-outline btn-sm" style="font-size:0.78rem;flex-shrink:0">Clear Chat</button>
  </div>

  <div class="sb-messages" id="msgArea">
    @if(empty($history))
    <div class="msg-ai">
      <div class="sb-avatar">AI</div>
      <div class="msg-bubble msg-bubble-ai">
        <strong>Hello, {{ $user->first_name }}!</strong> I'm your AI Study Buddy.<br>
        I can help you understand academic concepts, answer subject questions, explain code, and guide your learning.<br><br>
        What would you like to study today?
      </div>
    </div>
    @else
    @foreach($history as $msg)
    @if($msg['role'] === 'user')
    <div class="msg-user">
      <div class="msg-bubble msg-bubble-user">{{ $msg['content'] }}</div>
    </div>
    @else
    <div class="msg-ai">
      <div class="sb-avatar">AI</div>
      <div class="msg-bubble msg-bubble-ai md-content">{{ $msg['content'] }}</div>
    </div>
    @endif
    @endforeach
    @endif

    <div id="typing" style="display:none" class="msg-ai">
      <div class="sb-avatar">AI</div>
      <div class="msg-bubble msg-bubble-ai" style="padding:13px 16px">
        <span style="display:flex;gap:5px;align-items:center">
          <span class="dot" style="animation:bounce 1.4s infinite 0s"></span>
          <span class="dot" style="animation:bounce 1.4s infinite 0.2s"></span>
          <span class="dot" style="animation:bounce 1.4s infinite 0.4s"></span>
        </span>
      </div>
    </div>
  </div>

  <form id="chatForm" style="flex-shrink:0;border-top:1px solid var(--border);padding-top:14px;display:flex;gap:10px;align-items:flex-end">
    <textarea id="msgInput" class="form-input" placeholder="Ask me anything about your studies..." rows="2"
              style="flex:1;resize:none;min-height:44px;max-height:120px" onkeydown="handleKey(event)"></textarea>
    <button type="submit" id="sendBtn" class="btn btn-primary" style="height:44px;padding:0 22px;flex-shrink:0">Send</button>
  </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/marked/marked.min.js') }}"></script>
<script>
const area   = document.getElementById('msgArea');
const input  = document.getElementById('msgInput');
const sendBtn= document.getElementById('sendBtn');
const typing = document.getElementById('typing');

marked.setOptions({breaks: true});

document.querySelectorAll('.md-content').forEach(el => {
  el.innerHTML = marked.parse(el.textContent || '');
});

area.scrollTop = area.scrollHeight;

function handleKey(e) {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault();
    document.getElementById('chatForm').dispatchEvent(new Event('submit'));
  }
}

document.getElementById('chatForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const msg = input.value.trim();
  if (!msg) return;

  appendMsg('user', msg);
  input.value = '';
  sendBtn.disabled = true;
  typing.style.display = 'flex';
  area.scrollTop = area.scrollHeight;

  try {
    const res = await fetch('{{ route("ai.chat") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({message: msg}),
    });
    const data = await res.json();
    typing.style.display = 'none';
    appendMsg('ai', data.error ? 'Sorry, I am temporarily unavailable. Please try again later.' : data.reply);
  } catch (err) {
    typing.style.display = 'none';
    appendMsg('ai', 'Connection error. Please check your internet and try again.');
  }

  sendBtn.disabled = false;
  area.scrollTop = area.scrollHeight;
});

function appendMsg(role, content) {
  const div = document.createElement('div');
  if (role === 'user') {
    div.className = 'msg-user';
    div.innerHTML = `<div class="msg-bubble msg-bubble-user">${escHtml(content)}</div>`;
  } else {
    div.className = 'msg-ai';
    div.innerHTML = `<div class="sb-avatar">AI</div><div class="msg-bubble msg-bubble-ai">${marked.parse(content)}</div>`;
  }
  area.insertBefore(div, typing);
}

function escHtml(s) {
  return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

async function clearChat() {
  if (!confirm('Clear the entire conversation?')) return;
  await fetch('{{ route("ai.clear") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    },
    body: '{}',
  });
  location.reload();
}
</script>
@endpush
