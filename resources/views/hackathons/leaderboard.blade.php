@extends('layouts.sidebar')
@section('title', 'Results — ' . $hackathon->title)

@section('breadcrumbs')
<span>/</span>
<a href="{{ route('hackathons.index') }}" style="color:var(--text-3);text-decoration:none">Hackathons</a>
<span>/</span>
<a href="{{ route('hackathons.show', $hackathon) }}" style="color:var(--text-3);text-decoration:none">{{ $hackathon->title }}</a>
<span>/</span><span>Results</span>
@endsection

@section('page-content')
<div style="text-align:center;margin-bottom:32px">
  <div style="font-size:2.5rem;margin-bottom:8px">🏆</div>
  <h1 style="font-size:1.6rem;font-weight:800;margin-bottom:6px">{{ $hackathon->title }}</h1>
  <p style="color:var(--text-3);font-size:0.9rem">Final Results
    @if($hackathon->end_date)· {{ $hackathon->end_date->format('F j, Y') }}@endif
  </p>
  @if($hackathon->status !== 'completed')
  <div style="display:inline-block;margin-top:8px;background:#fef3c7;color:#92400e;border-radius:8px;padding:4px 14px;font-size:0.8rem;font-weight:700">⏳ Judging in progress — results not yet final</div>
  @endif
</div>

@if($submissions->isEmpty())
<div class="card" style="text-align:center;padding:60px;color:var(--text-3)">
  <p>No submitted projects to rank yet.</p>
</div>
@else

{{-- Top 3 Podium --}}
@if($submissions->count() >= 2)
<div style="display:flex;align-items:flex-end;justify-content:center;gap:16px;margin-bottom:32px;flex-wrap:wrap">
  @foreach([1,0,2] as $pos)
    @if(isset($submissions[$pos]))
    @php
      $s = $submissions[$pos];
      $medals  = ['🥇','🥈','🥉'];
      $heights = [160, 130, 110];
      $colors  = ['linear-gradient(135deg,#d97706,#fbbf24)', 'linear-gradient(135deg,#6b7280,#d1d5db)', 'linear-gradient(135deg,#92400e,#d97706)'];
    @endphp
    <div style="text-align:center;flex:1;max-width:200px">
      <div style="font-size:1.6rem;margin-bottom:6px">{{ $medals[$pos] }}</div>
      <div style="font-weight:800;font-size:0.9rem;margin-bottom:4px">{{ $s->team->name }}</div>
      <div style="font-size:0.75rem;color:var(--text-3);margin-bottom:8px">{{ $s->title }}</div>
      <div style="background:{{ $colors[$pos] }};height:{{ $heights[$pos] }}px;border-radius:12px 12px 0 0;display:flex;align-items:flex-start;justify-content:center;padding-top:14px">
        <div style="color:#fff;font-family:'Sora',sans-serif;font-weight:800;font-size:1.4rem">{{ round($s->average_score, 1) }}</div>
      </div>
      <div style="background:{{ $colors[$pos] }};opacity:0.15;height:4px;border-radius:0 0 4px 4px"></div>
    </div>
    @endif
  @endforeach
</div>
@endif

{{-- Full Rankings Table --}}
<div class="card">
  <div style="font-family:'Sora',sans-serif;font-weight:700;margin-bottom:16px">Full Rankings</div>
  <div style="overflow-x:auto">
    <table style="width:100%;border-collapse:collapse;font-size:0.88rem">
      <thead>
        <tr style="border-bottom:2px solid var(--border)">
          <th style="text-align:left;padding:8px 12px;font-size:0.75rem;color:var(--text-3);font-weight:700">#</th>
          <th style="text-align:left;padding:8px 12px;font-size:0.75rem;color:var(--text-3);font-weight:700">TEAM / PROJECT</th>
          <th style="text-align:center;padding:8px;font-size:0.75rem;color:var(--text-3);font-weight:700">INNOVATION</th>
          <th style="text-align:center;padding:8px;font-size:0.75rem;color:var(--text-3);font-weight:700">EXECUTION</th>
          <th style="text-align:center;padding:8px;font-size:0.75rem;color:var(--text-3);font-weight:700">IMPACT</th>
          <th style="text-align:center;padding:8px;font-size:0.75rem;color:var(--text-3);font-weight:700">PRESENTATION</th>
          <th style="text-align:center;padding:8px;font-size:0.75rem;color:var(--text-3);font-weight:700">TOTAL</th>
          <th style="text-align:center;padding:8px;font-size:0.75rem;color:var(--text-3);font-weight:700">JUDGES</th>
        </tr>
      </thead>
      <tbody>
        @foreach($submissions as $i => $s)
        @php
          $rank  = $i + 1;
          $icons = [1=>'🥇',2=>'🥈',3=>'🥉'];
          $isMe  = $myTeam && $myTeam->id === $s->team_id;
          $sum   = $s->score_summary;
        @endphp
        <tr style="border-bottom:1px solid var(--border);{{ $isMe ? 'background:linear-gradient(90deg,#eff6ff,transparent)' : '' }}">
          <td style="padding:12px;font-family:'Sora',sans-serif;font-weight:800;font-size:1rem">
            {{ $icons[$rank] ?? $rank }}
          </td>
          <td style="padding:12px">
            <div style="font-weight:700;margin-bottom:2px">
              {{ $s->team->name }}
              @if($isMe)<span style="font-size:0.68rem;background:#dbeafe;color:#1d4ed8;border-radius:4px;padding:1px 6px;font-weight:700;margin-left:6px">You</span>@endif
            </div>
            <div style="font-size:0.78rem;color:var(--text-3)">{{ $s->title }}</div>
            @if($s->team->track)<span class="badge badge-blue" style="font-size:0.65rem;margin-top:3px">{{ $s->team->track }}</span>@endif
          </td>
          <td style="text-align:center;padding:8px;font-weight:700">{{ $sum['innovation'] }}</td>
          <td style="text-align:center;padding:8px;font-weight:700">{{ $sum['execution'] }}</td>
          <td style="text-align:center;padding:8px;font-weight:700">{{ $sum['impact'] }}</td>
          <td style="text-align:center;padding:8px;font-weight:700">{{ $sum['presentation'] }}</td>
          <td style="text-align:center;padding:8px;font-family:'Sora',sans-serif;font-weight:800;font-size:1.05rem;color:var(--blue-500)">{{ round($s->average_score, 1) }}</td>
          <td style="text-align:center;padding:8px;color:var(--text-3);font-size:0.8rem">{{ $s->scores->count() }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

{{-- My cert link --}}
@if($hackathon->status === 'completed')
<div style="text-align:center;margin-top:24px">
  <a href="{{ route('certificates.index') }}" class="btn btn-primary">View My Certificates →</a>
</div>
@endif

@endif
@endsection
