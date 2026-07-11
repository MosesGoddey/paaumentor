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
          $isMentor                             => '#166534',
          default                               => '#1a3a6e',
      };
      $accent = '#b8860b';
  }
@endphp
<style>
* { margin: 0; padding: 0; }
@page { size: A4 portrait; margin: 0; }
body { font-family: Georgia, 'Times New Roman', serif; background: #fff; color: #1a1a1a; }

/* Explicit content-box mm budget — DomPDF adds padding/border OUTSIDE the
   declared size, so every dimension below is computed to total ≤ 297mm:
   page 296 ≥ frame margin 9 + frame (268 + 2×1.2 border + 2×1.6 pad = 273.6)
   frame content 268 ≥ frame-in (250 + 2×0.4 border + 2×8 pad = 266.8)      */
.page      { position: absolute; top: 0; left: 0; width: 210mm; height: 296mm; overflow: hidden; }
.frame     { width: 188mm; height: 268mm; margin: 9mm auto 0; border: 1.2mm solid {{ $primary }}; padding: 1.6mm; }
.frame-in  { width: 163mm; height: 250mm; border: 0.4mm solid {{ $accent }}; padding: 8mm 12mm; text-align: center; }

.layout    { width: 100%; height: 248mm; border-collapse: collapse; }

.rule-heavy { border-bottom: 0.8mm solid {{ $primary }}; font-size: 0; line-height: 0; }
.rule-light { border-bottom: 0.25mm solid {{ $accent }}; font-size: 0; line-height: 0; }

.sig-line { width: 42mm; height: 0.35mm; background-color: #333; margin: 0 auto 2.2mm; }
.sig-role { font-family: Arial, Helvetica, sans-serif; font-size: 8pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.8px; color: #222; line-height: 1.4; }
.sig-org  { font-family: Arial, Helvetica, sans-serif; font-size: 7pt; color: #777; margin-top: 0.8mm; line-height: 1.3; }
</style>
</head>
<body>
<div class="page">
  <div class="frame">
    <div class="frame-in">

      <table class="layout" cellpadding="0" cellspacing="0">

        {{-- ================= HEADER ================= --}}
        <tr>
          <td style="vertical-align:top;text-align:center;height:72mm">

            @if($hasLogo)
              <img src="{{ $logoPath }}" style="width:26mm;height:auto;margin-bottom:3mm">
            @else
              <div style="width:24mm;height:24mm;border:0.5mm solid {{ $primary }};border-radius:50%;margin:0 auto 3mm;line-height:23mm;text-align:center">
                <span style="color:{{ $primary }};font-size:9pt;font-weight:bold;font-family:Arial,sans-serif">PAAU</span>
              </div>
            @endif

            <div style="font-family:Arial,Helvetica,sans-serif;font-size:16pt;font-weight:bold;color:{{ $primary }};letter-spacing:1px;text-transform:uppercase;line-height:1.25;margin-bottom:1.5mm">
              Prince Abubakar Audu University
            </div>
            <div style="font-family:Arial,Helvetica,sans-serif;font-size:9pt;color:#555;letter-spacing:2.5px;text-transform:uppercase;line-height:1.3;margin-bottom:4mm">
              Anyigba, Kogi State, Nigeria
            </div>

            <div style="font-family:Arial,Helvetica,sans-serif;font-size:12.5pt;font-weight:bold;color:#222;letter-spacing:1.5px;text-transform:uppercase;line-height:1.3;margin-bottom:1mm">
              {{ $certTitle }}
            </div>
            <div style="font-family:Arial,Helvetica,sans-serif;font-size:8.5pt;font-weight:bold;color:{{ $accent }};letter-spacing:1px;text-transform:uppercase;line-height:1.4;margin-bottom:4mm">
              Certificate No. {{ $certificate->certificate_id }}
            </div>

            <table style="width:60%;margin:0 auto;border-collapse:collapse" cellpadding="0" cellspacing="0">
              <tr><td class="rule-heavy">&nbsp;</td></tr>
            </table>

          </td>
        </tr>

        {{-- ================= BODY ================= --}}
        <tr>
          <td style="vertical-align:middle;text-align:center">

            <div style="font-size:12pt;color:#444;font-style:italic;line-height:1.5;margin-bottom:4mm">
              The PAAUMENTOR Peer Mentorship Programme hereby certifies that
            </div>

            <div style="font-size:26pt;color:{{ $primary }};font-weight:bold;letter-spacing:2.5px;text-transform:uppercase;line-height:1.25;margin-bottom:4.5mm">
              {{ $certificate->user->full_name }}
            </div>

            @if($isHackathon)
              @php $team = $certificate->hackathonTeam; $hackathon = $team->hackathon; @endphp
              <div style="font-size:11pt;color:#444;font-style:italic;line-height:1.7;margin-bottom:3mm">
                as a member of team <strong style="color:{{ $primary }};font-style:normal">{{ $team->name }}</strong>,
                @if($certificate->placement === 'participant')
                  participated in
                @else
                  achieved <strong style="color:{{ $primary }};font-style:normal">{{ $certificate->placement }} Place</strong> in
                @endif
              </div>
              <div style="font-size:15pt;font-weight:bold;color:#222;text-transform:uppercase;letter-spacing:1px;line-height:1.4;margin-bottom:3mm">
                {{ $hackathon->title }}
              </div>
              @if($hackathon->theme)
              <div style="font-size:10pt;color:#666;font-style:italic;line-height:1.5;margin-bottom:3mm">Theme: {{ $hackathon->theme }}</div>
              @endif
              <div style="font-size:10.5pt;color:#555;line-height:1.7">
                organised under the PAAUMENTOR Peer Mentorship Programme.
              </div>
            @elseif($isMentor)
              <div style="font-size:11pt;color:#444;font-style:italic;line-height:1.7;margin-bottom:3mm">
                has demonstrated exceptional dedication and commitment in guiding and mentoring
              </div>
              <div style="font-size:15pt;color:{{ $primary }};font-weight:bold;letter-spacing:0.5px;line-height:1.3;margin-bottom:3.5mm">
                {{ $certificate->learningPath->mentee->full_name }}
              </div>
              <div style="font-size:11pt;color:#444;font-style:italic;line-height:1.6;margin-bottom:2.5mm">
                to the successful completion of the learning path
              </div>
              <div style="font-size:15pt;font-weight:bold;color:#222;text-transform:uppercase;letter-spacing:1px;line-height:1.4;margin-bottom:4mm">
                {{ $certificate->learningPath->title }}
              </div>
              <div style="font-size:10.5pt;color:#555;line-height:1.7">
                in recognition of dedicated guidance and mentorship provided<br>
                through the PAAUMENTOR Peer Mentorship Programme.
              </div>
            @else
              <div style="font-size:11pt;color:#444;font-style:italic;line-height:1.7;margin-bottom:3mm">
                has successfully completed all tasks, assessments and requirements<br>
                of the learning path
              </div>
              <div style="font-size:15pt;font-weight:bold;color:#222;text-transform:uppercase;letter-spacing:1px;line-height:1.4;margin-bottom:4mm">
                {{ $certificate->learningPath->title }}
              </div>
              <div style="font-size:10.5pt;color:#555;line-height:1.7">
                under the mentorship of
                <strong style="color:{{ $primary }}">{{ $certificate->learningPath->mentor->full_name }}</strong><br>
                through the PAAUMENTOR Peer Mentorship Programme.
              </div>
            @endif

            <div style="font-size:11pt;color:#333;font-style:italic;line-height:1.4;margin-top:6mm">
              Given under the seal of the Programme this
              {{ $certificate->issued_at->format('jS') }} day of {{ $certificate->issued_at->format('F, Y') }}
            </div>

          </td>
        </tr>

        {{-- ================= FOOTER ================= --}}
        <tr>
          <td style="vertical-align:bottom;height:52mm">

            <table style="width:70%;margin:0 auto 6mm;border-collapse:collapse" cellpadding="0" cellspacing="0">
              <tr><td class="rule-light">&nbsp;</td></tr>
            </table>

            <table style="width:100%;border-collapse:collapse;table-layout:fixed" cellpadding="0" cellspacing="0">
              <tr>
                <td style="width:33%;text-align:center;vertical-align:bottom;padding:0 3mm">
                  <div class="sig-line"></div>
                  <div class="sig-role">Programme Director</div>
                  <div class="sig-org">PAAUMENTOR</div>
                </td>
                <td style="width:34%;text-align:center;vertical-align:bottom;padding:0 3mm">
                  @if($qrCode)
                    {!! $qrCode !!}
                    <div style="font-family:Arial,Helvetica,sans-serif;font-size:6.5pt;color:#999;text-transform:uppercase;letter-spacing:0.8px;line-height:1.4;margin-top:1.5mm">Scan to Verify</div>
                  @endif
                </td>
                <td style="width:33%;text-align:center;vertical-align:bottom;padding:0 3mm">
                  <div class="sig-line"></div>
                  <div class="sig-role">Vice Chancellor</div>
                  <div class="sig-org">PAAU, Anyigba</div>
                </td>
              </tr>
            </table>

          </td>
        </tr>

      </table>

    </div>
  </div>
</div>
</body>
</html>
