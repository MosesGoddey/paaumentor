<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password — PAAUMENTOR</title>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<style>
.auth-page{min-height:100vh;display:grid;grid-template-columns:1fr 1fr;}
.auth-left{padding:60px 48px;display:flex;flex-direction:column;justify-content:space-between;position:relative;overflow:hidden;}
.auth-left::before{content:'';position:absolute;inset:0;background-image:url('{{ asset('images/login-bg.jpg') }}');background-size:cover;background-position:center;filter:blur(3px) brightness(0.45);transform:scale(1.06);}
.auth-left::after{content:'';position:absolute;inset:0;background:linear-gradient(160deg,rgba(10,22,40,0.75) 0%,rgba(26,58,110,0.60) 100%);}
.auth-left > *{position:relative;z-index:1;}
.auth-right{background:var(--bg);padding:60px 48px;display:flex;flex-direction:column;justify-content:center;overflow-y:auto;}
.auth-right-inner{max-width:420px;margin:0 auto;width:100%;}
.input-wrap{position:relative;}
@media(max-width:900px){.auth-page{grid-template-columns:1fr;}.auth-left{display:none;}.auth-right{padding:40px 24px;}}
</style>
</head>
<body>
<div class="auth-page">
  <div class="auth-left">
    <div style="display:flex;align-items:center;gap:12px">
      <div style="width:44px;height:44px;background:rgba(255,255,255,0.15);border-radius:12px;display:flex;align-items:center;justify-content:center;font-family:'Sora',sans-serif;font-weight:800;color:#fff;font-size:1.1rem;border:1px solid rgba(255,255,255,0.2)">PM</div>
      <span style="font-family:'Sora',sans-serif;font-weight:700;color:#fff;font-size:1.2rem">PAAU<span style="color:#f5a623">MENTOR</span></span>
    </div>
    <div>
      <h1 style="font-family:'Sora',sans-serif;font-size:2.4rem;font-weight:800;color:#fff;line-height:1.2;margin-bottom:16px">Choose a new<br>password</h1>
      <p style="color:rgba(255,255,255,0.65);font-size:1rem;line-height:1.7;max-width:360px">Pick a strong password you haven't used before.</p>
    </div>
    <p style="color:rgba(255,255,255,0.35);font-size:0.8rem">© {{ date('Y') }} PAAUMENTOR · Prince Abubakar Audu University</p>
  </div>

  <div class="auth-right">
    <div class="auth-right-inner">
      <div style="margin-bottom:32px">
        <div style="width:52px;height:52px;background:#d1fae5;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin-bottom:16px"></div>
        <h2 style="font-size:1.7rem;font-weight:800;margin-bottom:6px">Set a new password</h2>
        <p style="font-size:0.88rem;color:var(--text-3)">Your new password must be at least 8 characters.</p>
      </div>

      @if($errors->any())
      <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:14px 16px;margin-bottom:20px;font-size:0.85rem;color:#dc2626">
        {{ $errors->first() }}
      </div>
      @endif

      <form method="POST" action="{{ route('password.store') }}" style="display:flex;flex-direction:column;gap:16px">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="form-group">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" class="form-input" value="{{ old('email', $request->email) }}" required readonly style="background:var(--surface-2);cursor:not-allowed">
        </div>

        <div class="form-group">
          <label class="form-label">New Password</label>
          <div class="input-wrap">
            <input type="password" name="password" id="resetPw" class="form-input" placeholder="Minimum 8 characters" required autocomplete="new-password">
            <button type="button" onclick="togglePw('resetPw')" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:0.78rem;color:var(--text-3);font-weight:600">Show</button>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Confirm New Password</label>
          <div class="input-wrap">
            <input type="password" name="password_confirmation" id="resetPwConfirm" class="form-input" placeholder="Repeat your new password" required autocomplete="new-password">
            <button type="button" onclick="togglePw('resetPwConfirm')" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:0.78rem;color:var(--text-3);font-weight:600">Show</button>
          </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">Reset Password</button>
      </form>
    </div>
  </div>
</div>
<script>
function togglePw(id) { const e = document.getElementById(id); e.type = e.type === 'password' ? 'text' : 'password'; }
</script>
</body>
</html>
