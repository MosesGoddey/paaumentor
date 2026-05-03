<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>{{ $title }}</title>
<style>
  body{margin:0;padding:0;background:#f4f6fb;font-family:'Segoe UI',Arial,sans-serif;color:#1e293b}
  .wrap{max-width:580px;margin:40px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08)}
  .header{background:linear-gradient(135deg,#0a1628,#1a3a6e);padding:32px 40px;text-align:center}
  .header-logo{display:inline-block;font-size:1.3rem;font-weight:800;color:#fff;letter-spacing:0.02em}
  .header-logo span{color:#f5a623}
  .body{padding:36px 40px}
  .title{font-size:1.25rem;font-weight:700;margin-bottom:12px;color:#0f172a}
  .text{font-size:0.95rem;line-height:1.7;color:#475569;margin-bottom:24px}
  .btn{display:inline-block;background:#2563eb;color:#fff!important;text-decoration:none;padding:13px 28px;border-radius:10px;font-weight:700;font-size:0.95rem}
  .footer{background:#f8fafc;padding:20px 40px;text-align:center;font-size:0.78rem;color:#94a3b8;border-top:1px solid #e2e8f0}
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <div class="header-logo">PAAU<span>MENTOR</span></div>
    <div style="color:rgba(255,255,255,0.6);font-size:0.8rem;margin-top:4px">Prince Abubakar Audu University</div>
  </div>

  <div class="body">
    <div class="title">{{ $title }}</div>
    <div class="text">{!! nl2br(e($body)) !!}</div>

    @if($actionText && $actionUrl)
    <div style="text-align:center;margin-bottom:8px">
      <a href="{{ $actionUrl }}" class="btn">{{ $actionText }}</a>
    </div>
    @endif
  </div>

  <div class="footer">
    © {{ date('Y') }} PAAUMENTOR · Prince Abubakar Audu University, Anyigba<br>
    This is an automated message — please do not reply to this email.
  </div>
</div>
</body>
</html>
