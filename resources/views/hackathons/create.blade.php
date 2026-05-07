@extends('layouts.sidebar')
@section('title', 'Create Hackathon')

@section('breadcrumbs')
<span>/</span>
<a href="{{ route('hackathons.index') }}" style="color:var(--text-3);text-decoration:none">Hackathons</a>
<span>/</span><span>Create</span>
@endsection

@section('page-content')
<div style="max-width:720px">
  <h1 class="section-title" style="margin-bottom:6px">Create Hackathon</h1>
  <p class="section-sub" style="margin-bottom:24px">Fill in the details. You can save as draft and publish later.</p>

  @if($errors->any())
  <div class="alert alert-error" style="margin-bottom:16px">
    <ul style="margin:0;padding-left:16px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
  @endif

  <form method="POST" action="{{ route('hackathons.store') }}">
    @csrf

    <div class="card" style="margin-bottom:16px">
      <div style="font-family:'Sora',sans-serif;font-weight:700;margin-bottom:16px">Basic Info</div>
      <div class="form-group">
        <label class="form-label">Title <span style="color:#e11d48">*</span></label>
        <input type="text" name="title" class="form-input" value="{{ old('title') }}" placeholder="e.g. PAAU TechFest Hackathon 2026" required>
      </div>
      <div class="form-group">
        <label class="form-label">Theme</label>
        <input type="text" name="theme" class="form-input" value="{{ old('theme') }}" placeholder="e.g. Technology for Sustainable Development">
      </div>
      <div class="form-group">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-input" rows="4" placeholder="Describe what this hackathon is about…">{{ old('description') }}</textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Tracks <span style="font-weight:400;color:var(--text-3)">(comma-separated, e.g. AI, FinTech, AgriTech)</span></label>
        <input type="text" name="tracks" class="form-input" value="{{ old('tracks') }}" placeholder="AI, FinTech, AgriTech, Health">
      </div>
    </div>

    <div class="card" style="margin-bottom:16px">
      <div style="font-family:'Sora',sans-serif;font-weight:700;margin-bottom:16px">Schedule</div>
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px">
        <div class="form-group">
          <label class="form-label">Registration Deadline</label>
          <input type="date" name="registration_deadline" class="form-input" value="{{ old('registration_deadline') }}">
        </div>
        <div class="form-group">
          <label class="form-label">Start Date</label>
          <input type="date" name="start_date" class="form-input" value="{{ old('start_date') }}">
        </div>
        <div class="form-group">
          <label class="form-label">End Date</label>
          <input type="date" name="end_date" class="form-input" value="{{ old('end_date') }}">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Max Team Size <span style="color:#e11d48">*</span></label>
        <select name="max_team_size" class="form-select" style="width:auto">
          @foreach([2,3,4,5,6] as $n)
          <option value="{{ $n }}" {{ old('max_team_size', 4) == $n ? 'selected' : '' }}>{{ $n }} members</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="card" style="margin-bottom:16px">
      <div style="font-family:'Sora',sans-serif;font-weight:700;margin-bottom:16px">Rules & Prizes</div>
      <div class="form-group">
        <label class="form-label">Rules & Guidelines</label>
        <textarea name="rules" class="form-input" rows="6" placeholder="- Teams must consist of PAAU students&#10;- All projects must be original&#10;- Plagiarism leads to disqualification&#10;…">{{ old('rules') }}</textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Prizes</label>
        <input type="text" name="prizes" class="form-input" value="{{ old('prizes') }}" placeholder="e.g. 1st: ₦150,000 · 2nd: ₦75,000 · 3rd: ₦30,000 + certificates for all">
      </div>
    </div>

    <div style="display:flex;gap:10px">
      <button type="submit" class="btn btn-primary">Create Hackathon (Draft)</button>
      <a href="{{ route('hackathons.index') }}" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
@endsection
