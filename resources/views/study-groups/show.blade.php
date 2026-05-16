@extends('layouts.sidebar')
@section('title', $studyGroup->name)

@section('page-content')
<style>
.sg-wrap{display:flex;gap:0;height:calc(100vh - 80px);border:1px solid var(--border);border-radius:20px;overflow:hidden;background:var(--surface)}
.sg-sidebar{width:240px;border-right:1px solid var(--border);overflow-y:auto;flex-shrink:0;display:flex;flex-direction:column}
.sg-main{flex:1;display:flex;flex-direction:column;min-width:0}
@media(max-width:768px){.sg-sidebar{display:none}.sg-wrap{border-radius:12px}}
</style>

<div class="sg-wrap">

  {{-- Left sidebar: group info + members --}}
  <div class="sg-sidebar">
    <div style="padding:16px;border-bottom:1px solid var(--border)">
      <div style="font-family:'Sora',sans-serif;font-weight:800;font-size:0.95rem">{{ $studyGroup->name }}</div>
      <div style="font-size:0.75rem;color:var(--blue-500);font-weight:600;margin-top:2px">{{ $studyGroup->topic }}</div>
      @if($studyGroup->description)
      <div style="font-size:0.75rem;color:var(--text-3);margin-top:6px;line-height:1.5">{{ $studyGroup->description }}</div>
      @endif
    </div>

    {{-- Call buttons --}}
    @if($studyGroup->isMember($user))
    <div style="padding:12px 16px;border-bottom:1px solid var(--border)">
      <div style="font-size:0.72rem;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px">Start a Call</div>
      <div style="display:flex;gap:6px">
        <button onclick="startGroupCall('video')" title="Video call"
                style="flex:1;background:var(--blue-500);color:#fff;border:none;border-radius:8px;padding:7px 0;cursor:pointer;font-size:0.72rem;font-weight:700">Video</button>
        <button onclick="startGroupCall('voice')" title="Voice call"
                style="flex:1;background:var(--surface-2);color:var(--text);border:none;border-radius:8px;padding:7px 0;cursor:pointer;font-size:0.72rem;font-weight:700">Voice</button>
        <button onclick="startGroupCall('screen')" title="Share screen"
                style="flex:1;background:var(--surface-2);color:var(--text);border:none;border-radius:8px;padding:7px 0;cursor:pointer;font-size:0.72rem;font-weight:700">Screen</button>
      </div>
    </div>
    @endif

    {{-- Members --}}
    <div style="padding:12px 16px;border-bottom:1px solid var(--border)">
      <div style="font-size:0.75rem;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px">
        Members ({{ $studyGroup->members->count() }} / {{ $studyGroup->max_members }})
      </div>
      @foreach($studyGroup->members as $member)
      <div style="display:flex;align-items:center;gap:8px;padding:4px 0">
        <div class="avatar" style="width:28px;height:28px;font-size:0.65rem;flex-shrink:0">{{ $member->user->initials }}</div>
        <div style="min-width:0">
          <div style="font-size:0.8rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $member->user->full_name }}</div>
          @if($member->role === 'admin')
          <div style="font-size:0.65rem;color:var(--blue-500);font-weight:700">Admin</div>
          @endif
        </div>
      </div>
      @endforeach
    </div>

    {{-- Leave group --}}
    @if($studyGroup->isMember($user))
    <div style="padding:12px 16px;margin-top:auto">
      <form method="POST" action="{{ route('study-groups.leave', $studyGroup) }}" data-confirm="Leave this group? You can rejoin later.">
        @csrf @method('DELETE')
        <button type="submit" style="width:100%;background:none;border:1px solid #dc2626;color:#dc2626;padding:6px;border-radius:8px;cursor:pointer;font-size:0.8rem">Leave Group</button>
      </form>
    </div>
    @else
    <div style="padding:12px 16px;margin-top:auto">
      <form method="POST" action="{{ route('study-groups.join', $studyGroup) }}">
        @csrf
        <button type="submit" class="btn btn-primary btn-sm" style="width:100%">Join Group</button>
      </form>
    </div>
    @endif
  </div>

  {{-- Main: group chat --}}
  <div class="sg-main">
    <div style="padding:14px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
      <div>
        <div style="font-weight:700;font-size:0.92rem">Group Chat</div>
        <div style="font-size:0.75rem;color:var(--text-3)">{{ $studyGroup->members->count() }} members</div>
      </div>
      <a href="{{ route('study-groups.index') }}" style="font-size:0.8rem;color:var(--blue-500);text-decoration:none"> Back</a>
    </div>

    <div style="flex:1;overflow-y:auto;padding:20px;display:flex;flex-direction:column;gap:12px" id="messageArea">
      @if(session('success'))
      <div style="text-align:center;font-size:0.8rem;color:#166534;background:#dcfce7;border-radius:8px;padding:8px">{{ session('success') }}</div>
      @endif
      @forelse($messages as $msg)
      @php $mine = $msg->sender_id === auth()->id(); @endphp
      <div style="display:flex;{{ $mine ? 'justify-content:flex-end' : 'justify-content:flex-start' }};gap:8px;align-items:flex-end">
        @if(!$mine)
        <div class="avatar" style="width:28px;height:28px;font-size:0.65rem;flex-shrink:0">{{ $msg->sender->initials }}</div>
        @endif
        <div style="max-width:65%">
          @if(!$mine)
          <div style="font-size:0.7rem;font-weight:600;color:var(--text-3);margin-bottom:2px">{{ $msg->sender->full_name }}</div>
          @endif
          <div style="background:{{ $mine ? 'var(--blue-500)' : 'var(--surface-2)' }};color:{{ $mine ? '#fff' : 'var(--text)' }};border-radius:{{ $mine ? '18px 18px 4px 18px' : '18px 18px 18px 4px' }};padding:10px 14px;font-size:0.88rem;line-height:1.5">
            @if($msg->type === 'file')
            @php
              $ext = strtolower(pathinfo($msg->file_name, PATHINFO_EXTENSION));
              $url = asset('storage/'.$msg->file_path);
              $isImage = in_array($ext, ['jpg','jpeg','png','gif','webp']);
            @endphp
            @if($isImage)
              <img src="{{ $url }}" style="max-width:200px;max-height:160px;border-radius:8px;display:block;margin-bottom:4px">
            @else
              <a href="{{ $url }}" download style="color:{{ $mine ? '#fff' : 'var(--blue-500)' }};font-size:0.82rem"> {{ $msg->file_name }}</a>
            @endif
            @else
            {{ $msg->body }}
            @endif
            <div style="font-size:0.68rem;opacity:0.6;margin-top:4px;text-align:right">{{ $msg->created_at->format('g:i A') }}</div>
          </div>
        </div>
      </div>
      @empty
      <div style="text-align:center;color:var(--text-3);padding:40px;font-size:0.88rem">No messages yet. Start the conversation!</div>
      @endforelse
    </div>

    @if($studyGroup->isMember($user))
    <form method="POST" action="{{ route('study-groups.message', $studyGroup) }}" enctype="multipart/form-data"
          style="padding:12px 20px;border-top:1px solid var(--border)">
      @csrf
      <input type="file" name="file" id="sgFile" style="display:none" onchange="showSgPreview(this)">
      <div id="sgFilePreview" style="display:none;align-items:center;gap:8px;background:var(--surface-2);border-radius:10px;padding:8px 12px;margin-bottom:8px;font-size:0.82rem">
        <span id="sgFileName" style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"></span>
        <button type="button" onclick="clearSgFile()" style="background:none;border:none;cursor:pointer;font-size:1rem;color:var(--text-3)"></button>
      </div>
      <div style="display:flex;gap:8px;align-items:center">
        <button type="button" onclick="document.getElementById('sgFile').click()" style="background:none;border:1px solid var(--border);border-radius:6px;cursor:pointer;font-size:0.72rem;padding:4px 8px;color:var(--text-3);font-weight:600" title="Attach file">Attach</button>
        <input type="text" name="body" class="form-input" placeholder="Type a message to the group..." style="flex:1" autocomplete="off">
        <button type="submit" class="btn btn-primary btn-sm">Send</button>
      </div>
    </form>
    @else
    <div style="padding:12px 20px;border-top:1px solid var(--border);text-align:center;color:var(--text-3);font-size:0.85rem">
      <a href="{{ route('study-groups.join', $studyGroup) }}" style="color:var(--blue-500);font-weight:600">Join the group</a> to send messages.
    </div>
    @endif
  </div>
</div>
@endsection

@push('scripts')
<script>
const area = document.getElementById('messageArea');
if (area) area.scrollTop = area.scrollHeight;

function showSgPreview(input) {
  if (input.files && input.files[0]) {
    document.getElementById('sgFileName').textContent = input.files[0].name;
    document.getElementById('sgFilePreview').style.display = 'flex';
  }
}
function clearSgFile() {
  document.getElementById('sgFile').value = '';
  document.getElementById('sgFilePreview').style.display = 'none';
}

async function startGroupCall(type) {
  try {
    await fetch('{{ route("study-groups.call", $studyGroup) }}', {
      method: 'POST',
      headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
      body: JSON.stringify({type}),
    });
  } catch (_) {}

  const room = '{{ $studyGroup->roomName() }}';
  let url = `https://meet.jit.si/${room}#config.prejoinPageEnabled=false&config.startWithVideoMuted=${type !== 'video'}&config.startWithAudioMuted=false&userInfo.displayName="{{ urlencode(auth()->user()->full_name) }}"`;
  if (type === 'screen') url += '&config.startScreenSharing=true';
  window.open(url, '_blank');
}
</script>
@endpush
