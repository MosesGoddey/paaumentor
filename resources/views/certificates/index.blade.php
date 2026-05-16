@extends('layouts.sidebar')
@section('title', 'My Certificates')

@push('scripts')
<style>
.star-rating { display:flex; flex-direction:row-reverse; gap:2px; width:fit-content; }
.star-rating label { font-size:1.6rem; color:#d1d5db; cursor:pointer; transition:color 0.1s; }
.star-rating input:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label { color:#f59e0b; }
</style>
@endpush

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
          @if($cert->type === 'hackathon')
            @php
              $hTeam  = $cert->hackathonTeam;
              $hPlc   = $cert->placement ?? 'participant';
              $hBg    = match($hPlc) { '1st'=>'#fef3c7','2nd'=>'#f3f4f6','3rd'=>'#fde8d8', default=>'#dbeafe' };
              $hClr   = match($hPlc) { '1st'=>'#92400e','2nd'=>'#374151','3rd'=>'#78350f', default=>'#1d4ed8' };
              $hIcon  = match($hPlc) { '1st'=>'','2nd'=>'','3rd'=>'', default=>'' };
            @endphp
            <div style="font-weight:800;font-size:1rem;margin-bottom:4px">{{ $hTeam->hackathon->title }}</div>
            <div style="font-size:0.8rem;color:var(--text-3);margin-bottom:6px">
              <span style="background:{{ $hBg }};color:{{ $hClr }};border-radius:4px;padding:1px 7px;font-size:0.7rem;font-weight:700;margin-right:6px">{{ $hIcon }} HACKATHON</span>
              Team: <strong>{{ $hTeam->name }}</strong> ·
              @if($hPlc === 'participant') Participant @else {{ $hPlc }} Place @endif
            </div>
          @else
            <div style="font-weight:800;font-size:1rem;margin-bottom:4px">{{ $cert->learningPath->title }}</div>
            <div style="font-size:0.8rem;color:var(--text-3);margin-bottom:6px">
              @if($cert->type === 'mentor')
                @php
                  $certTier  = $cert->user->mentor_tier;
                  $certLabel = match($certTier) { 'lead' => 'LEAD MENTOR', 'senior' => 'SENIOR MENTOR', default => 'JUNIOR MENTOR' };
                  $certBg    = match($certTier) { 'lead' => '#fef3c7', 'senior' => '#ede9fe', default => '#d1fae5' };
                  $certClr   = match($certTier) { 'lead' => '#92400e', 'senior' => '#5b21b6', default => '#065f46' };
                @endphp
                <span style="background:{{ $certBg }};color:{{ $certClr }};border-radius:4px;padding:1px 7px;font-size:0.7rem;font-weight:700;margin-right:6px">{{ $certLabel }}</span>
                Mentee: <a href="{{ route('profile.show', $cert->learningPath->mentee) }}" style="color:var(--text);font-weight:700;text-decoration:none" onmouseover="this.style.color='var(--blue-500)'" onmouseout="this.style.color='var(--text)'">{{ $cert->learningPath->mentee->full_name }}</a>
              @else
                <span style="background:#d1fae5;color:#065f46;border-radius:4px;padding:1px 7px;font-size:0.7rem;font-weight:700;margin-right:6px">MENTEE</span>
                Mentored by <a href="{{ route('profile.show', $cert->learningPath->mentor) }}" style="color:var(--text);font-weight:700;text-decoration:none" onmouseover="this.style.color='var(--blue-500)'" onmouseout="this.style.color='var(--text)'">{{ $cert->learningPath->mentor->full_name }}</a>
              @endif
            </div>
          @endif
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

      {{-- Mentor rating prompt (mentee certs only) --}}
      @if($cert->type === 'mentee')
      @php $alreadyRated = isset($ratedMentorIds[$cert->learningPath->mentor_id]); @endphp
      <div style="border-top:1px solid var(--border);padding:14px 24px 16px 30px;background:var(--surface-2)">
        @if($alreadyRated)
          <div style="font-size:0.8rem;color:var(--text-3);display:flex;align-items:center;gap:8px">
            <span style="color:#f59e0b;font-size:1rem"></span>
            You rated <strong>{{ $cert->learningPath->mentor->full_name }}</strong> — thank you!
          </div>
        @else
          <div style="font-size:0.82rem;font-weight:700;margin-bottom:8px;color:var(--text)">
            Rate your mentor — <span style="font-weight:500;color:var(--text-3)">{{ $cert->learningPath->mentor->full_name }}</span>
          </div>
          <form method="POST" action="{{ route('certificates.rateMentor', $cert) }}" style="display:flex;flex-direction:column;gap:10px">
            @csrf
            <div class="star-rating" data-cert="{{ $cert->id }}">
              @for($s = 5; $s >= 1; $s--)
              <input type="radio" name="score" id="star-{{ $cert->id }}-{{ $s }}" value="{{ $s }}" style="display:none">
              <label for="star-{{ $cert->id }}-{{ $s }}" style="font-size:1.5rem;color:#d1d5db;cursor:pointer;transition:color 0.1s"></label>
              @endfor
            </div>
            <textarea name="review" placeholder="Share your experience with this mentor (optional)…"
                      style="width:100%;max-width:500px;padding:8px 12px;border:1px solid var(--border);border-radius:10px;font-size:0.82rem;background:var(--surface);color:var(--text);resize:vertical;min-height:60px;font-family:inherit"></textarea>
            <div>
              <button type="submit" class="btn btn-sm btn-primary">Submit Rating</button>
            </div>
          </form>
        @endif
      </div>
      @endif

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
        Mentor certificates are awarded as a milestone — earn one for every 3 mentees you successfully guide to completion.
      @else
        Complete all tasks in a learning path assigned by your mentor to earn your first certificate.
      @endif
    </p>
    <a href="{{ route('learning.index') }}" class="btn btn-primary">View Learning Paths</a>
  </div>
  @endif

</div>
@endsection
