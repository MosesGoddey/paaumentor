@extends('layouts.sidebar')
@section('title', $myTeam->name . ' — Team Workspace')

@section('breadcrumbs')
<span>/</span>
<a href="{{ route('hackathons.index') }}" style="color:var(--text-3);text-decoration:none">Hackathons</a>
<span>/</span>
<a href="{{ route('hackathons.show', $hackathon) }}" style="color:var(--text-3);text-decoration:none">{{ $hackathon->title }}</a>
<span>/</span><span>Team Workspace</span>
@endsection

@section('page-content')
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif

<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start">

  {{-- Left: Submission --}}
  <div>
    <div class="card" style="margin-bottom:20px">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
        <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1.05rem">📦 Project Submission</div>
        @php $submission = $myTeam->submission; @endphp
        @if($submission)
          @if($submission->status === 'submitted')
          <span class="badge badge-green" style="font-size:0.78rem">✓ Submitted {{ $submission->submitted_at->format('M j, g:ia') }}</span>
          @else
          <span style="background:#fef3c7;color:#92400e;border-radius:6px;padding:3px 10px;font-size:0.72rem;font-weight:700">Draft</span>
          @endif
        @endif
      </div>

      @if($hackathon->status !== 'ongoing')
        @if($hackathon->status === 'open')
        <p style="font-size:0.88rem;color:var(--text-3)">Submissions open when the hackathon starts.</p>
        @elseif($submission && $submission->status === 'submitted')
        {{-- Show submitted project read-only --}}
        <div style="margin-bottom:10px"><div style="font-size:0.75rem;font-weight:700;color:var(--text-3);margin-bottom:3px">PROJECT TITLE</div><div style="font-weight:700">{{ $submission->title }}</div></div>
        <div style="margin-bottom:10px"><div style="font-size:0.75rem;font-weight:700;color:var(--text-3);margin-bottom:3px">DESCRIPTION</div><div style="font-size:0.88rem;color:var(--text-2);line-height:1.7">{{ $submission->description }}</div></div>
        @foreach(['github_url'=>'GitHub','demo_url'=>'Live Demo','deck_url'=>'Slides/Deck'] as $field=>$label)
          @if($submission->$field)<div style="margin-bottom:6px"><span style="font-size:0.78rem;font-weight:700;color:var(--text-3)">{{ $label }}:</span> <a href="{{ $submission->$field }}" target="_blank" style="color:var(--blue-500);font-size:0.85rem">{{ $submission->$field }}</a></div>@endif
        @endforeach
        @else
        <p style="font-size:0.88rem;color:var(--text-3)">No submission found for this hackathon.</p>
        @endif
      @else
        {{-- Submission form (team lead only) --}}
        @if($isLead)
        <form method="POST" action="{{ route('hackathons.submit', $hackathon) }}">
          @csrf
          <div class="form-group">
            <label class="form-label">Project Title <span style="color:#e11d48">*</span></label>
            <input type="text" name="title" class="form-input" value="{{ old('title', $submission->title ?? '') }}" placeholder="Give your project a name" required>
          </div>
          <div class="form-group">
            <label class="form-label">Description <span style="color:#e11d48">*</span></label>
            <textarea name="description" class="form-input" rows="5" placeholder="Explain what you built, the problem it solves, and how it works…" required>{{ old('description', $submission->description ?? '') }}</textarea>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div class="form-group">
              <label class="form-label">GitHub URL</label>
              <input type="url" name="github_url" class="form-input" value="{{ old('github_url', $submission->github_url ?? '') }}" placeholder="https://github.com/…">
            </div>
            <div class="form-group">
              <label class="form-label">Live Demo URL</label>
              <input type="url" name="demo_url" class="form-input" value="{{ old('demo_url', $submission->demo_url ?? '') }}" placeholder="https://…">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Slides / Deck URL</label>
            <input type="url" name="deck_url" class="form-input" value="{{ old('deck_url', $submission->deck_url ?? '') }}" placeholder="Google Slides, Canva, etc.">
          </div>
          <div style="display:flex;gap:10px;margin-top:4px">
            <button type="submit" name="action" value="save" class="btn btn-outline">Save Draft</button>
            <button type="submit" name="action" value="submit" class="btn btn-primary" onclick="return confirm('Submit your project? You can still update it while the hackathon is ongoing.')">Submit Project</button>
          </div>
        </form>
        @else
        <p style="font-size:0.88rem;color:var(--text-3)">Only the team lead can submit the project.</p>
        @if($submission)
        <div style="margin-top:10px"><div style="font-weight:700;margin-bottom:4px">{{ $submission->title }}</div><div style="font-size:0.85rem;color:var(--text-2)">{{ $submission->description }}</div></div>
        @endif
        @endif
      @endif
    </div>

    {{-- Scores (visible after judging) --}}
    @if($submission && $submission->scores->count() > 0)
    <div class="card">
      <div style="font-family:'Sora',sans-serif;font-weight:700;margin-bottom:14px">📊 Judge Scores</div>
      @php $summary = $submission->score_summary; @endphp
      <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:14px">
        @foreach(['innovation'=>'Innovation','execution'=>'Execution','impact'=>'Impact','presentation'=>'Presentation'] as $k=>$label)
        <div style="text-align:center;padding:12px;background:var(--surface-2);border-radius:10px">
          <div style="font-family:'Sora',sans-serif;font-weight:800;font-size:1.4rem;color:var(--blue-500)">{{ $summary[$k] }}</div>
          <div style="font-size:0.7rem;color:var(--text-3);margin-top:2px">{{ $label }}</div>
        </div>
        @endforeach
      </div>
      <div style="text-align:right;font-size:0.82rem;color:var(--text-3)">{{ $submission->scores->count() }} judge(s) scored this submission</div>
    </div>
    @endif
  </div>

  {{-- Right: Team Info --}}
  <div>
    {{-- Team card --}}
    <div class="card" style="margin-bottom:16px">
      <div style="font-family:'Sora',sans-serif;font-weight:700;margin-bottom:14px">Your Team</div>

      @if($isLead)
      <div style="background:var(--surface-2);border-radius:10px;padding:12px;margin-bottom:14px;text-align:center">
        <div style="font-size:0.72rem;font-weight:700;color:var(--text-3);margin-bottom:4px;text-transform:uppercase;letter-spacing:0.05em">Join Code</div>
        <div style="font-family:'Sora',sans-serif;font-size:1.6rem;font-weight:800;letter-spacing:8px;color:var(--blue-500)">{{ $myTeam->join_code }}</div>
        <div style="font-size:0.72rem;color:var(--text-3);margin-top:4px">Share with teammates</div>
      </div>
      @endif

      <div style="display:flex;flex-wrap:wrap;gap:4px;margin-bottom:10px">
        @if($myTeam->track)<span class="badge badge-blue">{{ $myTeam->track }}</span>@endif
        @if($myTeam->is_locked)<span class="badge" style="background:#fee2e2;color:#b91c1c">🔒 Locked</span>@endif
      </div>

      {{-- Members --}}
      <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:14px">
        @foreach($myTeam->users as $member)
        <div style="display:flex;align-items:center;gap:10px">
          <div class="avatar" style="width:32px;height:32px;font-size:0.78rem;flex-shrink:0">{{ $member->initials }}</div>
          <div style="flex:1;min-width:0">
            <div style="font-weight:700;font-size:0.88rem">{{ $member->full_name }}</div>
            <div style="font-size:0.72rem;color:var(--text-3)">{{ $member->level }} · {{ $member->department }}</div>
          </div>
          @if($member->pivot->is_lead)
          <span style="font-size:0.65rem;background:#dbeafe;color:#1d4ed8;border-radius:4px;padding:1px 6px;font-weight:700">Lead</span>
          @endif
        </div>
        @endforeach
      </div>

      {{-- Lock team (lead only, ongoing) --}}
      @if($isLead && $hackathon->status === 'ongoing' && !$myTeam->is_locked)
      <form method="POST" action="{{ route('hackathons.status', $hackathon) }}" style="display:none">
        {{-- Lock team button placeholder — will add route later --}}
      </form>
      @endif
    </div>

    {{-- Coach card --}}
    <div class="card" style="margin-bottom:16px">
      <div style="font-family:'Sora',sans-serif;font-weight:700;margin-bottom:12px">🎓 Coach</div>
      @if($myTeam->coach)
        <div style="display:flex;align-items:center;gap:10px">
          <div class="avatar" style="width:36px;height:36px;font-size:0.8rem;flex-shrink:0">{{ $myTeam->coach->initials }}</div>
          <div>
            <div style="font-weight:700;font-size:0.9rem">{{ $myTeam->coach->full_name }}</div>
            <div style="font-size:0.75rem;color:var(--text-3)">{{ ucfirst($myTeam->coach->role) }}</div>
            @if($myTeam->coach_status === 'pending')
            <span style="font-size:0.68rem;color:#92400e;background:#fef3c7;border-radius:4px;padding:1px 6px;font-weight:700">⏳ Pending acceptance</span>
            @else
            <span style="font-size:0.68rem;color:#065f46;background:#d1fae5;border-radius:4px;padding:1px 6px;font-weight:700">✓ Confirmed</span>
            @endif
          </div>
        </div>
        {{-- Team lead responds to coach request --}}
        @if($isLead && $myTeam->coach_status === 'pending')
        <div style="display:flex;gap:8px;margin-top:12px">
          <form method="POST" action="{{ route('hackathons.respondCoach', $myTeam) }}">@csrf<input type="hidden" name="action" value="accept"><button class="btn btn-primary btn-sm">Accept Coach</button></form>
          <form method="POST" action="{{ route('hackathons.respondCoach', $myTeam) }}">@csrf<input type="hidden" name="action" value="decline"><button class="btn btn-outline btn-sm">Decline</button></form>
        </div>
        @endif
      @else
        <p style="font-size:0.82rem;color:var(--text-3)">No coach assigned yet. A mentor can volunteer to coach your team from the hackathon page.</p>
      @endif
    </div>

    {{-- Back --}}
    <a href="{{ route('hackathons.show', $hackathon) }}" class="btn btn-outline btn-sm" style="width:100%;justify-content:center;text-align:center">← Back to Hackathon</a>
  </div>

</div>
@endsection
