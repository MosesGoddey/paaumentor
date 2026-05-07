<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
@php
  $isHackathon = $certificate->type === 'hackathon';
  $isMentor    = $certificate->type === 'mentor';
  $mentorTier  = $isMentor ? $certificate->user->mentor_tier : '';

  if ($isHackathon) {
      $placement = $certificate->placement ?? 'participant';
      $certTitle = match($placement) {
          '1st'   => 'Certificate of Excellence',
          '2nd'   => 'Certificate of Achievement',
          '3rd'   => 'Certificate of Merit',
          default => 'Certificate of Participation',
      };
      $primary = match($placement) {
          '1st'   => '#92400e',
          '2nd'   => '#374151',
          '3rd'   => '#78350f',
          default => '#1a3a6e',
      };
      $accent  = match($placement) {
          '1st'   => '#d97706',
          '2nd'   => '#9ca3af',
          '3rd'   => '#b45309',
          default => '#c9a227',
      };
  } else {
      $certTitle = match($mentorTier) {
          'lead'   => 'Certificate of Mentorship Excellence',
          'senior' => 'Certificate of Senior Mentorship',
          default  => 'Certificate of Mentorship',
      };
      $primary = match(true) {
          $isMentor && $mentorTier === 'lead'   => '#7c3a00',
          $isMentor && $mentorTier === 'senior' => '#3b0764',
          $isMentor                             => '#1a5c2e',
          default                               => '#1a3a6e',
      };
      $accent = '#c9a227';
  }
@endphp
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
@page { size: A4 portrait; margin: 0; }

html, body {
  width: 210mm;
  height: 297mm;
  font-family: Georgia, 'Times New Roman', serif;
  background: #fff;
  color: #111;
  font-size: 0;
  line-height: 1;
}

/* Only clip here — prevents a second page without clipping inner content */
.page {
  width: 210mm;
  height: 297mm;
  padding: 6mm;
  background: #fff;
  overflow: hidden;
}

.outer-border {
  width: 100%;
  height: 100%;
  border: 5px solid {{ $primary }};
  padding: 2mm;
}

.inner-border {
  width: 100%;
  height: 100%;
  border: 1.5px solid {{ $accent }};
  padding: 6mm 12mm 4mm;
  text-align: center;
  position: relative;
}

.corner {
  position: absolute;
  width: 14mm;
  height: 14mm;
  border-style: solid;
  border-color: {{ $primary }};
}
.corner-tl { top: -2px; left: -2px; border-width: 4px 0 0 4px; }
.corner-tr { top: -2px; right: -2px; border-width: 4px 4px 0 0; }
.corner-bl { bottom: -2px; left: -2px; border-width: 0 0 4px 4px; }
.corner-br { bottom: -2px; right: -2px; border-width: 0 4px 4px 0; }

.corner-inner {
  position: absolute;
  width: 8mm;
  height: 8mm;
  border-style: solid;
  border-color: {{ $accent }};
}
.corner-inner-tl { top: 3mm; left: 3mm; border-width: 2px 0 0 2px; }
.corner-inner-tr { top: 3mm; right: 3mm; border-width: 2px 2px 0 0; }
.corner-inner-bl { bottom: 3mm; left: 3mm; border-width: 0 0 2px 2px; }
.corner-inner-br { bottom: 3mm; right: 3mm; border-width: 0 2px 2px 0; }
</style>
</head>
<body>
<div class="page">
  <div class="outer-border">
    <div class="inner-border">

      <div class="corner corner-tl"></div>
      <div class="corner corner-tr"></div>
      <div class="corner corner-bl"></div>
      <div class="corner corner-br"></div>
      <div class="corner-inner corner-inner-tl"></div>
      <div class="corner-inner corner-inner-tr"></div>
      <div class="corner-inner corner-inner-bl"></div>
      <div class="corner-inner corner-inner-br"></div>

      {{-- Certificate ID — top right (table-based for DomPDF compatibility) --}}
      <table style="width:100%;border-collapse:collapse;margin-bottom:1mm" cellpadding="0" cellspacing="0">
        <tr>
          <td style="font-size:0">&nbsp;</td>
          <td style="vertical-align:top;text-align:center;border:1px solid {{ $accent }};padding:1.5mm 2.5mm">
            <div style="font-family:Arial,sans-serif;font-size:4.5pt;font-weight:normal;color:#aaa;text-transform:uppercase;letter-spacing:0.5px;line-height:1.3">Certificate ID</div>
            <div style="font-family:Arial,sans-serif;font-size:5.5pt;font-weight:bold;color:{{ $primary }};line-height:1.3">{{ $certificate->certificate_id }}</div>
          </td>
        </tr>
      </table>

      {{-- Logo --}}
      @if($hasLogo)
      <div style="margin-bottom:2mm;line-height:1">
        <img src="{{ $logoPath }}" style="width:20mm;height:auto">
      </div>
      @else
      <div style="width:20mm;height:20mm;border-radius:50%;background-color:{{ $primary }};margin:0 auto 2mm;line-height:20mm;text-align:center">
        <span style="color:#fff;font-size:7pt;font-weight:bold;font-family:Arial,sans-serif">PAAU</span>
      </div>
      @endif

      {{-- University name --}}
      <div style="font-family:Arial,Helvetica,sans-serif;font-size:13pt;font-weight:bold;color:{{ $primary }};letter-spacing:1.5px;text-transform:uppercase;line-height:1.2;margin-bottom:1mm">
        Prince Abubakar Audu University, Anyigba
      </div>
      <div style="font-family:Arial,Helvetica,sans-serif;font-size:7.5pt;font-weight:bold;color:{{ $accent }};letter-spacing:2px;text-transform:uppercase;line-height:1.3;margin-bottom:2.5mm">
        Peer Mentorship Programme &nbsp;&bull;&nbsp; PAAUMENTOR
      </div>

      {{-- Top divider --}}
      <table style="width:100%;border-collapse:collapse;margin-bottom:3.5mm" cellpadding="0" cellspacing="0">
        <tr><td style="border-bottom:2px solid {{ $primary }};height:0;font-size:0;line-height:0">&nbsp;</td></tr>
        <tr><td style="height:1.5mm;font-size:0">&nbsp;</td></tr>
        <tr><td style="border-bottom:1px solid {{ $accent }};height:0;font-size:0;line-height:0">&nbsp;</td></tr>
      </table>

      {{-- "This is to certify that" --}}
      <div style="font-size:10pt;color:#555;font-style:italic;line-height:1.3;margin-bottom:2.5mm">
        This is to Certify that
      </div>

      {{-- Recipient name --}}
      <div style="font-size:22pt;color:{{ $primary }};font-weight:bold;letter-spacing:3px;text-transform:uppercase;line-height:1.2;margin-bottom:1mm">
        {{ $certificate->user->full_name }}
      </div>
      <table style="width:100%;border-collapse:collapse;margin-bottom:3.5mm" cellpadding="0" cellspacing="0">
        <tr><td style="border-bottom:1.5px solid {{ $primary }};height:0;font-size:0;line-height:0">&nbsp;</td></tr>
      </table>

      {{-- Certificate body --}}
      @if($isHackathon)
        @php $team = $certificate->hackathonTeam; $hackathon = $team->hackathon; @endphp
        <div style="font-size:9pt;color:#555;font-style:italic;line-height:1.6;margin-bottom:1.5mm">
          As a member of team
        </div>
        <div style="font-size:16pt;color:{{ $primary }};font-style:italic;letter-spacing:1px;line-height:1.2;margin-bottom:1.5mm">
          {{ $team->name }}
        </div>
        <div style="font-size:9pt;color:#555;font-style:italic;line-height:1.4;margin-bottom:1.5mm">
          @if($certificate->placement === 'participant')
            having participated in
          @else
            having achieved <strong style="color:{{ $primary }}">{{ $certificate->placement }} Place</strong> in
          @endif
        </div>
        <div style="font-size:20pt;color:{{ $primary }};font-style:italic;letter-spacing:1px;line-height:1.2;margin-bottom:2mm">
          {{ $certTitle }}
        </div>
        <div style="font-size:8.5pt;color:#666;font-style:italic;line-height:1.4;margin-bottom:1.5mm">awarded for participation in</div>
        <div style="font-family:Arial,Helvetica,sans-serif;font-size:11pt;font-weight:bold;color:{{ $primary }};text-transform:uppercase;letter-spacing:1px;line-height:1.4;margin-bottom:1.5mm">
          {{ $hackathon->title }}
        </div>
        @if($hackathon->theme)
        <div style="font-size:8.5pt;color:#666;font-style:italic;line-height:1.4;margin-bottom:1mm">Theme: {{ $hackathon->theme }}</div>
        @endif
        <div style="font-size:8pt;color:#666;line-height:1.6;margin-bottom:3.5mm">
          Organised under the PAAUMENTOR Peer Mentorship Programme<br>
          Prince Abubakar Audu University, Anyigba
        </div>
      @elseif($isMentor)
        <div style="font-size:9pt;color:#555;font-style:italic;line-height:1.6;margin-bottom:1.5mm">
          Having demonstrated exceptional dedication and commitment<br>
          in guiding and mentoring
        </div>
        <div style="font-size:15pt;color:{{ $primary }};font-style:italic;letter-spacing:1px;line-height:1.2;margin-bottom:1.5mm">
          {{ $certificate->learningPath->mentee->full_name }}
        </div>
        <div style="font-size:9pt;color:#555;font-style:italic;line-height:1.4;margin-bottom:1.5mm">
          has been awarded a
        </div>
        <div style="font-size:20pt;color:{{ $primary }};font-style:italic;letter-spacing:1px;line-height:1.2;margin-bottom:2mm">
          {{ $certTitle }}
        </div>
        <div style="font-size:8.5pt;color:#666;font-style:italic;line-height:1.4;margin-bottom:1.5mm">in</div>
        <div style="font-family:Arial,Helvetica,sans-serif;font-size:11pt;font-weight:bold;color:{{ $primary }};text-transform:uppercase;letter-spacing:1px;line-height:1.4;margin-bottom:1.5mm">
          {{ $certificate->learningPath->title }}
        </div>
        <div style="font-size:8pt;color:#666;line-height:1.6;margin-bottom:3.5mm">
          In recognition of dedicated guidance and mentorship provided<br>
          through the PAAUMENTOR Peer Mentorship Programme at PAAU
        </div>
      @else
        <div style="font-size:9pt;color:#555;font-style:italic;line-height:1.6;margin-bottom:2mm">
          Having successfully completed all tasks and requirements,<br>
          has been awarded a
        </div>
        <div style="font-size:20pt;color:{{ $primary }};font-style:italic;letter-spacing:1px;line-height:1.2;margin-bottom:2mm">
          Certificate of Completion
        </div>
        <div style="font-size:8.5pt;color:#666;font-style:italic;line-height:1.4;margin-bottom:1.5mm">in</div>
        <div style="font-family:Arial,Helvetica,sans-serif;font-size:11pt;font-weight:bold;color:{{ $primary }};text-transform:uppercase;letter-spacing:1px;line-height:1.4;margin-bottom:1.5mm">
          {{ $certificate->learningPath->title }}
        </div>
        <div style="font-size:8pt;color:#666;line-height:1.6;margin-bottom:3.5mm">
          Under the mentorship of
          <strong style="color:{{ $primary }}">{{ $certificate->learningPath->mentor->full_name }}</strong>
        </div>
      @endif

      {{-- Date --}}
      <div style="font-size:8.5pt;color:#444;font-style:italic;line-height:1.3;margin-bottom:3mm">
        Issued on this {{ $certificate->issued_at->format('jS') }} Day of {{ $certificate->issued_at->format('F, Y') }}
      </div>

      {{-- Bottom divider --}}
      <table style="width:100%;border-collapse:collapse;margin-bottom:2.5mm" cellpadding="0" cellspacing="0">
        <tr><td style="border-bottom:1px solid {{ $accent }};height:0;font-size:0;line-height:0">&nbsp;</td></tr>
        <tr><td style="height:1.5mm;font-size:0">&nbsp;</td></tr>
        <tr><td style="border-bottom:2px solid {{ $primary }};height:0;font-size:0;line-height:0">&nbsp;</td></tr>
      </table>

      {{-- Footer: 3 columns --}}
      <table style="width:100%;border-collapse:collapse;table-layout:fixed" cellpadding="0" cellspacing="0">
        <tr>

          {{-- Left: Programme Director --}}
          <td style="width:28%;text-align:center;vertical-align:bottom;padding:0 2mm">
            <div style="width:28mm;height:1px;background-color:{{ $primary }};margin:0 auto 2mm"></div>
            <div style="font-family:Arial,Helvetica,sans-serif;font-size:6.5pt;text-transform:uppercase;letter-spacing:0.5px;color:#333;line-height:1.4;word-wrap:break-word">Programme Director</div>
            <div style="font-family:Arial,Helvetica,sans-serif;font-size:5.5pt;color:#888;margin-top:0.5mm;line-height:1.4">PAAUMENTOR</div>
          </td>

          {{-- Center: QR only --}}
          <td style="width:28%;text-align:center;vertical-align:bottom;padding:0 2mm">
            @if($qrCode)
              {!! $qrCode !!}
              <div style="font-size:5pt;color:#aaa;text-transform:uppercase;letter-spacing:0.5px;line-height:1.3;margin-top:1mm">Scan to verify</div>
            @endif
          </td>

          {{-- Right: Vice Chancellor --}}
          <td style="width:44%;text-align:center;vertical-align:bottom;padding:0 2mm">
            <div style="width:36mm;height:1px;background-color:{{ $primary }};margin:0 auto 2mm"></div>
            <div style="font-family:Arial,Helvetica,sans-serif;font-size:6.5pt;text-transform:uppercase;letter-spacing:0.5px;color:#333;line-height:1.4;word-wrap:break-word">Vice Chancellor</div>
            <div style="font-family:Arial,Helvetica,sans-serif;font-size:5.5pt;color:#888;margin-top:0.5mm;line-height:1.4">PAAU, Anyigba</div>
          </td>

        </tr>
      </table>

    </div>
  </div>
</div>
</body>
</html>
