@extends('layouts.sidebar')
@section('title', 'Study Groups')

@section('page-content')
<style>
.sg-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px}
.sg-card{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:20px;display:flex;flex-direction:column;gap:10px}
.sg-badge{display:inline-block;background:var(--blue-500);color:#fff;border-radius:6px;padding:2px 8px;font-size:0.7rem;font-weight:700;text-transform:uppercase}
</style>

<div style="max-width:1100px;margin:0 auto">

  {{-- Header --}}
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px">
    <div>
      <h1 style="font-family:'Sora',sans-serif;font-size:1.4rem;font-weight:800;margin:0">Study Groups</h1>
      <p style="color:var(--text-3);font-size:0.85rem;margin:4px 0 0">Collaborate with peers on shared topics</p>
    </div>
    <button onclick="document.getElementById('createModal').style.display='flex'" class="btn btn-primary">+ New Group</button>
  </div>

  @if(session('success'))
  <div style="background:#dcfce7;border:1px solid #86efac;color:#166534;padding:10px 16px;border-radius:10px;margin-bottom:16px;font-size:0.88rem">{{ session('success') }}</div>
  @endif

  {{-- My Groups --}}
  @if($myGroups->isNotEmpty())
  <h2 style="font-size:1rem;font-weight:700;margin-bottom:12px">My Groups ({{ $myGroups->count() }})</h2>
  <div class="sg-grid" style="margin-bottom:32px">
    @foreach($myGroups as $group)
    <div class="sg-card">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px">
        <div>
          <div style="font-weight:700;font-size:0.95rem">{{ $group->name }}</div>
          <span class="sg-badge">{{ $group->topic }}</span>
        </div>
        <div style="font-size:0.75rem;color:var(--text-3);white-space:nowrap">{{ $group->members_count }} / {{ $group->max_members }} members</div>
      </div>
      @if($group->description)
      <div style="font-size:0.82rem;color:var(--text-3);line-height:1.5">{{ Str::limit($group->description, 80) }}</div>
      @endif
      <div style="margin-top:auto;padding-top:6px">
        <a href="{{ route('study-groups.show', $group) }}" class="btn btn-primary btn-sm" style="width:100%;text-align:center;display:block">Open</a>
      </div>
    </div>
    @endforeach
  </div>
  @endif

  {{-- Other Groups --}}
  <h2 style="font-size:1rem;font-weight:700;margin-bottom:12px">Open Groups</h2>
  @if($otherGroups->isNotEmpty())
  <div class="sg-grid">
    @foreach($otherGroups as $group)
    <div class="sg-card">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px">
        <div>
          <div style="font-weight:700;font-size:0.95rem">{{ $group->name }}</div>
          <span class="sg-badge" style="background:var(--surface-2);color:var(--text)">{{ $group->topic }}</span>
        </div>
        <div style="font-size:0.75rem;color:var(--text-3);white-space:nowrap">{{ $group->members_count }} / {{ $group->max_members }}</div>
      </div>
      @if($group->description)
      <div style="font-size:0.82rem;color:var(--text-3);line-height:1.5">{{ Str::limit($group->description, 80) }}</div>
      @endif
      <div style="font-size:0.75rem;color:var(--text-3)">by {{ $group->creator->full_name }}</div>
      <div style="display:flex;gap:8px;margin-top:auto;padding-top:6px">
        <a href="{{ route('study-groups.show', $group) }}" class="btn btn-sm" style="flex:1;text-align:center;background:var(--surface-2);color:var(--text)">View</a>
        @if($group->members_count < $group->max_members)
        <form method="POST" action="{{ route('study-groups.join', $group) }}" style="flex:1">
          @csrf
          <button type="submit" class="btn btn-primary btn-sm" style="width:100%">Join</button>
        </form>
        @else
        <span class="btn btn-sm" style="flex:1;text-align:center;background:var(--surface-2);color:var(--text-3);cursor:not-allowed">Full</span>
        @endif
      </div>
    </div>
    @endforeach
  </div>
  @else
  <div style="text-align:center;padding:48px;color:var(--text-3)">
    <p>No open groups yet. Be the first to create one!</p>
  </div>
  @endif
</div>

{{-- Create Group Modal --}}
<div id="createModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center" onclick="if(event.target===this)this.style.display='none'">
  <div style="background:var(--surface);border-radius:20px;padding:28px;width:90%;max-width:480px;box-shadow:0 8px 32px rgba(0,0,0,0.2)">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
      <h2 style="font-family:'Sora',sans-serif;font-size:1.1rem;font-weight:800;margin:0">Create Study Group</h2>
      <button onclick="document.getElementById('createModal').style.display='none'" style="background:none;border:none;cursor:pointer;font-size:1.2rem;color:var(--text-3)"></button>
    </div>
    <form method="POST" action="{{ route('study-groups.store') }}">
      @csrf
      <div style="display:flex;flex-direction:column;gap:14px">
        <div>
          <label class="form-label">Group Name</label>
          <input type="text" name="name" class="form-input" placeholder="e.g. DSA Study Circle" required>
        </div>
        <div>
          <label class="form-label">Topic</label>
          <input type="text" name="topic" class="form-input" placeholder="e.g. Data Structures" required>
        </div>
        <div>
          <label class="form-label">Description <span style="color:var(--text-3);font-weight:400">(optional)</span></label>
          <textarea name="description" class="form-input" rows="3" placeholder="What will this group focus on?"></textarea>
        </div>
        <div>
          <label class="form-label">Max Members</label>
          <input type="number" name="max_members" class="form-input" value="20" min="2" max="100">
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top:4px">Create Group</button>
      </div>
    </form>
  </div>
</div>

@if($errors->any())
<script>document.getElementById('createModal').style.display='flex';</script>
@endif
@endsection
