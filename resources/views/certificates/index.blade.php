@extends('layouts.sidebar')
@section('title', 'My Certificates')

@section('page-content')
<div style="max-width:900px;margin:0 auto">

  <div style="margin-bottom:28px">
    <h1 style="font-family:'Sora',sans-serif;font-size:1.4rem;font-weight:800;margin:0">My Certificates</h1>
    <p style="color:var(--text-3);font-size:0.85rem;margin:4px 0 0">Certificates earned by completing mentorship learning paths</p>
  </div>

  @if($certificates->isNotEmpty())
  <div style="display:flex;flex-direction:column;gap:16px">
    @foreach($certificates as $cert)
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:20px;overflow:hidden;display:flex">

      {{-- Gold accent bar --}}
      <div style="width:6px;background:linear-gradient(180deg,#c9a227,#f5c842);flex-shrink:0"></div>

      <div style="flex:1;padding:20px 24px;display:flex;align-items:center;gap:24px;flex-wrap:wrap">

        {{-- Icon --}}
        <div style="width:56px;height:56px;border-radius:14px;background:linear-gradient(135deg,#c9a227,#f5c842);display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/>
          </svg>
        </div>

        {{-- Info --}}
        <div style="flex:1;min-width:0">
          <div style="font-weight:800;font-size:1rem;margin-bottom:4px">{{ $cert->learningPath->title }}</div>
          <div style="font-size:0.8rem;color:var(--text-3);margin-bottom:6px">
            @if($cert->type === 'mentor')
              <span style="background:#fef3c7;color:#92400e;border-radius:4px;padding:1px 7px;font-size:0.7rem;font-weight:700;margin-right:6px">MENTOR</span>
              Mentee: <strong style="color:var(--text)">{{ $cert->learningPath->mentee->full_name }}</strong>
            @else
              <span style="background:#d1fae5;color:#065f46;border-radius:4px;padding:1px 7px;font-size:0.7rem;font-weight:700;margin-right:6px">MENTEE</span>
              Mentored by <strong style="color:var(--text)">{{ $cert->learningPath->mentor->full_name }}</strong>
            @endif
          </div>
          <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
            <span style="background:var(--surface-2);border-radius:6px;padding:3px 10px;font-size:0.72rem;font-weight:700;font-family:'Sora',sans-serif;letter-spacing:0.04em">
              {{ $cert->certificate_id }}
            </span>
            <span style="font-size:0.78rem;color:var(--text-3)">
              Issued {{ $cert->issued_at->format('F j, Y') }}
            </span>
          </div>
        </div>

        {{-- Actions --}}
        <div style="display:flex;flex-direction:column;gap:8px;flex-shrink:0">
          <a href="{{ route('certificates.download', $cert) }}"
             class="btn btn-primary btn-sm" style="text-align:center;white-space:nowrap">
             Download PDF
          </a>
          <a href="{{ route('certificates.verify', $cert->certificate_id) }}"
             target="_blank"
             style="font-size:0.78rem;color:var(--blue-500);text-align:center;text-decoration:none">
             Verify Certificate
          </a>
        </div>
      </div>
    </div>
    @endforeach
  </div>

  @else
  <div style="background:var(--surface);border:1px solid var(--border);border-radius:20px;padding:64px;text-align:center;color:var(--text-3)">
    <div style="width:64px;height:64px;border-radius:16px;background:var(--surface-2);display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
      <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/>
      </svg>
    </div>
    <div style="font-weight:700;font-size:1rem;margin-bottom:8px;color:var(--text)">No certificates yet</div>
    <p style="font-size:0.88rem;max-width:360px;margin:0 auto 20px">
      @if($user->isMentor())
        Certificates are issued automatically when a mentee completes all tasks in your learning path.
      @else
        Complete all tasks in a learning path assigned by your mentor to earn your first certificate.
      @endif
    </p>
    <a href="{{ route('learning.index') }}" class="btn btn-primary">View Learning Paths</a>
  </div>
  @endif

</div>
@endsection
