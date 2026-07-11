@extends('layouts.sidebar')
@section('title', 'Messages')
@section('breadcrumbs')<span style="opacity:0.5">›</span> <a href="{{ route('chat.index') }}" style="color:var(--blue-500);text-decoration:none">Messages</a>@endsection

@section('page-content')
@php
  $isMentor      = auth()->user()->role === 'mentor';
  $roleColor     = $isMentor ? '#16a34a' : '#2563eb';
  $roleDark      = $isMentor ? '#15803d' : '#1d4ed8';
  $roleLabel     = $isMentor ? 'Mentor' : 'Mentee';
  $roleBadgeBg   = $isMentor ? '#dcfce7' : '#dbeafe';
  $roleBadgeText = $isMentor ? '#15803d' : '#1d4ed8';
@endphp
<style>
:root{--role-color:{{ $roleColor }};--role-dark:{{ $roleDark }}}
.chat-wrap{display:flex;gap:0;height:calc(100vh - 80px);border:1px solid var(--border);border-radius:20px;overflow:hidden;background:var(--surface)}
.chat-list{width:300px;border-right:1px solid var(--border);overflow-y:auto;flex-shrink:0}
.chat-main{flex:1;display:flex;flex-direction:column;min-width:0}
@media(max-width:768px){
  .chat-wrap{height:calc(100vh - 60px);border-radius:12px}
  .chat-list{width:100%;border-right:none;position:absolute;inset:0;z-index:2;background:var(--surface);border-radius:12px}
  .chat-list.hidden{display:none}
  .chat-main{width:100%}
  .chat-back{display:flex!important}
}
.chat-back{display:none;align-items:center;gap:8px;background:none;border:none;cursor:pointer;font-size:0.9rem;color:var(--blue-500);font-weight:600;padding:0}
@keyframes typingBounce{0%,60%,100%{transform:translateY(0)}30%{transform:translateY(-5px)}}
@keyframes recPulse{0%,100%{opacity:1}50%{opacity:0.3}}
@keyframes vnBounce{0%,100%{transform:scaleY(0.3)}50%{transform:scaleY(1)}}
.vn-player{display:flex;align-items:center;gap:8px;padding:2px 0 4px;min-width:200px}
.vn-play{width:36px;height:36px;border-radius:50%;border:none;cursor:pointer;font-size:0.8rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:opacity 0.15s}
.vn-play:hover{opacity:0.85}
.vn-wave{display:flex;align-items:center;gap:2px;flex:1;height:28px}
.vn-bar{width:3px;border-radius:2px;transition:background 0.08s}
.vn-time{font-size:0.7rem;font-weight:600;font-variant-numeric:tabular-nums;min-width:30px;text-align:right;flex-shrink:0}
.rec-bar{width:3px;border-radius:2px;background:#dc2626;transform-origin:center}
</style>

<div class="chat-wrap" style="position:relative">

  {{-- Conversation list --}}
  <div class="chat-list {{ isset($activeConversation) ? 'hidden' : '' }}" id="chatList">
    <div style="padding:16px;border-bottom:1px solid var(--border);font-family:'Sora',sans-serif;font-weight:700;display:flex;align-items:center;justify-content:space-between">
      Messages
      <span style="font-size:0.62rem;background:{{ $roleBadgeBg }};color:{{ $roleBadgeText }};border-radius:4px;padding:2px 7px;font-weight:700;text-transform:uppercase;letter-spacing:0.04em">{{ $roleLabel }}</span>
    </div>
    @forelse($conversations as $conv)
    @php
      $other   = $conv->otherUser(auth()->id());
      $lastMsg = $conv->messages->first();
      $isActive = isset($activeConversation) && $activeConversation->id === $conv->id;
      $isSkillEx = !$conv->mentorship_id && $conv->skill_exchange_request_id;
    @endphp
    <a href="{{ route('chat.show', $conv) }}"
       style="display:flex;align-items:center;gap:12px;padding:14px 16px;border-bottom:1px solid var(--border);text-decoration:none;background:{{ $isActive ? 'var(--surface-2)' : 'transparent' }};transition:background 0.15s"
       onmouseover="this.style.background='var(--surface-2)'" onmouseout="this.style.background='{{ $isActive ? 'var(--surface-2)' : 'transparent' }}'">
      <div class="avatar avatar-sm" @if($other?->role === 'mentor') style="background:#f97316" @endif>{{ $other?->initials ?? '?' }}</div>
      <div style="flex:1;min-width:0">
        <div style="font-weight:700;font-size:0.88rem;display:flex;align-items:center;gap:6px">
          {{ $other?->full_name ?? 'Unknown' }}
          @if($isSkillEx)<span style="font-size:0.62rem;background:#ede9fe;color:#5b21b6;border-radius:4px;padding:1px 5px;font-weight:700">SKILL</span>@endif
        </div>
        <div style="font-size:0.75rem;color:var(--text-3);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
          {{ $lastMsg?->body ?? 'No messages yet' }}
        </div>
      </div>
    </a>
    @empty
    <div style="padding:24px;text-align:center;color:var(--text-3);font-size:0.85rem">
      No conversations yet.<br>Start a mentorship or accept a skill exchange.
    </div>
    @endforelse
  </div>

  {{-- Message area --}}
  <div class="chat-main">
    @if(isset($activeConversation))
    @php
      $other    = $activeConversation->otherUser(auth()->id());
      $subtitle = $activeConversation->subtitle;
    @endphp
    <div style="padding:14px 16px;border-bottom:1px solid var(--border);border-top:3px solid var(--role-color);display:flex;align-items:center;gap:12px">
      <button class="chat-back" onclick="showList()"> Back</button>
      @if($other)
      <a href="{{ route('profile.show', $other) }}" style="display:flex;align-items:center;gap:12px;text-decoration:none;flex:1;min-width:0" title="View {{ $other->full_name }}'s profile">
        @if($other->avatar_url)
        <img src="{{ $other->avatar_url }}" style="width:36px;height:36px;border-radius:50%;object-fit:cover;flex-shrink:0;transition:opacity 0.15s" onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
        @else
        <div class="avatar avatar-sm" @if($other->role === 'mentor') style="background:#f97316" @endif>{{ $other->initials }}</div>
        @endif
        <div>
          <div style="font-weight:700;font-size:0.92rem;display:flex;align-items:center;gap:6px;color:var(--text)">
            {{ $other->full_name }}
            <span style="font-size:0.6rem;background:{{ $other->role === 'mentor' ? '#fff7ed' : '#dbeafe' }};color:{{ $other->role === 'mentor' ? '#ea580c' : '#1d4ed8' }};border-radius:4px;padding:2px 6px;font-weight:700;text-transform:uppercase;letter-spacing:0.04em">{{ $other->role }}</span>
          </div>
          @if($subtitle)<div style="font-size:0.75rem;color:var(--text-3)">{{ $subtitle }}</div>@endif
        </div>
      </a>
      @else
      <div style="display:flex;align-items:center;gap:12px;flex:1;min-width:0">
        <div class="avatar avatar-sm">?</div>
        <div style="font-weight:700;font-size:0.92rem;color:var(--text)">Unknown</div>
      </div>
      @endif
    </div>

    <div style="flex:1;overflow-y:auto;padding:20px;display:flex;flex-direction:column;gap:12px" id="messageArea">
      @forelse($messages as $msg)
      @php $mine = $msg->sender_id === auth()->id(); @endphp
      <div style="display:flex;{{ $mine ? 'justify-content:flex-end' : 'justify-content:flex-start' }}">
        <div style="max-width:65%;background:{{ $mine ? 'var(--blue-500)' : 'var(--surface-2)' }};color:{{ $mine ? '#fff' : 'var(--text)' }};border-radius:{{ $mine ? '18px 18px 4px 18px' : '18px 18px 18px 4px' }};padding:10px 14px;font-size:0.88rem;line-height:1.5">
          @if($msg->type === 'file')
            @php
              $ext     = strtolower(pathinfo($msg->file_name, PATHINFO_EXTENSION));
              $url     = asset('storage/'.$msg->file_path);
              $isImage = in_array($ext, ['jpg','jpeg','png','gif','webp','svg']);
              $isPdf   = $ext === 'pdf';
              $isDocx  = $ext === 'docx';
              $isAudio = in_array($ext, ['webm','mp3','ogg','wav','m4a','aac']);
              $icons   = ['doc'=>'','docx'=>'','xls'=>'','xlsx'=>'','ppt'=>'','pptx'=>'','zip'=>'','rar'=>'','txt'=>''];
              $icon    = $icons[$ext] ?? '';
            @endphp
            @if($isAudio)
              <div class="vn-player" data-src="{{ $url }}" data-mine="{{ $mine ? '1' : '0' }}">
                <button class="vn-play" onclick="vnToggle(this)" type="button"
                        style="background:{{ $mine ? '#fff' : 'var(--blue-500)' }};color:{{ $mine ? 'var(--blue-500)' : '#fff' }}"><x-icon name="play" :size="12" fill="currentColor" /></button>
                <div class="vn-wave"></div>
                <span class="vn-time" style="color:{{ $mine ? 'rgba(255,255,255,0.75)' : 'var(--text-3)' }}">0:00</span>
                <audio src="{{ $url }}" preload="metadata" style="display:none"></audio>
              </div>
            @elseif($isImage)
              <img src="{{ $url }}" alt="{{ $msg->file_name }}"
                   style="max-width:220px;max-height:180px;border-radius:10px;display:block;cursor:pointer;margin-bottom:4px"
                   onclick="openPreview('{{ $url }}','{{ $msg->file_name }}','image')">
            @elseif($isPdf)
              <div onclick="openPreview('{{ $url }}','{{ $msg->file_name }}','pdf')"
                   style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:8px 12px;background:rgba(0,0,0,0.1);border-radius:10px;margin-bottom:4px">
                <span style="font-size:1.4rem"></span>
                <div>
                  <div style="font-size:0.82rem;font-weight:600">{{ $msg->file_name }}</div>
                  <div style="font-size:0.7rem;opacity:0.7">Click to view</div>
                </div>
              </div>
            @elseif($isDocx)
              <div onclick="openPreview('{{ $url }}','{{ $msg->file_name }}','docx')"
                   style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:8px 12px;background:rgba(0,0,0,0.1);border-radius:10px;margin-bottom:4px">
                <span style="font-size:1.4rem"></span>
                <div>
                  <div style="font-size:0.82rem;font-weight:600">{{ $msg->file_name }}</div>
                  <div style="font-size:0.7rem;opacity:0.7">Click to view</div>
                </div>
              </div>
            @else
              <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;background:rgba(0,0,0,0.1);border-radius:10px;margin-bottom:4px">
                <span style="font-size:1.4rem">{{ $icon }}</span>
                <div>
                  <div style="font-size:0.82rem;font-weight:600">{{ $msg->file_name }}</div>
                  <a href="{{ $url }}" download style="font-size:0.7rem;opacity:0.8;color:{{ $mine ? '#fff' : 'var(--blue-500)' }}">Download</a>
                </div>
              </div>
            @endif
          @else
            {{ $msg->body }}
          @endif
          <div style="font-size:0.68rem;margin-top:4px;text-align:right;display:flex;align-items:center;justify-content:flex-end;gap:4px;opacity:0.75">
            <span>{{ $msg->created_at->format('g:i A') }}</span>
            @if($mine)
              @if($msg->read_at)
                <span class="msg-tick" data-id="{{ $msg->id }}" style="color:#93c5fd;font-size:0.75rem;font-weight:700" title="Read"></span>
              @else
                <span class="msg-tick" data-id="{{ $msg->id }}" style="opacity:0.55;font-size:0.75rem;font-weight:700" title="Sent"></span>
              @endif
            @endif
          </div>
        </div>
      </div>
      @empty
      <div style="text-align:center;color:var(--text-3);padding:40px;font-size:0.88rem">No messages yet. Say hello!</div>
      @endforelse
    </div>

    {{-- Typing indicator --}}
    <div id="typingIndicator" style="display:none;padding:6px 20px 0;font-size:0.78rem;color:var(--text-3);font-style:italic;min-height:24px">
      <span id="typingName"></span> is typing
      <span style="display:inline-flex;gap:2px;margin-left:2px">
        <span class="typing-dot" style="width:4px;height:4px;border-radius:50%;background:var(--text-3);display:inline-block;animation:typingBounce 1.2s infinite 0s"></span>
        <span class="typing-dot" style="width:4px;height:4px;border-radius:50%;background:var(--text-3);display:inline-block;animation:typingBounce 1.2s infinite 0.2s"></span>
        <span class="typing-dot" style="width:4px;height:4px;border-radius:50%;background:var(--text-3);display:inline-block;animation:typingBounce 1.2s infinite 0.4s"></span>
      </span>
    </div>

    <form method="POST" action="{{ route('chat.send', $activeConversation) }}" enctype="multipart/form-data"
          style="padding:12px 20px;border-top:1px solid var(--border)">
      @csrf
      <input type="file" name="file" id="chatFile" style="display:none" onchange="showFilePreview(this)">
      <div id="filePreview" style="display:none;align-items:center;gap:8px;background:var(--surface-2);border-radius:10px;padding:8px 12px;margin-bottom:8px;font-size:0.82rem">
        <span id="filePreviewName" style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"></span>
        <button type="button" onclick="clearFile()" style="background:none;border:none;cursor:pointer;display:inline-flex;align-items:center;color:var(--text-3)"><x-icon name="x" :size="15" /></button>
      </div>
      <div id="recordingStrip" style="display:none;align-items:center;gap:10px;background:var(--surface-2);border:1px solid #fecaca;border-radius:14px;padding:8px 14px;margin-bottom:8px">
        <button type="button" onclick="cancelRecording()" title="Cancel"
                style="background:none;border:none;cursor:pointer;color:#dc2626;display:inline-flex;align-items:center;padding:0;flex-shrink:0"><x-icon name="x" :size="17" /></button>
        <div id="recWave" style="display:flex;align-items:center;gap:2px;flex:1;height:28px"></div>
        <span id="recordTimer" style="font-size:0.8rem;font-weight:700;color:#dc2626;font-variant-numeric:tabular-nums;min-width:32px;text-align:right">0:00</span>
        <button type="button" onclick="stopRecording()" title="Send voice note"
                style="width:36px;height:36px;border-radius:50%;background:#dc2626;color:#fff;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0"><x-icon name="send" :size="15" /></button>
      </div>
      <div style="display:flex;gap:8px;align-items:center">
        <button type="button" onclick="document.getElementById('chatFile').click()" title="Attach file"
                style="background:none;border:none;cursor:pointer;display:inline-flex;align-items:center;padding:4px 6px;color:var(--text-3)"><x-icon name="paperclip" :size="18" /></button>
        <button type="button" id="micBtn" onclick="toggleRecording()" title="Voice note"
                style="background:none;border:none;cursor:pointer;display:inline-flex;align-items:center;padding:4px 6px;color:var(--text-3)"><x-icon name="mic" :size="18" /></button>
        <button type="button" onclick="startCall('video')" title="Start video call"
                style="background:none;border:none;cursor:pointer;display:inline-flex;align-items:center;padding:4px 6px;color:var(--text-3)"><x-icon name="video" :size="18" /></button>
        <button type="button" onclick="startCall('voice')" title="Start voice call"
                style="background:none;border:none;cursor:pointer;display:inline-flex;align-items:center;padding:4px 6px;color:var(--text-3)"><x-icon name="phone" :size="18" /></button>
        <button type="button" onclick="startCall('screen')" title="Share screen"
                style="background:none;border:none;cursor:pointer;display:inline-flex;align-items:center;padding:4px 6px;color:var(--text-3)"><x-icon name="monitor-up" :size="18" /></button>
        <input type="text" name="body" id="chatBody" class="form-input" placeholder="Type a message..." style="flex:1" autocomplete="off">
        <button type="submit" class="btn btn-sm" style="background:var(--role-color);color:#fff;border:none">Send</button>
      </div>
    </form>
    @else
    <div style="flex:1;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:0;padding:40px 20px">
      {{-- Illustration --}}
      <div style="position:relative;width:120px;height:100px;margin-bottom:24px">
        <div style="position:absolute;bottom:0;left:0;width:72px;height:72px;background:var(--blue-100);border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--blue-500)"><x-icon name="message-circle" :size="30" /></div>
        <div style="position:absolute;top:0;right:0;width:52px;height:52px;background:#fef9c3;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#ca8a04"><x-icon name="sparkles" :size="20" /></div>
        <div style="position:absolute;top:22px;left:30px;width:36px;height:36px;background:#dcfce7;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#16a34a"><x-icon name="users" :size="15" /></div>
      </div>
      <h3 style="font-family:'Sora',sans-serif;font-weight:800;font-size:1rem;margin:0 0 8px;color:var(--text)">Your messages live here</h3>
      <p style="font-size:0.82rem;color:var(--text-3);text-align:center;max-width:240px;line-height:1.6;margin:0 0 20px">Select a conversation on the left or start a new one by connecting with a mentor.</p>
      <a href="{{ route('mentors.index') }}" class="btn btn-primary btn-sm">Find a Mentor</a>
    </div>
    @endif
  </div>
</div>
@endsection

{{-- Calling overlay — shown to caller while waiting --}}
@if(isset($activeConversation))
@php $otherPerson = $activeConversation->otherUser(auth()->id()); @endphp
<div id="callingOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.88);z-index:10002;flex-direction:column;align-items:center;justify-content:center;gap:14px">
  <div class="avatar" style="width:72px;height:72px;font-size:1.5rem;border:3px solid rgba(255,255,255,0.2)">{{ $otherPerson?->initials ?? '?' }}</div>
  <div style="color:#fff;font-size:1.15rem;font-weight:700">{{ $otherPerson?->full_name ?? '' }}</div>
  <div id="callingStatusText" style="color:rgba(255,255,255,0.55);font-size:0.88rem;letter-spacing:0.04em">Calling…</div>
  <button onclick="cancelOutgoingCall()" style="margin-top:10px;background:#dc2626;color:#fff;border:none;width:56px;height:56px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center"><x-icon name="phone-off" :size="24" /></button>
  <div style="color:rgba(255,255,255,0.3);font-size:0.75rem">Tap to cancel</div>
</div>
@endif

{{-- Call modal --}}
@if(isset($activeConversation))
<div id="callModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.9);z-index:10000;flex-direction:column">
  <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 20px;background:rgba(0,0,0,0.5)">
    <div style="display:flex;align-items:center;gap:10px">
      <span id="callIcon" style="font-size:1.3rem"></span>
      <span id="callTitle" style="color:#fff;font-weight:700;font-size:0.95rem"></span>
    </div>
    <button onclick="endCall()" style="background:#dc2626;color:#fff;border:none;padding:8px 18px;border-radius:8px;cursor:pointer;font-weight:700">End Call</button>
  </div>
  <div id="callFrame" style="flex:1;width:100%"></div>
</div>
@endif

{{-- File preview modal --}}
<div id="fileModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.75);z-index:9999;align-items:center;justify-content:center;flex-direction:column;gap:12px" onclick="closePreview(event)">
  <div style="display:flex;justify-content:space-between;align-items:center;width:90%;max-width:900px">
    <span id="modalFileName" style="color:#fff;font-weight:600;font-size:0.95rem"></span>
    <div style="display:flex;gap:8px">
      <a id="modalDownload" href="#" download style="background:rgba(255,255,255,0.15);color:#fff;padding:6px 14px;border-radius:8px;font-size:0.82rem;text-decoration:none;display:inline-flex;align-items:center;gap:6px"><x-icon name="download" :size="14" /> Download</a>
      <button onclick="closePreview()" style="background:rgba(255,255,255,0.15);color:#fff;border:none;padding:6px 14px;border-radius:8px;cursor:pointer;font-size:0.82rem;display:inline-flex;align-items:center;gap:6px"><x-icon name="x" :size="14" /> Close</button>
    </div>
  </div>
  <div id="modalBody" style="width:90%;max-width:900px;max-height:82vh;overflow:auto;border-radius:14px;background:#fff;display:flex;align-items:center;justify-content:center"></div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/mammoth@1.8.0/mammoth.browser.min.js"></script>
<script>
const area = document.getElementById('messageArea');
if (area) area.scrollTop = area.scrollHeight;

const VN_PLAY_SVG  = '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="6 3 20 12 6 21 6 3"/></svg>';
const VN_PAUSE_SVG = '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="6" y="4" width="4" height="16" rx="1"/><rect x="14" y="4" width="4" height="16" rx="1"/></svg>';

function showList() {
  const list = document.getElementById('chatList');
  if (list) list.classList.remove('hidden');
}

document.querySelectorAll('#chatList a').forEach(link => {
  link.addEventListener('click', () => {
    const list = document.getElementById('chatList');
    if (window.innerWidth <= 768 && list) list.classList.add('hidden');
  });
});

function showFilePreview(input) {
  const preview = document.getElementById('filePreview');
  const name    = document.getElementById('filePreviewName');
  if (input.files && input.files[0]) {
    name.textContent = input.files[0].name;
    preview.style.display = 'flex';
  }
}

function clearFile() {
  document.getElementById('chatFile').value = '';
  document.getElementById('filePreview').style.display = 'none';
  document.getElementById('filePreviewName').textContent = '';
}

function openPreview(url, name, type) {
  const modal = document.getElementById('fileModal');
  const body  = document.getElementById('modalBody');
  const title = document.getElementById('modalFileName');
  const dl    = document.getElementById('modalDownload');
  title.textContent = name;
  dl.href = url;
  dl.download = name;
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';

  if (type === 'image') {
    body.innerHTML = `<img src="${url}" style="max-width:100%;max-height:80vh;border-radius:14px;display:block">`;
  } else if (type === 'pdf') {
    body.innerHTML = `<iframe src="${url}" style="width:100%;height:80vh;border:none;border-radius:14px"></iframe>`;
  } else if (type === 'docx') {
    body.innerHTML = `<div style="padding:24px;color:#64748b;font-size:0.9rem">Loading document…</div>`;
    fetch(url)
      .then(r => r.arrayBuffer())
      .then(buffer => mammoth.convertToHtml({ arrayBuffer: buffer }))
      .then(result => {
        body.innerHTML = `<div style="padding:32px 40px;max-width:800px;width:100%;font-family:Georgia,serif;font-size:1rem;line-height:1.8;color:#1e293b">${result.value}</div>`;
      })
      .catch(() => {
        body.innerHTML = `<div style="padding:24px;color:#dc2626">Could not render document. <a href="${url}" download style="color:#2563eb">Download instead</a>.</div>`;
      });
  }
}

let _mySessionId      = null;
let _callPollTimer    = null;
let _callTimeoutTimer = null;
const CALL_TIMEOUT_MS = 60000;

async function startCall(type) {
  @isset($activeConversation)
  try {
    const res = await fetch('{{ route("chat.call", $activeConversation) }}', {
      method: 'POST',
      headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
      body: JSON.stringify({type}),
    });
    const data = await res.json();
    _mySessionId = data.session_id;

    const labels = {video: 'Video call', voice: 'Voice call', screen: 'Screen share'};
    document.getElementById('callingStatusText').textContent = (labels[type] || 'Call') + ' — calling…';
    document.getElementById('callingOverlay').style.display = 'flex';

    const roomName = data.room;
    let url = `https://meet.jit.si/${roomName}#config.prejoinPageEnabled=false&config.startWithVideoMuted=${type !== 'video'}&config.startWithAudioMuted=false&userInfo.displayName="{{ urlencode(auth()->user()->full_name) }}"`;
    if (type === 'screen') url += '&config.startScreenSharing=true';
    window.open(url, '_blank');

    _callPollTimer = setInterval(async () => {
      try {
        const sr = await fetch('/sessions/' + _mySessionId + '/status');
        const sd = await sr.json();
        if (sd.call_outcome === 'missed' || sd.status === 'cancelled') {
          _stopCallTimers();
          _hideCallingOverlay();
          _showCallToast('No answer', '#dc2626');
        } else if (sd.call_outcome === 'answered') {
          _stopCallTimers();
          _hideCallingOverlay();
        }
      } catch (_) {}
    }, 2500);

    _callTimeoutTimer = setTimeout(() => {
      _stopCallTimers();
      if (_mySessionId) {
        fetch('/sessions/' + _mySessionId + '/missed', {
          method: 'POST',
          headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        });
        _mySessionId = null;
      }
      _hideCallingOverlay();
      _showCallToast('No answer', '#dc2626');
    }, CALL_TIMEOUT_MS);

  } catch (e) {
    alert('Could not start call. Please try again.');
  }
  @endisset
}

function cancelOutgoingCall() {
  _stopCallTimers();
  if (_mySessionId) {
    fetch('/sessions/' + _mySessionId + '/missed', {
      method: 'POST',
      headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
    });
    _mySessionId = null;
  }
  _hideCallingOverlay();
}

function _stopCallTimers() {
  if (_callPollTimer)    { clearInterval(_callPollTimer);  _callPollTimer    = null; }
  if (_callTimeoutTimer) { clearTimeout(_callTimeoutTimer); _callTimeoutTimer = null; }
}

function _hideCallingOverlay() {
  const el = document.getElementById('callingOverlay');
  if (el) el.style.display = 'none';
}

function _showCallToast(msg, bg) {
  const t = document.createElement('div');
  t.style.cssText = `position:fixed;bottom:28px;left:50%;transform:translateX(-50%);background:${bg};color:#fff;padding:11px 24px;border-radius:10px;font-weight:700;font-size:0.88rem;z-index:99999;box-shadow:0 4px 16px rgba(0,0,0,0.35)`;
  t.textContent = msg;
  document.body.appendChild(t);
  setTimeout(() => t.remove(), 3500);
}

function endCall() {
  document.getElementById('callModal').style.display = 'none';
}

//  Incoming call polling 
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
  banner.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:20000;background:#1e293b;color:#fff;border-radius:16px;padding:18px 22px;box-shadow:0 8px 32px rgba(0,0,0,0.4);display:flex;flex-direction:column;gap:10px;min-width:280px;max-width:320px;animation:slideUp 0.3s ease';
  banner.innerHTML = `
    <style>@keyframes slideUp{from{transform:translateY(20px);opacity:0}to{transform:translateY(0);opacity:1}}</style>
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
  setTimeout(() => dismissCallBanner(), 90000);
}

function joinIncomingCall(room, type, sessionId) {
  if (sessionId) {
    fetch('/sessions/' + sessionId + '/answered', {
      method: 'POST',
      headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
    });
  }
  let url = `https://meet.jit.si/${room}#config.prejoinPageEnabled=false&config.startWithVideoMuted=${type !== 'video'}&config.startWithAudioMuted=false&userInfo.displayName="{{ urlencode(auth()->user()->full_name) }}"`;
  if (type === 'screen') url += '&config.startScreenSharing=true';
  window.open(url, '_blank');
  _dismissBanner(false);
}

function dismissCallBanner() {
  _dismissBanner(true);
}

function _dismissBanner(markMissed) {
  const banner = document.getElementById('callBanner');
  const sid = banner ? banner.dataset.sessionId : null;
  if (banner) banner.remove();
  if (markMissed && sid) {
    fetch('/sessions/' + sid + '/missed', {
      method: 'POST',
      headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
    });
  }
  if (_callBannerNotifId) {
    fetch('/notifications/' + _callBannerNotifId + '/read', {
      method: 'POST',
      headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
    });
    _callBannerNotifId = null;
  }
}

async function pollForIncomingCall() {
  try {
    const res = await fetch('{{ route("notifications.pendingCall") }}');
    const data = await res.json();
    if (data.call && !document.getElementById('callBanner')) {
      showCallBanner(data.call);
    }
    const banner = document.getElementById('callBanner');
    if (banner && banner.dataset.sessionId) {
      const sr = await fetch('/sessions/' + banner.dataset.sessionId + '/status');
      const sd = await sr.json();
      if (sd.status === 'cancelled') {
        _dismissBanner(false);
      }
    }
  } catch (_) {}
}

setInterval(pollForIncomingCall, 5000);
pollForIncomingCall();

//  Typing indicator 
@isset($activeConversation)
let _typingTimer = null;
const _csrfToken = '{{ csrf_token() }}';

document.getElementById('chatBody')?.addEventListener('input', () => {
  fetch('{{ route("chat.typing", $activeConversation) }}', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': _csrfToken, 'Content-Type': 'application/json' },
  });
  clearTimeout(_typingTimer);
  _typingTimer = setTimeout(() => {}, 2000);
});

async function pollTyping() {
  try {
    const res  = await fetch('{{ route("chat.isTyping", $activeConversation) }}');
    const data = await res.json();
    const el   = document.getElementById('typingIndicator');
    if (el) {
      if (data.typing) {
        document.getElementById('typingName').textContent = data.name;
        el.style.display = 'block';
        const area = document.getElementById('messageArea');
        if (area) area.scrollTop = area.scrollHeight;
      } else {
        el.style.display = 'none';
      }
    }
  } catch (_) {}
}
setInterval(pollTyping, 2000);
pollTyping();

//  Read receipt polling 
async function pollReadStatus() {
  try {
    const res  = await fetch('{{ route("chat.readStatus", $activeConversation) }}');
    const data = await res.json();
    (data.read_ids || []).forEach(id => {
      const tick = document.querySelector(`.msg-tick[data-id="${id}"]`);
      if (tick && tick.textContent.trim() === '') {
        tick.textContent  = '';
        tick.style.color  = '#93c5fd';
        tick.style.opacity = '1';
        tick.title = 'Read';
      }
    });
  } catch (_) {}
}
setInterval(pollReadStatus, 5000);
@endisset

function closePreview(e) {
  if (e && e.target !== document.getElementById('fileModal')) return;
  document.getElementById('fileModal').style.display = 'none';
  document.getElementById('modalBody').innerHTML = '';
  document.body.style.overflow = '';
}

//  Voice note player 
const VN_BARS   = 28;
const VN_SEED   = (s) => { let x = Math.sin(s) * 10000; return x - Math.floor(x); };

function vnInit(container) {
  const mine  = container.dataset.mine === '1';
  const audio = container.querySelector('audio');
  const wave  = container.querySelector('.vn-wave');
  const timeEl = container.querySelector('.vn-time');
  const btn   = container.querySelector('.vn-play');

  const seed = container.dataset.src.length;
  const heights = Array.from({ length: VN_BARS }, (_, i) => Math.max(4, Math.round(VN_SEED(seed + i) * 22) + 4));

  wave.innerHTML = heights.map((h, i) =>
    `<span class="vn-bar" data-i="${i}" style="height:${h}px;background:${mine ? 'rgba(255,255,255,0.35)' : '#cbd5e1'}"></span>`
  ).join('');

  const bars = wave.querySelectorAll('.vn-bar');

  audio.addEventListener('loadedmetadata', () => {
    timeEl.textContent = vnFmt(audio.duration);
  });

  audio.addEventListener('timeupdate', () => {
    const pct = audio.currentTime / (audio.duration || 1);
    bars.forEach((bar, i) => {
      bar.style.background = i / VN_BARS <= pct
        ? (mine ? '#fff' : 'var(--blue-500)')
        : (mine ? 'rgba(255,255,255,0.35)' : '#cbd5e1');
    });
    const left = (audio.duration || 0) - audio.currentTime;
    timeEl.textContent = vnFmt(left);
  });

  audio.addEventListener('ended', () => {
    btn.innerHTML = VN_PLAY_SVG;
    bars.forEach(b => b.style.background = mine ? 'rgba(255,255,255,0.35)' : '#cbd5e1');
    timeEl.textContent = vnFmt(audio.duration);
  });
}

function vnToggle(btn) {
  const container = btn.closest('.vn-player');
  const audio = container.querySelector('audio');
  if (audio.paused) {
    document.querySelectorAll('.vn-player audio').forEach(a => {
      if (a !== audio && !a.paused) {
        a.pause();
        a.closest('.vn-player').querySelector('.vn-play').innerHTML = VN_PLAY_SVG;
      }
    });
    audio.play();
    btn.innerHTML = VN_PAUSE_SVG;
  } else {
    audio.pause();
    btn.innerHTML = VN_PLAY_SVG;
  }
}

function vnFmt(s) {
  if (!s || isNaN(s)) return '0:00';
  return Math.floor(s / 60) + ':' + String(Math.floor(s % 60)).padStart(2, '0');
}

document.querySelectorAll('.vn-player').forEach(vnInit);

//  Voice recording 
@isset($activeConversation)
let _mediaRecorder  = null;
let _audioChunks    = [];
let _recordInterval = null;
let _recordSeconds  = 0;
let _recAnimFrame   = null;

function _buildRecWave() {
  const wave = document.getElementById('recWave');
  wave.innerHTML = Array.from({ length: 24 }, (_, i) =>
    `<span class="rec-bar" style="height:4px;animation:vnBounce ${0.4 + (i % 5) * 0.1}s ease-in-out infinite alternate;animation-delay:${(i % 6) * 0.07}s"></span>`
  ).join('');
}

async function toggleRecording() {
  if (_mediaRecorder && _mediaRecorder.state === 'recording') {
    stopRecording();
  } else {
    await startRecording();
  }
}

async function startRecording() {
  try {
    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
    _audioChunks   = [];
    _recordSeconds = 0;
    _mediaRecorder = new MediaRecorder(stream);
    _mediaRecorder.ondataavailable = e => { if (e.data.size > 0) _audioChunks.push(e.data); };
    _mediaRecorder.onstop = sendVoiceNote;
    _mediaRecorder.start(100);

    _buildRecWave();
    document.getElementById('recordingStrip').style.display = 'flex';
    document.getElementById('micBtn').style.color = '#dc2626';

    _recordInterval = setInterval(() => {
      _recordSeconds++;
      document.getElementById('recordTimer').textContent =
        Math.floor(_recordSeconds / 60) + ':' + String(_recordSeconds % 60).padStart(2, '0');
      if (_recordSeconds >= 120) stopRecording();
    }, 1000);
  } catch (e) {
    alert('Microphone access denied. Please allow microphone permission in your browser.');
  }
}

function stopRecording() {
  if (_mediaRecorder && _mediaRecorder.state === 'recording') {
    _mediaRecorder.stop();
    _mediaRecorder.stream.getTracks().forEach(t => t.stop());
  }
  clearInterval(_recordInterval);
  document.getElementById('recordingStrip').style.display = 'none';
  document.getElementById('micBtn').style.color = 'var(--text-3)';
}

function cancelRecording() {
  if (_mediaRecorder && _mediaRecorder.state === 'recording') {
    _mediaRecorder.onstop = null;
    _mediaRecorder.stop();
    _mediaRecorder.stream.getTracks().forEach(t => t.stop());
  }
  _audioChunks = [];
  clearInterval(_recordInterval);
  document.getElementById('recordingStrip').style.display = 'none';
  document.getElementById('micBtn').style.color = 'var(--text-3)';
}

async function sendVoiceNote() {
  if (!_audioChunks.length) return;
  const mimeType = MediaRecorder.isTypeSupported('audio/webm') ? 'audio/webm' : 'audio/ogg';
  const ext      = mimeType === 'audio/webm' ? 'webm' : 'ogg';
  const blob     = new Blob(_audioChunks, { type: mimeType });
  const filename = `voice-note-${Date.now()}.${ext}`;
  const form = new FormData();
  form.append('_token', _csrfToken);
  form.append('file', blob, filename);
  try {
    const res  = await fetch('{{ route("chat.send", $activeConversation) }}', {
      method: 'POST', headers: { 'Accept': 'application/json' }, body: form,
    });
    const data = await res.json();
    if (data.message) appendVoiceMessage(data.message);
  } catch (e) {
    alert('Failed to send voice note. Please try again.');
  }
  _audioChunks = [];
}

function appendVoiceMessage(msg) {
  const area = document.getElementById('messageArea');
  const url  = msg.file_path ? `/storage/${msg.file_path}` : '';
  const time = new Date(msg.created_at).toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
  const div  = document.createElement('div');
  div.style.cssText = 'display:flex;justify-content:flex-end';
  div.innerHTML = `
    <div style="max-width:65%;background:var(--blue-500);color:#fff;border-radius:18px 18px 4px 18px;padding:10px 14px;font-size:0.88rem;line-height:1.5">
      <div class="vn-player" data-src="${url}" data-mine="1">
        <button class="vn-play" onclick="vnToggle(this)" type="button" style="background:#fff;color:var(--blue-500)">${VN_PLAY_SVG}</button>
        <div class="vn-wave"></div>
        <span class="vn-time" style="color:rgba(255,255,255,0.75)">0:00</span>
        <audio src="${url}" preload="metadata" style="display:none"></audio>
      </div>
      <div style="font-size:0.68rem;margin-top:2px;text-align:right;opacity:0.75">${time} <span style="opacity:0.55;font-size:0.75rem;font-weight:700"></span></div>
    </div>`;
  area.appendChild(div);
  area.scrollTop = area.scrollHeight;
  vnInit(div.querySelector('.vn-player'));
}
@endisset
</script>
@endpush
