@extends('layouts.sidebar')
@section('title', 'Resources')

@section('page-content')
<style>
.res-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px}
.res-card{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:18px;display:flex;flex-direction:column;gap:8px}
</style>

<div style="max-width:1100px;margin:0 auto">

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px">
    <div>
      <h1 style="font-family:'Sora',sans-serif;font-size:1.4rem;font-weight:800;margin:0">Resources</h1>
      <p style="color:var(--text-3);font-size:0.85rem;margin:4px 0 0">Shared study materials and files</p>
    </div>
    <button onclick="document.getElementById('uploadModal').style.display='flex'" class="btn btn-primary">+ Upload</button>
  </div>

  @if(session('success'))
  <div style="background:#dcfce7;border:1px solid #86efac;color:#166534;padding:10px 16px;border-radius:10px;margin-bottom:16px;font-size:0.88rem">{{ session('success') }}</div>
  @endif

  @if($resources->isNotEmpty())
  <div class="res-grid">
    @foreach($resources as $res)
    @php
      $ext  = strtolower(pathinfo($res->file_name, PATHINFO_EXTENSION));
      $url  = asset('storage/'.$res->file_path);
      $size = $res->file_size > 1048576
              ? round($res->file_size/1048576, 1).'MB'
              : round($res->file_size/1024, 0).'KB';
    @endphp
    <div class="res-card">
      <div style="display:inline-block;background:var(--surface-2);border-radius:6px;padding:3px 8px;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-3)">{{ $ext ?: 'file' }}</div>
      <div style="font-weight:700;font-size:0.9rem;line-height:1.3">{{ $res->title }}</div>
      @if($res->description)
      <div style="font-size:0.78rem;color:var(--text-3);line-height:1.4">{{ Str::limit($res->description, 70) }}</div>
      @endif
      <div style="font-size:0.72rem;color:var(--text-3)">
        {{ $res->file_name }} · {{ $size }}
      </div>
      <div style="font-size:0.72rem;color:var(--text-3)">
        by {{ $res->uploader->full_name }} · {{ $res->created_at->diffForHumans() }}
        @if($res->studyGroup) · 👥 {{ $res->studyGroup->name }} @endif
        @if($res->is_public) · <span style="color:#16a34a">Public</span> @endif
      </div>
      <div style="display:flex;gap:8px;margin-top:auto;padding-top:4px">
        <a href="{{ $url }}" download="{{ $res->file_name }}" class="btn btn-sm btn-primary" style="flex:1;text-align:center">Download</a>
        @if($res->uploader_id === auth()->id() || auth()->user()->isAdmin())
        <form method="POST" action="{{ route('resources.destroy', $res) }}" data-confirm="Delete this resource? This cannot be undone.">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-sm" style="background:var(--surface-2);color:#dc2626;border:1px solid #dc2626">Delete</button>
        </form>
        @endif
      </div>
    </div>
    @endforeach
  </div>
  @else
  <div style="text-align:center;padding:64px;color:var(--text-3)">
    <p>No resources yet. Upload the first one!</p>
  </div>
  @endif
</div>

{{-- Upload Modal --}}
<div id="uploadModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center" onclick="if(event.target===this)this.style.display='none'">
  <div style="background:var(--surface);border-radius:20px;padding:28px;width:90%;max-width:480px;box-shadow:0 8px 32px rgba(0,0,0,0.2)">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
      <h2 style="font-family:'Sora',sans-serif;font-size:1.1rem;font-weight:800;margin:0">Upload Resource</h2>
      <button onclick="document.getElementById('uploadModal').style.display='none'" style="background:none;border:none;cursor:pointer;font-size:1.2rem;color:var(--text-3)">✕</button>
    </div>
    <form method="POST" action="{{ route('resources.store') }}" enctype="multipart/form-data">
      @csrf
      <div style="display:flex;flex-direction:column;gap:14px">
        <div>
          <label class="form-label">Title</label>
          <input type="text" name="title" class="form-input" placeholder="e.g. DSA Lecture Notes Week 3" required>
        </div>
        <div>
          <label class="form-label">Description <span style="color:var(--text-3);font-weight:400">(optional)</span></label>
          <textarea name="description" class="form-input" rows="2" placeholder="Brief description of the file"></textarea>
        </div>
        <div>
          <label class="form-label">File <span style="color:var(--text-3);font-weight:400">(max 50MB)</span></label>
          <input type="file" name="file" class="form-input" style="padding:8px" required>
        </div>
        <div style="display:flex;align-items:center;gap:8px">
          <input type="checkbox" name="is_public" id="isPublic" value="1" checked style="width:16px;height:16px">
          <label for="isPublic" style="font-size:0.88rem;cursor:pointer">Make publicly visible to all users</label>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top:4px">Upload</button>
      </div>
    </form>
  </div>
</div>

@if($errors->any())
<script>document.getElementById('uploadModal').style.display='flex';</script>
@endif
@endsection
