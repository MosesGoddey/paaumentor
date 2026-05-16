@extends('layouts.sidebar')
@section('title', 'Judge Panel — ' . $hackathon->title)

@section('breadcrumbs')
<span>/</span>
<a href="{{ route('hackathons.index') }}" style="color:var(--text-3);text-decoration:none">Hackathons</a>
<span>/</span>
<a href="{{ route('hackathons.show', $hackathon) }}" style="color:var(--text-3);text-decoration:none">{{ $hackathon->title }}</a>
<span>/</span><span>Judge Panel</span>
@endsection

@section('page-content')
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

<div style="margin-bottom:24px">
  <h1 class="section-title">Judge Panel</h1>
  <p class="section-sub">Score each submission on 4 criteria (1–10). You cannot score your own team's project.</p>
</div>

@if($submissions->isEmpty())
<div class="card" style="text-align:center;padding:60px;color:var(--text-3)">
  <div style="font-size:2rem;margin-bottom:12px"></div>
  <p>No submitted projects yet.</p>
</div>
@else
@foreach($submissions as $i => $submission)
@php
  $isOwn   = $submission->team->users->contains('id', $user->id);
  $myScore = $myScores[$submission->id] ?? null;
@endphp
<div class="card" style="margin-bottom:20px">
  {{-- Header --}}
  <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:16px;flex-wrap:wrap">
    <div style="width:36px;height:36px;background:linear-gradient(135deg,var(--blue-700),var(--blue-500));border-radius:10px;color:#fff;display:flex;align-items:center;justify-content:center;font-family:'Sora',sans-serif;font-weight:800;font-size:0.9rem;flex-shrink:0">{{ $i+1 }}</div>
    <div style="flex:1;min-width:0">
      <div style="font-family:'Sora',sans-serif;font-weight:800;font-size:1.05rem;margin-bottom:3px">{{ $submission->title }}</div>
      <div style="font-size:0.8rem;color:var(--text-3)">
        Team: <strong>{{ $submission->team->name }}</strong>
        @if($submission->team->track) · {{ $submission->team->track }} @endif
        · {{ $submission->team->users->count() }} member(s)
      </div>
    </div>
    @if($myScore)
    <span class="badge badge-green"> Scored ({{ $myScore->total }}/40)</span>
    @elseif($isOwn)
    <span class="badge" style="background:#fee2e2;color:#b91c1c">Your team</span>
    @endif
  </div>

  {{-- Team members --}}
  <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:12px">
    @foreach($submission->team->users as $m)
    <div style="display:flex;align-items:center;gap:5px;background:var(--surface-2);border-radius:8px;padding:3px 8px">
      <div class="avatar" style="width:20px;height:20px;font-size:0.55rem;flex-shrink:0">{{ $m->initials }}</div>
      <span style="font-size:0.75rem;font-weight:600">{{ $m->full_name }}</span>
      @if($m->pivot->is_lead)<span style="font-size:0.6rem;color:var(--text-3)">(Lead)</span>@endif
    </div>
    @endforeach
  </div>

  {{-- Description --}}
  <p style="font-size:0.88rem;color:var(--text-2);line-height:1.7;margin-bottom:12px">{{ $submission->description }}</p>

  {{-- Links --}}
  <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px">
    @if($submission->github_url)<a href="{{ $submission->github_url }}" target="_blank" class="btn btn-outline btn-sm"> GitHub</a>@endif
    @if($submission->demo_url)<a href="{{ $submission->demo_url }}" target="_blank" class="btn btn-outline btn-sm"> Live Demo</a>@endif
    @if($submission->deck_url)<a href="{{ $submission->deck_url }}" target="_blank" class="btn btn-outline btn-sm"> Slides</a>@endif
  </div>

  {{-- Scoring form --}}
  @if($isOwn)
  <div style="padding:14px;background:#fef2f2;border-radius:10px;font-size:0.85rem;color:#b91c1c">
    You cannot score your own team's project.
  </div>
  @elseif($hackathon->status !== 'judging')
  <div style="padding:14px;background:var(--surface-2);border-radius:10px;font-size:0.85rem;color:var(--text-3)">
    Scoring will open when the hackathon moves to the judging phase.
  </div>
  @else
  <form method="POST" action="{{ route('hackathons.score', $submission) }}" style="background:var(--surface-2);border-radius:12px;padding:16px">
    @csrf
    <div style="font-weight:700;font-size:0.9rem;margin-bottom:14px">{{ $myScore ? 'Update Score' : 'Score this Project' }}</div>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:14px">
      @foreach(['innovation'=>'Innovation','execution'=>'Execution','impact'=>'Impact','presentation'=>'Presentation'] as $field => $label)
      <div>
        <label style="font-size:0.75rem;font-weight:700;color:var(--text-3);display:block;margin-bottom:4px">{{ $label }} <span style="color:var(--text-3)">(1–10)</span></label>
        <input type="number" name="{{ $field }}" min="1" max="10"
               value="{{ $myScore ? $myScore->$field : '' }}"
               class="form-input" style="text-align:center;font-family:'Sora',sans-serif;font-weight:800;font-size:1.1rem" required
               oninput="updateTotal(this.closest('form'))">
      </div>
      @endforeach
    </div>
    <div style="margin-bottom:12px;font-size:0.85rem;color:var(--text-3)">
      Total: <strong id="total-{{ $submission->id }}" style="color:var(--blue-500)">{{ $myScore ? $myScore->total : '—' }}</strong> / 40
    </div>
    <div class="form-group" style="margin-bottom:12px">
      <label class="form-label">Feedback Notes (optional)</label>
      <textarea name="notes" class="form-input" rows="2" placeholder="Brief feedback for the team…">{{ $myScore->notes ?? '' }}</textarea>
    </div>
    <button type="submit" class="btn btn-primary btn-sm">{{ $myScore ? 'Update Score' : 'Submit Score' }}</button>
  </form>
  @endif

  {{-- Other judges' scores summary --}}
  @if($submission->scores->count() > 0)
  <div style="margin-top:12px;font-size:0.78rem;color:var(--text-3)">
    {{ $submission->scores->count() }} judge(s) have scored this — avg: <strong style="color:var(--text)">{{ round($submission->average_score, 1) }}/40</strong>
  </div>
  @endif
</div>
@endforeach
@endif

<script>
function updateTotal(form) {
  const inputs = form.querySelectorAll('input[type=number]');
  let total = 0, allFilled = true;
  inputs.forEach(i => {
    const v = parseInt(i.value);
    if (!isNaN(v)) total += v; else allFilled = false;
  });
  const display = form.querySelector('[id^="total-"]');
  if (display) display.textContent = allFilled ? total : '—';
}
</script>
@endsection
