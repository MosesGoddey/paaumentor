<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Certificate Verification — PAAUMENTOR</title>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<style>
body{min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;background:var(--bg);padding:32px 16px}
.verify-card{background:var(--surface);border:1px solid var(--border);border-radius:24px;padding:40px;max-width:520px;width:100%;text-align:center;box-shadow:0 8px 32px rgba(0,0,0,0.08)}
</style>
</head>
<body>

<div style="margin-bottom:28px;text-align:center">
  <div style="font-family:'Sora',sans-serif;font-weight:800;font-size:1.3rem;color:var(--blue-500)">PAAUMENTOR</div>
  <div style="font-size:0.78rem;color:var(--text-3)">Certificate Verification Portal</div>
</div>

<div class="verify-card">
  @if($certificate)

  {{-- Valid --}}
  <div style="width:64px;height:64px;border-radius:50%;background:#dcfce7;display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <polyline points="20 6 9 17 4 12"/>
    </svg>
  </div>

  <div style="font-family:'Sora',sans-serif;font-size:1.2rem;font-weight:800;color:#16a34a;margin-bottom:4px">
    Certificate Valid
  </div>
  <p style="font-size:0.85rem;color:var(--text-3);margin-bottom:28px">
    This certificate has been verified as authentic and was issued by PAAUMENTOR.
  </p>

  <div style="background:var(--surface-2);border-radius:14px;padding:20px;text-align:left;display:flex;flex-direction:column;gap:12px;margin-bottom:24px">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;padding-bottom:12px;border-bottom:1px solid var(--border)">
      <span style="font-size:0.78rem;color:var(--text-3);font-weight:600;text-transform:uppercase;letter-spacing:0.05em">Certificate ID</span>
      <span style="font-family:'Sora',sans-serif;font-weight:800;font-size:0.88rem;color:var(--blue-500)">{{ $certificate->certificate_id }}</span>
    </div>
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px">
      <span style="font-size:0.78rem;color:var(--text-3);font-weight:600;text-transform:uppercase;letter-spacing:0.05em">Recipient</span>
      <span style="font-weight:700;font-size:0.9rem;text-align:right">{{ $certificate->user->full_name }}</span>
    </div>
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px">
      <span style="font-size:0.78rem;color:var(--text-3);font-weight:600;text-transform:uppercase;letter-spacing:0.05em">Learning Path</span>
      <span style="font-weight:600;font-size:0.88rem;text-align:right;max-width:240px">{{ $certificate->learningPath->title }}</span>
    </div>
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px">
      <span style="font-size:0.78rem;color:var(--text-3);font-weight:600;text-transform:uppercase;letter-spacing:0.05em">Mentor</span>
      <span style="font-weight:600;font-size:0.88rem">{{ $certificate->learningPath->mentor->full_name }}</span>
    </div>
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px">
      <span style="font-size:0.78rem;color:var(--text-3);font-weight:600;text-transform:uppercase;letter-spacing:0.05em">Date Issued</span>
      <span style="font-weight:600;font-size:0.88rem">{{ $certificate->issued_at->format('F j, Y') }}</span>
    </div>
  </div>

  <div style="font-size:0.75rem;color:var(--text-3)">
    Issued by Prince Abubakar Audu University, Anyigba · PAAUMENTOR Platform
  </div>

  @else

  {{-- Invalid --}}
  <div style="width:64px;height:64px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
    </svg>
  </div>

  <div style="font-family:'Sora',sans-serif;font-size:1.2rem;font-weight:800;color:#dc2626;margin-bottom:4px">
    Certificate Not Found
  </div>
  <p style="font-size:0.85rem;color:var(--text-3);margin-bottom:24px">
    No certificate matching <strong>{{ $certificateId }}</strong> was found in our records. It may be invalid or the ID may have been entered incorrectly.
  </p>

  <div style="font-size:0.75rem;color:var(--text-3)">
    If you believe this is an error, please contact the PAAUMENTOR administrator.
  </div>

  @endif
</div>

<div style="margin-top:20px;font-size:0.78rem;color:var(--text-3)">
  <a href="{{ route('home') }}" style="color:var(--blue-500);text-decoration:none">Return to PAAUMENTOR</a>
</div>

</body>
</html>
