<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign In — PAAUMENTOR</title>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<style>
.auth-page{min-height:100vh;display:grid;grid-template-columns:1fr 1fr;}
.auth-left{padding:60px 48px;display:flex;flex-direction:column;justify-content:space-between;position:relative;overflow:hidden;}

/* Blurry background image */
.auth-left::before{
  content:'';
  position:absolute;inset:0;
  background-image:url('{{ asset('images/login-bg.jpg') }}');
  background-size:cover;background-position:center;
  filter:blur(3px) brightness(0.9);
  transform:scale(1.06);
}
/* Dark gradient overlay */
.auth-left::after{
  content:'';
  position:absolute;inset:0;
  background:linear-gradient(160deg,rgba(10,22,40,0.75) 0%,rgba(26,58,110,0.60) 100%);
}
.auth-left > *{position:relative;z-index:1;}

.auth-right{background:var(--bg);padding:60px 48px;display:flex;flex-direction:column;justify-content:center;overflow-y:auto;}
.auth-right-inner{max-width:420px;margin:0 auto;width:100%;}
.input-wrap{position:relative;}
.input-wrap .form-input{padding-left:42px;}
.input-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);font-size:1rem;pointer-events:none;}
.role-select{display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;}
.role-option{display:none;}
.role-label{display:flex;flex-direction:column;align-items:center;gap:6px;padding:12px 8px;border-radius:12px;border:1.5px solid var(--border);cursor:pointer;transition:all 0.2s;font-size:0.8rem;font-weight:600;color:var(--text-3);text-align:center;}
.role-option:checked + .role-label{border-color:var(--blue-500);background:var(--blue-100);color:var(--blue-500);}
@media(max-width:900px){.auth-page{grid-template-columns:1fr;}.auth-left{display:none;}.auth-right{padding:40px 24px;}}
</style>
</head>
<body>
<div class="auth-page">
  <div class="auth-left">
    <div style="display:flex;align-items:center;gap:12px;position:relative;z-index:1">
      <div style="width:44px;height:44px;background:rgba(255,255,255,0.15);border-radius:12px;display:flex;align-items:center;justify-content:center;font-family:'Sora',sans-serif;font-weight:800;color:#fff;font-size:1.1rem;border:1px solid rgba(255,255,255,0.2)">PM</div>
      <span style="font-family:'Sora',sans-serif;font-weight:700;color:#fff;font-size:1.2rem">PAAU<span style="color:#f5a623">MENTOR</span></span>
    </div>
    <div style="position:relative;z-index:1">
      <h2 style="font-size:2.4rem;font-weight:800;color:#fff;line-height:1.2;margin-bottom:16px">Your <span style="color:#fcd34d">academic success</span> starts here.</h2>
      <p style="color:rgba(255,255,255,0.7);font-size:0.95rem;line-height:1.7;margin-bottom:36px">Join PAAU's official peer mentorship platform — connect with senior students and alumni who've walked your path.</p>
      @foreach([['Smart Mentor Matching','AI-powered pairing based on skills and courses'],['Real-Time Sessions','Video calls, chat, and screen sharing'],['Verified Certificates','Auto-generated PDF certificates with QR codes'],['Progress Tracking','Structured learning paths with milestones']] as [$title,$desc])
      <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:16px">
        <div style="width:4px;height:36px;background:rgba(255,255,255,0.3);border-radius:4px;flex-shrink:0;margin-top:2px"></div>
        <div style="font-size:0.88rem;color:rgba(255,255,255,0.8)"><strong style="color:#fff;display:block">{{ $title }}</strong>{{ $desc }}</div>
      </div>
      @endforeach
    </div>
    <div style="position:relative;z-index:1">
      <p style="font-size:0.78rem;color:rgba(255,255,255,0.5)">© {{ date('Y') }} PAAUMENTOR · Prince Abubakar Audu University, Anyigba<br>Moses Goddey Joseph (23CS1004)</p>
    </div>
  </div>

  <div class="auth-right">
    <div class="auth-right-inner">
      @if($errors->any())
        <div style="background:#fee2e2;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:0.85rem;color:#991b1b">
          @foreach($errors->all() as $e) <div> {{ $e }}</div> @endforeach
        </div>
      @endif

      <div id="loginPanel">
        <h2 style="font-size:1.7rem;font-weight:800;margin-bottom:6px">Welcome back</h2>
        <p style="font-size:0.88rem;color:var(--text-3);margin-bottom:28px">Sign in to your PAAUMENTOR account to continue.</p>
        <form method="POST" action="{{ route('login') }}" style="display:flex;flex-direction:column;gap:16px">
          @csrf
          <div class="form-group">
            <label class="form-label">Email or Student ID</label>
            <input type="text" name="login" class="form-input" placeholder="e.g. 23CS1004 or you@paau.edu.ng" value="{{ old('login') }}" required>
          </div>
          <div class="form-group">
            <label class="form-label">Password</label>
            <div class="input-wrap">
              <input type="password" name="password" id="loginPw" class="form-input" placeholder="Enter your password" required>
              <button type="button" onclick="togglePw('loginPw')" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:0.78rem;color:var(--text-3);font-weight:600">Show</button>
            </div>
          </div>
          <div style="display:flex;align-items:center;justify-content:space-between">
            <label style="display:flex;align-items:center;gap:8px;font-size:0.85rem;cursor:pointer">
              <input type="checkbox" name="remember" style="accent-color:var(--blue-500)"> Remember me
            </label>
            <a href="{{ route('password.request') }}" style="font-size:0.82rem;color:var(--blue-500);text-decoration:none;font-weight:600">Forgot password?</a>
          </div>
          <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">Sign In to PAAUMENTOR</button>
        </form>
        <p style="font-size:0.85rem;color:var(--text-3);text-align:center;margin-top:16px">Don't have an account? <a href="{{ route('register') }}" style="color:var(--blue-500);font-weight:600">Create one free</a></p>
      </div>
    </div>
  </div>
</div>
<script src="{{ asset('js/app.js') }}"></script>
<script>
function togglePw(id){ const e=document.getElementById(id); e.type=e.type==='password'?'text':'password'; }
</script>
@include('partials.mentor-ai-widget')
</body>
</html>
