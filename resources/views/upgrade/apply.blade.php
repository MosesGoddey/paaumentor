@extends('layouts.sidebar')
@section('title', 'Upgrade to Mentor')
@section('breadcrumbs')<span style="opacity:0.5">›</span> My Account <span style="opacity:0.5">›</span> <a href="{{ route('upgrade.show') }}" style="color:var(--blue-500);text-decoration:none">Upgrade to Mentor</a>@endsection

@section('page-content')
<div style="max-width:680px;margin:0 auto">

  <div style="margin-bottom:28px">
    <h1 style="font-family:'Sora',sans-serif;font-size:1.5rem;font-weight:800;margin:0">Upgrade to Mentor</h1>
    <p style="color:var(--text-3);font-size:0.88rem;margin:6px 0 0">Meet all the requirements below, then submit your application for your mentor's recommendation.</p>
  </div>

  {{-- Already has an active request --}}
  @if($existing)
  <div style="background:#fef9c3;border:1px solid #fde68a;border-radius:16px;padding:20px 24px;margin-bottom:24px">
    <div style="font-weight:700;font-size:0.95rem;color:#854d0e;margin-bottom:4px">⏳ Application In Progress</div>
    <p style="font-size:0.85rem;color:#92400e;margin:0 0 10px">
      @if($existing->isPendingAssessment())
        You need to complete the knowledge assessment before your mentor is notified.
      @elseif($existing->isPending())
        Assessment passed! Waiting for your mentor <strong>{{ $existing->mentor->full_name }}</strong> to submit a recommendation.
      @else
        Your mentor has submitted a recommendation. An admin is now reviewing your application.
      @endif
    </p>
    @if($existing->isPendingAssessment())
    <a href="{{ route('upgrade-assessment.show', $existing) }}" class="btn btn-sm btn-primary">
      Go to Assessment →
    </a>
    @endif
  </div>
  @endif

  {{-- Requirements checklist --}}
  <div style="background:var(--surface);border:1px solid var(--border);border-radius:20px;padding:24px;margin-bottom:24px">
    <h2 style="font-family:'Sora',sans-serif;font-size:1rem;font-weight:800;margin:0 0 18px">Requirements Checklist</h2>
    <div style="display:flex;flex-direction:column;gap:14px">
      @foreach($requirements as $key => $req)
      <div style="display:flex;align-items:center;gap:14px;padding:14px 16px;border-radius:12px;background:{{ $req['met'] ? '#f0fdf4' : 'var(--surface-2)' }};border:1px solid {{ $req['met'] ? '#86efac' : 'var(--border)' }}">
        <div style="width:32px;height:32px;border-radius:50%;background:{{ $req['met'] ? '#16a34a' : 'var(--border)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:0.9rem;color:#fff;font-weight:700">
          {{ $req['met'] ? '✓' : '✗' }}
        </div>
        <div style="flex:1">
          <div style="font-weight:600;font-size:0.88rem;color:{{ $req['met'] ? '#166534' : 'var(--text)' }}">{{ $req['label'] }}</div>
          <div style="font-size:0.78rem;color:var(--text-3);margin-top:2px">
            @if($key === 'account_age')
              {{ $req['current'] }} / {{ $req['target'] }} days
            @elseif($key === 'mentor')
              {{ $req['met'] ? 'Active mentor: '.$activeMentor->mentor->full_name : 'No active mentor found' }}
            @elseif($key === 'certificates')
              {{ $req['current'] }} / {{ $req['target'] }} certificate{{ $req['current'] === 1 ? '' : 's' }} earned
            @else
              {{ $req['current'] }} / {{ $req['target'] }} completed
            @endif
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>

  {{-- Apply button --}}
  @if(!$existing)
    @if($allMet)
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:20px;padding:24px">
      <div style="font-weight:700;font-size:0.95rem;margin-bottom:6px">✅ You meet all requirements!</div>
      <p style="font-size:0.85rem;color:var(--text-3);margin:0 0 18px">
        Submitting this application will notify your mentor <strong>{{ $activeMentor?->mentor->full_name }}</strong> to write a recommendation for you.
      </p>
      {{-- Portfolio fields (reviewed by verifier) --}}
      <div style="margin-bottom:20px;padding:16px;background:#fff7ed;border-radius:14px;border:1.5px solid #fed7aa">
        <div style="font-size:0.85rem;font-weight:700;color:#c2410c;margin-bottom:14px">📋 Mentor Portfolio <span style="font-weight:500;color:var(--text-3)">(reviewed by a verifier before your mentor account goes live)</span></div>
        <div style="display:flex;flex-direction:column;gap:12px">
          <div class="form-group" style="margin:0">
            <label class="form-label">GitHub Profile URL</label>
            <input type="url" name="github_url" form="upgradeForm" class="form-input" placeholder="https://github.com/yourusername" value="{{ auth()->user()->github_url ?? old('github_url') }}">
          </div>
          <div class="form-group" style="margin:0">
            <label class="form-label">LinkedIn Profile URL</label>
            <input type="url" name="linkedin_url" form="upgradeForm" class="form-input" placeholder="https://linkedin.com/in/yourprofile" value="{{ auth()->user()->linkedin_url ?? old('linkedin_url') }}">
          </div>
          <div class="form-group" style="margin:0">
            <label class="form-label">What can you teach? <span style="font-weight:400;color:var(--text-3)">(skills, projects, achievements)</span></label>
            <textarea name="portfolio_bio" form="upgradeForm" class="form-input" rows="3" placeholder="Describe what you can mentor on, projects you've completed, skills you have..." style="resize:vertical">{{ auth()->user()->bio ?? old('portfolio_bio') }}</textarea>
          </div>
        </div>
      </div>

      <form id="upgradeForm" method="POST" action="{{ route('upgrade.apply') }}" data-confirm="Submit your upgrade application? Your mentor will be notified to write a recommendation.">
        @csrf
        <button type="submit" class="btn btn-primary">Submit Application</button>
      </form>
    </div>
    @else
    <div style="background:var(--surface-2);border:1px solid var(--border);border-radius:16px;padding:18px 20px;font-size:0.85rem;color:var(--text-3)">
      Complete all the requirements above before you can apply.
    </div>
    @endif
  @endif

</div>
@endsection
