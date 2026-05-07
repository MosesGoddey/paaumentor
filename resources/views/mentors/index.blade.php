@extends('layouts.sidebar')
@section('title', 'Find a Mentor')

@section('page-content')
<div style="margin-bottom:24px">
  <h1 class="section-title">Find a Mentor</h1>
  <p class="section-sub">Browse verified PAAU mentors or let our smart matching find the best fit for your goals.</p>
</div>

<form method="GET" action="{{ route('mentors.index') }}">
  <div style="display:flex;gap:12px;align-items:center;background:var(--surface);border:1.5px solid var(--border);border-radius:14px;padding:12px 16px;margin-bottom:20px">
    <input type="text" name="search" class="form-input" style="border:none;background:transparent;box-shadow:none;padding:0" placeholder="Search by name, skill, or course..." value="{{ request('search') }}">
    <button type="submit" class="btn btn-primary btn-sm">Search</button>
  </div>
  <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px">
    <select name="level" class="form-select" style="width:auto;padding:8px 14px">
      <option value="">All Levels</option>
      @foreach(['200L','300L','400L','500L','Alumni'] as $l)
        <option value="{{ $l }}" {{ request('level')===$l?'selected':'' }}>{{ $l }}</option>
      @endforeach
    </select>
    <select name="skill" class="form-select" style="width:auto;padding:8px 14px">
      <option value="">All Skills</option>
      @foreach($skills as $skill)
        <option value="{{ $skill->name }}" {{ request('skill')===$skill->name?'selected':'' }}>{{ $skill->name }}</option>
      @endforeach
    </select>
    <button type="submit" class="btn btn-outline btn-sm">Apply Filters</button>
    <a href="{{ route('mentors.index') }}" class="btn btn-outline btn-sm">Clear</a>
  </div>
</form>

{{-- AI Smart Match --}}
<div style="background:var(--surface);border:1.5px solid var(--blue-500);border-radius:16px;padding:20px;margin-bottom:24px">
  <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
    <span style="font-size:1.3rem">✨</span>
    <div>
      <div style="font-weight:700;font-size:0.95rem">AI Smart Mentor Match</div>
      <div style="font-size:0.78rem;color:var(--text-3)">Describe your goals and get smart mentor recommendations</div>
    </div>
  </div>
  <div style="display:flex;gap:10px;align-items:flex-end">
    <textarea id="ai-goals" class="form-input" rows="2"
              placeholder="e.g. I want to learn web development with PHP, improve data structures, and prepare for industry internships..."
              style="flex:1;resize:none"></textarea>
    <button type="button" onclick="findAiMatch()" id="ai-match-btn" class="btn btn-primary" style="white-space:nowrap;align-self:flex-end">Find Match</button>
  </div>
  <div id="ai-results" style="display:none;margin-top:20px">
    <div style="font-size:0.82rem;font-weight:700;color:var(--blue-500);margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid var(--border)">AI Recommended Mentors</div>
    <div id="ai-cards" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px"></div>
  </div>
</div>

<div style="font-size:0.9rem;color:var(--text-3);margin-bottom:16px">
  Showing <strong style="color:var(--text)">{{ $mentors->count() }} mentors</strong>
</div>

<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px">
  @forelse($mentors as $m)
  @php $mentor = $m['mentor']; $score = $m['score']; @endphp
  <div style="background:var(--surface);border:1px solid var(--border);border-radius:20px;overflow:hidden;transition:transform 0.25s,box-shadow 0.25s" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='var(--shadow-lg)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
    <div style="height:70px;background:linear-gradient(135deg,var(--blue-700),var(--blue-500));position:relative">
      @if($mentor->is_active)<span class="badge badge-green" style="position:absolute;top:12px;right:12px">● Online</span>@endif
      @if($score >= 80)<span class="badge badge-blue" style="position:absolute;top:12px;left:12px;background:rgba(255,255,255,0.2);color:#fff;border:none">{{ $score }}% Match</span>@endif
    </div>
    <div style="padding:0 20px 20px">
      <div style="margin-top:-28px;margin-bottom:12px">
        <div class="avatar avatar-lg" style="border:3px solid var(--surface)">{{ $mentor->initials }}</div>
      </div>
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:2px;flex-wrap:wrap">
        <div style="font-weight:800;font-size:1rem">{{ $mentor->full_name }}</div>
        @php $tier = $mentor->mentor_tier; @endphp
        <span style="font-size:0.65rem;font-weight:700;padding:2px 8px;border-radius:99px;letter-spacing:0.05em;
          background:{{ $tier==='lead' ? '#fef3c7' : ($tier==='senior' ? '#ede9fe' : '#dbeafe') }};
          color:{{ $tier==='lead' ? '#92400e' : ($tier==='senior' ? '#5b21b6' : '#1d4ed8') }}">
          {{ $mentor->mentor_tier_icon }} {{ $mentor->mentor_tier_label }}
        </span>
      </div>
      <div style="font-size:0.8rem;color:var(--text-3);margin-bottom:10px">{{ $mentor->level }} · {{ $mentor->department }}</div>
      <div style="display:flex;flex-wrap:wrap;gap:5px;margin-bottom:14px">
        @foreach($mentor->hasSkills->take(4) as $skill)
          <span class="badge badge-blue">{{ $skill->name }}</span>
        @endforeach
      </div>
      <div style="display:flex;gap:16px;margin-bottom:14px">
        <div style="text-align:center">
          <div style="color:#f59e0b;font-size:0.9rem;letter-spacing:1px;line-height:1">
            @for($s = 1; $s <= 5; $s++)
              {{ $s <= round($mentor->average_rating) ? '★' : '☆' }}
            @endfor
          </div>
          <div style="font-size:0.68rem;color:var(--text-3);margin-top:2px">{{ $mentor->average_rating > 0 ? number_format($mentor->average_rating,1) : 'No ratings' }}</div>
        </div>
        <div style="width:1px;background:var(--border)"></div>
        <div style="text-align:center"><div style="font-family:'Sora',sans-serif;font-weight:800;font-size:1.1rem">{{ $mentor->mentorMentorships->count() }}</div><div style="font-size:0.7rem;color:var(--text-3)">Mentees</div></div>
      </div>
      @if($mentor->bio)
        <p style="font-size:0.82rem;color:var(--text-2);line-height:1.6;margin-bottom:14px">{{ Str::limit($mentor->bio, 100) }}</p>
      @endif
      <div style="display:flex;gap:8px">
        <a href="{{ route('mentors.show', $mentor) }}" class="btn btn-primary" style="flex:1;justify-content:center;font-size:0.85rem">View & Request</a>
        <a href="{{ route('chat.index') }}" class="btn btn-outline" style="font-size:0.85rem">Message</a>
      </div>
    </div>
  </div>
  @empty
  <div style="grid-column:span 3;text-align:center;padding:60px;color:var(--text-3)">
      <p>No mentors found matching your criteria. Try adjusting your filters.</p>
  </div>
  @endforelse
</div>

@php
$mentorJson = $mentors->map(fn($m) => [
    'id'       => $m['mentor']->id,
    'name'     => $m['mentor']->full_name,
    'level'    => $m['mentor']->level,
    'dept'     => $m['mentor']->department,
    'skills'   => $m['mentor']->hasSkills->pluck('name')->values()->toArray(),
    'rating'   => $m['mentor']->average_rating,
    'mentees'  => $m['mentor']->mentorMentorships->count(),
    'bio'      => \Illuminate\Support\Str::limit($m['mentor']->bio ?? '', 100),
    'initials' => $m['mentor']->initials,
    'url'      => route('mentors.show', $m['mentor']),
    'online'   => (bool) $m['mentor']->is_active,
])->values();
@endphp
<script>
const mentorData = @json($mentorJson);

async function findAiMatch() {
  const goals = document.getElementById('ai-goals').value.trim();
  if (!goals) { alert('Please describe your goals first.'); return; }

  const btn = document.getElementById('ai-match-btn');
  btn.textContent = 'Analyzing...';
  btn.disabled = true;

  try {
    const res = await fetch('{{ route("ai.mentors.match") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({goals}),
    });
    const data = await res.json();
    if (data.error) { alert(data.error); return; }

    const container = document.getElementById('ai-cards');
    container.innerHTML = '';

    (data.matches || []).forEach(match => {
      const mentor = mentorData.find(m => m.id === match.mentor_id);
      if (!mentor) return;
      const skills = (mentor.skills || []).slice(0, 3)
        .map(s => `<span class="badge badge-blue" style="font-size:0.68rem">${s}</span>`).join(' ');
      container.innerHTML += `
        <div style="background:var(--surface-2);border:1px solid var(--border);border-radius:16px;overflow:hidden">
          <div style="height:54px;background:linear-gradient(135deg,var(--blue-700),var(--blue-500));position:relative">
            ${mentor.online ? '<span class="badge badge-green" style="position:absolute;top:8px;right:8px;font-size:0.65rem">● Online</span>' : ''}
          </div>
          <div style="padding:0 14px 14px">
            <div style="margin-top:-20px;margin-bottom:8px">
              <div class="avatar" style="width:40px;height:40px;font-size:0.8rem;border:3px solid var(--surface-2)">${mentor.initials}</div>
            </div>
            <div style="font-weight:800;font-size:0.9rem;margin-bottom:2px">${mentor.name}</div>
            <div style="font-size:0.72rem;color:var(--text-3);margin-bottom:8px">${mentor.level} · ${mentor.dept}</div>
            <div style="font-size:0.75rem;color:var(--text-2);background:var(--surface);border:1px solid var(--border);border-radius:8px;padding:6px 10px;margin-bottom:8px;line-height:1.5">
              <strong style="color:var(--blue-500)">AI:</strong> ${match.reason}
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:4px;margin-bottom:10px">${skills}</div>
            <a href="${mentor.url}" class="btn btn-primary btn-sm" style="width:100%;justify-content:center;font-size:0.82rem">View &amp; Request</a>
          </div>
        </div>`;
    });

    if (!(data.matches || []).length) {
      container.innerHTML = '<p style="color:var(--text-3);font-size:0.85rem;grid-column:1/-1">No matches found. Try adjusting your goals description.</p>';
    }

    document.getElementById('ai-results').style.display = 'block';
    document.getElementById('ai-results').scrollIntoView({behavior: 'smooth'});
  } catch (err) {
    alert('Failed to get AI match. Please try again.');
  } finally {
    btn.textContent = 'Find Match';
    btn.disabled = false;
  }
}
</script>
@endsection
