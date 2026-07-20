@extends('layouts.sidebar')
@section('title', 'Verifier Panel')
@section('breadcrumbs')<span style="opacity:0.5">›</span> <span>Verifier Panel</span>@endsection

@section('page-content')
@php $isAdminViewer = auth()->user()->isAdmin(); @endphp
<div style="margin-bottom:28px">
  <h1 style="font-family:'Sora',sans-serif;font-size:1.5rem;font-weight:800;margin:0">{{ $isAdminViewer ? 'Verification Panel' : 'Certificate Verification' }}</h1>
  <p style="color:var(--text-3);font-size:0.88rem;margin:6px 0 0">
    {{ $isAdminViewer
        ? 'Review mentor portfolios, approve certificates, and manage upgrade requests.'
        : 'Review and issue certificates for mentees who have completed their learning paths.' }}
  </p>
</div>

{{--  Pending direct-registration mentors — ADMIN ONLY  --}}
@if($isAdminViewer)
<div class="card" style="margin-bottom:24px">
  <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1rem;margin-bottom:18px;display:flex;align-items:center;gap:10px">
    Pending Mentor Portfolios
    <span style="background:var(--blue-500);color:#fff;border-radius:999px;padding:2px 10px;font-size:0.75rem;font-weight:700">{{ $pendingMentors->count() }}</span>
  </div>

  @forelse($pendingMentors as $mentor)
  <div style="border:1px solid var(--border);border-radius:16px;padding:20px;margin-bottom:14px">
    <div style="display:flex;align-items:flex-start;gap:16px;flex-wrap:wrap">

      {{-- Avatar + identity --}}
      <div style="display:flex;align-items:center;gap:12px;flex:1;min-width:220px">
        @if($mentor->avatar_url)
          <img src="{{ $mentor->avatar_url }}" alt="" style="width:48px;height:48px;border-radius:50%;object-fit:cover;flex-shrink:0">
        @else
          <div class="avatar avatar-md" style="flex-shrink:0;background:linear-gradient(135deg,#f97316,#c2410c)">{{ $mentor->initials }}</div>
        @endif
        <div>
          <div style="font-weight:700;font-size:0.95rem">{{ $mentor->full_name }}</div>
          <div style="font-size:0.78rem;color:var(--text-3)">{{ $mentor->level }} · {{ $mentor->department }}</div>
          <div style="font-size:0.76rem;color:var(--text-3)">ID: {{ $mentor->student_id }} · Joined {{ $mentor->created_at->diffForHumans() }}</div>
        </div>
      </div>

      {{-- Portfolio links --}}
      <div style="flex:2;min-width:260px">
        <div style="font-size:0.78rem;font-weight:700;color:var(--text-2);margin-bottom:8px;text-transform:uppercase;letter-spacing:0.06em">Portfolio</div>
        <div style="display:flex;flex-direction:column;gap:6px">
          @if($mentor->github_url)
          <a href="{{ $mentor->github_url }}" target="_blank" rel="noopener"
             style="display:inline-flex;align-items:center;gap:6px;font-size:0.83rem;color:#2563eb;text-decoration:none;font-weight:600">
            <span style="font-size:1rem"></span> GitHub Profile 
          </a>
          @else
          <span style="font-size:0.82rem;color:var(--text-3)"> No GitHub provided</span>
          @endif

          @if($mentor->linkedin_url)
          <a href="{{ $mentor->linkedin_url }}" target="_blank" rel="noopener"
             style="display:inline-flex;align-items:center;gap:6px;font-size:0.83rem;color:#0a66c2;text-decoration:none;font-weight:600">
            <span style="font-size:1rem"></span> LinkedIn Profile 
          </a>
          @else
          <span style="font-size:0.82rem;color:var(--text-3)"> No LinkedIn provided</span>
          @endif
        </div>

        @if($mentor->bio)
        <div style="margin-top:10px;padding:10px 12px;background:var(--surface-2);border-radius:10px;font-size:0.82rem;color:var(--text-2);line-height:1.6">
          <span style="font-weight:700;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-3);display:block;margin-bottom:4px">Portfolio Bio</span>
          {{ $mentor->bio }}
        </div>
        @endif
      </div>

      {{-- Actions --}}
      <div style="display:flex;flex-direction:column;gap:8px;flex-shrink:0">
        <form method="POST" action="{{ route('verifier.approve', ['user' => $mentor]) }}">
          @csrf
          <button type="submit" class="btn btn-primary btn-sm" style="width:100%"> Approve</button>
        </form>

        <button type="button" class="btn btn-outline btn-sm" style="width:100%;color:#dc2626;border-color:#dc2626"
                onclick="document.getElementById('reject-{{ $mentor->id }}').style.display='block';this.style.display='none'">
           Reject
        </button>

        <div id="reject-{{ $mentor->id }}" style="display:none">
          <form method="POST" action="{{ route('verifier.reject', ['user' => $mentor]) }}" style="display:flex;flex-direction:column;gap:6px">
            @csrf
            <textarea name="reason" class="form-input" rows="2" placeholder="Reason for rejection..." required
                      style="font-size:0.82rem;min-width:200px;resize:vertical"></textarea>
            <button type="submit" class="btn btn-sm" style="background:#dc2626;color:#fff;width:100%">Send Rejection</button>
          </form>
        </div>
      </div>

    </div>
  </div>

  @empty
  <div style="text-align:center;padding:48px 20px">
    <div style="font-size:3rem;margin-bottom:12px"></div>
    <div style="font-weight:700;font-size:0.95rem;margin-bottom:6px">No pending mentor portfolios</div>
    <div style="font-size:0.83rem;color:var(--text-3)">All mentor registrations have been reviewed.</div>
  </div>
  @endforelse
</div>
@endif

{{--  Certificate Requests Pending Verifier Review  --}}
<div class="card" style="margin-bottom:24px">
  <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1rem;margin-bottom:18px;display:flex;align-items:center;gap:10px">
    Pending Certificate Approvals
    <span style="background:var(--gold,#f59e0b);color:#fff;border-radius:999px;padding:2px 10px;font-size:0.75rem;font-weight:700">{{ $pendingCertRequests->count() }}</span>
  </div>

  @forelse($pendingCertRequests as $cr)
  @php
    $crMentorship = \App\Models\Mentorship::where('mentor_id', $cr->mentor_id)
        ->where('mentee_id', $cr->mentee_id)->first();
    $crSessions = $crMentorship
        ? \App\Models\MentorSession::where('mentorship_id', $crMentorship->id)->where('status','completed')->count()
        : 0;
  @endphp
  <div style="border:1px solid var(--border);border-radius:16px;padding:20px;margin-bottom:14px">
    <div style="display:flex;align-items:flex-start;gap:16px;flex-wrap:wrap">

      {{-- Mentee info --}}
      <div style="flex:1;min-width:200px">
        <div style="font-weight:700;font-size:0.95rem;margin-bottom:2px">{{ $cr->mentee->full_name }}</div>
        <div style="font-size:0.78rem;color:var(--text-3)">{{ $cr->mentee->level }} · {{ $cr->mentee->department }}</div>
        <div style="font-size:0.78rem;color:var(--text-3);margin-top:2px">Mentor: {{ $cr->mentor->full_name }}</div>
      </div>

      {{-- Learning path + score + session gate --}}
      <div style="flex:2;min-width:260px">
        <div style="font-weight:600;font-size:0.88rem;margin-bottom:8px"> {{ $cr->learningPath->title }}</div>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:8px">
          <span style="padding:4px 12px;border-radius:8px;font-size:0.8rem;font-weight:700;background:{{ $cr->assessment_score >= 80 ? '#dcfce7' : ($cr->assessment_score >= 70 ? '#fef9c3' : '#fee2e2') }};color:{{ $cr->assessment_score >= 80 ? '#166534' : ($cr->assessment_score >= 70 ? '#854d0e' : '#991b1b') }}">
             Assessment: {{ $cr->assessment_score }}%
          </span>
          <span style="padding:4px 12px;border-radius:8px;font-size:0.8rem;font-weight:700;background:{{ $crSessions >= 3 ? '#dcfce7' : '#fee2e2' }};color:{{ $crSessions >= 3 ? '#166534' : '#991b1b' }}">
             Sessions: {{ $crSessions }}/3
          </span>
          @if($cr->hasReflection())
          <span style="padding:4px 10px;border-radius:8px;font-size:0.78rem;font-weight:700;background:#f0fdf4;color:#166534;border:1px solid #86efac">
             Reflection submitted
          </span>
          @else
          <span style="padding:4px 10px;border-radius:8px;font-size:0.78rem;font-weight:700;background:#fef9c3;color:#854d0e;border:1px solid #fde047">
             Awaiting reflection
          </span>
          @endif
        </div>

        {{-- Mentor reflection text --}}
        @if($cr->hasReflection())
        <div style="margin-bottom:10px">
          <button type="button" onclick="this.nextElementSibling.style.display=this.nextElementSibling.style.display==='none'?'block':'none'"
                  style="font-size:0.78rem;color:var(--blue-500);background:none;border:none;cursor:pointer;padding:0;font-weight:600">
             Read mentor reflection 
          </button>
          <div style="display:none;margin-top:8px;padding:12px 14px;background:var(--surface-2);border-radius:10px;border-left:3px solid var(--blue-500)">
            <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-3);margin-bottom:6px">
              Mentor Reflection · {{ $cr->mentor_reflection_submitted_at?->format('M d, Y') }}
            </div>
            <div style="font-size:0.82rem;color:var(--text-2);line-height:1.7;white-space:pre-wrap">{{ $cr->mentor_reflection }}</div>
          </div>
        </div>
        @endif

        <a href="{{ route('learning.show', $cr->learningPath) }}" target="_blank"
           style="font-size:0.8rem;color:var(--blue-500);text-decoration:none;font-weight:600">
          View Learning Path & Submissions 
        </a>

        {{-- Gate warnings --}}
        @if(!$cr->hasReflection() || $crSessions < 3)
        <div style="margin-top:10px;padding:8px 12px;background:#fef9c3;border:1px solid #fde047;border-radius:8px;font-size:0.78rem;color:#854d0e">
           Cannot approve yet:
          @if(!$cr->hasReflection()) <span>mentor reflection not submitted</span> @endif
          @if(!$cr->hasReflection() && $crSessions < 3) <span> · </span> @endif
          @if($crSessions < 3) <span>only {{ $crSessions }}/3 sessions completed</span> @endif
        </div>
        @endif
      </div>

      {{-- Actions --}}
      <div style="display:flex;flex-direction:column;gap:8px;flex-shrink:0;min-width:180px">
        <form method="POST" action="{{ route('verifier.cert.approve', $cr) }}" style="display:flex;flex-direction:column;gap:6px">
          @csrf
          <input type="text" name="verifier_note" class="form-input" placeholder="Approval note (optional)" style="font-size:0.8rem">
          <button type="submit" class="btn btn-primary btn-sm" style="width:100%"
                  @if(!$cr->hasReflection() || $crSessions < 3) title="Gates not met — server will block this" @endif>
             Issue Certificate
          </button>
        </form>

        <button type="button" class="btn btn-outline btn-sm" style="width:100%;color:#dc2626;border-color:#dc2626"
                onclick="document.getElementById('cert-reject-{{ $cr->id }}').style.display='block';this.style.display='none'">
           Reject
        </button>
        <div id="cert-reject-{{ $cr->id }}" style="display:none">
          <form method="POST" action="{{ route('verifier.cert.reject', $cr) }}" style="display:flex;flex-direction:column;gap:6px">
            @csrf
            <textarea name="verifier_note" class="form-input" rows="2" placeholder="Reason for rejection (required)..." required style="font-size:0.8rem;resize:vertical"></textarea>
            <button type="submit" class="btn btn-sm" style="background:#dc2626;color:#fff;width:100%">Send Rejection</button>
          </form>
        </div>
      </div>

    </div>
  </div>

  @empty
  <div style="text-align:center;padding:36px 20px">
    <div style="font-size:3rem;margin-bottom:12px"></div>
    <div style="font-weight:700;font-size:0.88rem;margin-bottom:6px">No pending certificate requests</div>
    <div style="font-size:0.82rem;color:var(--text-3)">Certificate requests will appear here once mentees pass their assessments.</div>
  </div>
  @endforelse
</div>

{{--  Upgrade requests (redirect to existing admin view) — ADMIN ONLY  --}}
@if($isAdminViewer)
<div class="card">
  <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1rem;margin-bottom:8px">Mentee → Mentor Upgrade Requests</div>
  <p style="font-size:0.85rem;color:var(--text-3);margin:0 0 14px">Review mentees who have been recommended by their mentor for promotion.</p>
  <a href="{{ route('upgrade.admin') }}" class="btn btn-outline btn-sm">Open Upgrade Requests</a>
</div>
@endif
@endsection
