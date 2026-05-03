@extends('layouts.sidebar')
@section('title', 'Post Skill Exchange')

@section('page-content')
<div style="max-width:560px;margin:0 auto">

  <div style="margin-bottom:24px">
    <a href="{{ route('skill-exchange.index') }}" style="font-size:0.82rem;color:var(--blue-500);text-decoration:none">← Back to listings</a>
    <h1 style="font-family:'Sora',sans-serif;font-size:1.4rem;font-weight:800;margin:8px 0 4px">Post a Skill Exchange</h1>
    <p style="color:var(--text-3);font-size:0.85rem;margin:0">Tell the community what you can teach and what you want to learn.</p>
  </div>

  <div class="card">
    <form method="POST" action="{{ route('skill-exchange.store') }}">
      @csrf

      <div style="margin-bottom:20px">
        <label style="font-weight:700;font-size:0.88rem;display:block;margin-bottom:6px">
          I am offering <span style="color:#dc2626">*</span>
        </label>
        <input type="text" name="offering" value="{{ old('offering') }}" required maxlength="100"
               class="form-input @error('offering') border-red-500 @enderror"
               placeholder="e.g. Graphic Design, Python, Public Speaking…">
        @error('offering')
        <div style="color:#dc2626;font-size:0.8rem;margin-top:4px">{{ $message }}</div>
        @enderror
        <div style="font-size:0.75rem;color:var(--text-3);margin-top:4px">The skill or knowledge you're willing to teach.</div>
      </div>

      <div style="margin-bottom:20px">
        <label style="font-weight:700;font-size:0.88rem;display:block;margin-bottom:6px">
          I want to learn <span style="color:#dc2626">*</span>
        </label>
        <input type="text" name="seeking" value="{{ old('seeking') }}" required maxlength="100"
               class="form-input @error('seeking') border-red-500 @enderror"
               placeholder="e.g. UI/UX Design, Web Development, Photography…">
        @error('seeking')
        <div style="color:#dc2626;font-size:0.8rem;margin-top:4px">{{ $message }}</div>
        @enderror
        <div style="font-size:0.75rem;color:var(--text-3);margin-top:4px">The skill you're looking to learn from someone else.</div>
      </div>

      <div style="margin-bottom:24px">
        <label style="font-weight:700;font-size:0.88rem;display:block;margin-bottom:6px">Description <span style="color:var(--text-3);font-weight:400">(optional)</span></label>
        <textarea name="description" rows="3" maxlength="500"
                  class="form-input" style="resize:vertical"
                  placeholder="Add details — your experience level, what topics you'd cover, your preferred learning style, availability, etc.">{{ old('description') }}</textarea>
        <div style="font-size:0.75rem;color:var(--text-3);margin-top:4px">Max 500 characters.</div>
      </div>

      {{-- Preview --}}
      <div style="background:var(--surface-2);border-radius:12px;padding:14px 16px;margin-bottom:20px;border:1px solid var(--border)">
        <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-3);margin-bottom:8px">Preview</div>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
          <span style="background:#d1fae5;color:#065f46;border-radius:6px;padding:3px 10px;font-size:0.75rem;font-weight:700">
            Offering: <span id="preview-offering">—</span>
          </span>
          <span style="color:var(--text-3);font-size:0.8rem">↔</span>
          <span style="background:#ede9fe;color:#5b21b6;border-radius:6px;padding:3px 10px;font-size:0.75rem;font-weight:700">
            Seeking: <span id="preview-seeking">—</span>
          </span>
        </div>
      </div>

      <div style="display:flex;gap:10px;justify-content:flex-end">
        <a href="{{ route('skill-exchange.index') }}" class="btn btn-sm" style="background:var(--surface-2);color:var(--text);border:1px solid var(--border)">Cancel</a>
        <button type="submit" class="btn btn-primary">Post Listing</button>
      </div>
    </form>
  </div>

</div>

@push('scripts')
<script>
const offeringInput = document.querySelector('input[name="offering"]');
const seekingInput  = document.querySelector('input[name="seeking"]');
const previewOff    = document.getElementById('preview-offering');
const previewSeek   = document.getElementById('preview-seeking');

offeringInput.addEventListener('input', () => {
  previewOff.textContent = offeringInput.value.trim() || '—';
});
seekingInput.addEventListener('input', () => {
  previewSeek.textContent = seekingInput.value.trim() || '—';
});

// Set initial values if old() is populated
if (offeringInput.value) previewOff.textContent = offeringInput.value;
if (seekingInput.value)  previewSeek.textContent = seekingInput.value;
</script>
@endpush
@endsection
