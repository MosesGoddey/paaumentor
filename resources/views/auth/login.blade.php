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
  filter:blur(3px) brightness(0.45);
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
.auth-tabs{display:flex;gap:0;background:var(--surface-2);border-radius:12px;padding:4px;margin-bottom:32px;}
.auth-tab{flex:1;padding:10px;border-radius:9px;border:none;background:transparent;cursor:pointer;font-family:'Sora',sans-serif;font-weight:600;font-size:0.88rem;color:var(--text-3);transition:all 0.2s;}
.auth-tab.active{background:var(--surface);color:var(--blue-500);box-shadow:0 2px 8px rgba(0,0,0,0.08);}
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
      <div class="auth-tabs">
        <button class="auth-tab active" id="tabLogin" onclick="switchTab('login')">Sign In</button>
        <button class="auth-tab" id="tabRegister" onclick="switchTab('register')">Create Account</button>
      </div>

      @if($errors->any())
        <div style="background:#fee2e2;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:0.85rem;color:#991b1b">
          @foreach($errors->all() as $e) <div>✗ {{ $e }}</div> @endforeach
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
          <label style="display:flex;align-items:center;gap:8px;font-size:0.85rem;cursor:pointer">
            <input type="checkbox" name="remember" style="accent-color:var(--blue-500)"> Remember me for 30 days
          </label>
          <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">Sign In to PAAUMENTOR</button>
        </form>
        <p style="font-size:0.85rem;color:var(--text-3);text-align:center;margin-top:16px">Don't have an account? <a onclick="switchTab('register')" style="color:var(--blue-500);font-weight:600;cursor:pointer">Create one free →</a></p>
      </div>

      <div id="registerPanel" style="display:none">
        <h2 style="font-size:1.7rem;font-weight:800;margin-bottom:6px">Join PAAUMENTOR</h2>
        <p style="font-size:0.88rem;color:var(--text-3);margin-bottom:28px">Create your free account — takes less than 2 minutes.</p>
        <form method="POST" action="{{ route('register') }}" style="display:flex;flex-direction:column;gap:16px">
          @csrf
          <div class="form-group">
            <label class="form-label">I want to join as</label>
            <div class="role-select">
              <div><input type="radio" name="role" id="roleMentee" class="role-option" value="mentee" checked onchange="updateRoleDesc()"><label for="roleMentee" class="role-label">Mentee</label></div>
              <div><input type="radio" name="role" id="roleMentor" class="role-option" value="mentor" onchange="updateRoleDesc()"><label for="roleMentor" class="role-label">Mentor</label></div>
              <div><input type="radio" name="role" id="roleAlumni" class="role-option" value="alumni" onchange="updateRoleDesc()"><label for="roleAlumni" class="role-label">Alumni</label></div>
            </div>
            <p id="roleDesc" style="font-size:0.8rem;color:var(--text-3);margin-top:8px;padding:8px 12px;background:var(--surface-2);border-radius:8px">You are a student looking for guidance from senior peers or alumni.</p>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div class="form-group"><label class="form-label">First Name</label><input type="text" name="first_name" class="form-input" placeholder="Moses" value="{{ old('first_name') }}" required></div>
            <div class="form-group"><label class="form-label">Last Name</label><input type="text" name="last_name" class="form-input" placeholder="Joseph" value="{{ old('last_name') }}" required></div>
          </div>
          <div class="form-group"><label class="form-label">Student / Matric Number</label><input type="text" name="student_id" class="form-input" placeholder="e.g. 23CS1004" value="{{ old('student_id') }}" required></div>
          <div class="form-group"><label class="form-label">Institutional Email</label><input type="email" name="email" class="form-input" placeholder="you@paau.edu.ng" value="{{ old('email') }}" required></div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div class="form-group"><label class="form-label">Department</label><select name="department" class="form-select" required><option>Computer Science</option><option>Mathematics</option><option>Physics</option><option>Statistics</option></select></div>
            <div class="form-group"><label class="form-label">Level</label><select name="level" class="form-select" required><option>100L</option><option>200L</option><option>300L</option><option>400L</option><option>500L</option><option>Alumni</option></select></div>
          </div>

          {{-- Mentor portfolio section (shown when mentor/alumni role selected) --}}
          <div id="mentorPortfolio" style="display:none;flex-direction:column;gap:12px;padding:16px;background:var(--surface-2);border-radius:14px;border:1.5px solid #fed7aa">
            <div style="font-size:0.82rem;font-weight:700;color:#c2410c;display:flex;align-items:center;gap:6px">
              📋 Mentor Portfolio
              <span style="font-size:0.72rem;font-weight:500;color:var(--text-3)">Required for mentor accounts</span>
            </div>
            <div class="form-group" style="margin:0">
              <label class="form-label">GitHub Profile URL</label>
              <input type="url" name="github_url" class="form-input" placeholder="https://github.com/yourusername" value="{{ old('github_url') }}">
            </div>
            <div class="form-group" style="margin:0">
              <label class="form-label">LinkedIn Profile URL</label>
              <input type="url" name="linkedin_url" class="form-input" placeholder="https://linkedin.com/in/yourprofile" value="{{ old('linkedin_url') }}">
            </div>
            <div class="form-group" style="margin:0">
              <label class="form-label">What can you teach? <span style="font-weight:400;color:var(--text-3)">(portfolio / projects / skills)</span></label>
              <textarea name="bio" class="form-input" rows="3" placeholder="Describe your skills, completed projects, and the topics you can mentor on..." style="resize:vertical">{{ old('bio') }}</textarea>
            </div>
            <p style="font-size:0.75rem;color:#92400e;margin:0;padding:8px 10px;background:#fff7ed;border-radius:8px;border:1px solid #fed7aa">
              ⚠️ Your mentor account will be <strong>pending verification</strong> until a verifier reviews your portfolio. You'll be notified once approved.
            </p>
          </div>

          <div class="form-group"><label class="form-label">Password</label><div class="input-wrap"><input type="password" name="password" id="regPw" class="form-input" placeholder="Minimum 8 characters" required><button type="button" onclick="togglePw('regPw')" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:0.78rem;color:var(--text-3);font-weight:600">Show</button></div></div>
          <input type="password" name="password_confirmation" class="form-input" placeholder="Confirm password" required>
          <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">Create My Account</button>
        </form>
        <p style="font-size:0.85rem;color:var(--text-3);text-align:center;margin-top:16px">Already have an account? <a onclick="switchTab('login')" style="color:var(--blue-500);font-weight:600;cursor:pointer">Sign in →</a></p>
      </div>
    </div>
  </div>
</div>
<script src="{{ asset('js/app.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  switchTab('{{ $startTab ?? 'login' }}');
  updateRoleDesc();
});
function switchTab(t){
  document.getElementById('loginPanel').style.display    = t==='login'    ? '' : 'none';
  document.getElementById('registerPanel').style.display = t==='register' ? '' : 'none';
  document.getElementById('tabLogin').classList.toggle('active',    t==='login');
  document.getElementById('tabRegister').classList.toggle('active', t==='register');
}
function togglePw(id){ const e=document.getElementById(id); e.type=e.type==='password'?'text':'password'; }
function updateRoleDesc(){
  const descs = {
    mentee: 'You are a student looking for guidance from senior peers or alumni.',
    mentor: 'You are a senior student ready to guide and support junior mentees.',
    alumni: 'You are a graduate who wants to give back by mentoring current students.'
  };
  const role = document.querySelector('input[name="role"]:checked')?.value;
  document.getElementById('roleDesc').textContent = descs[role] || '';
  const portfolio = document.getElementById('mentorPortfolio');
  const isMentorRole = role === 'mentor' || role === 'alumni';
  portfolio.style.display = isMentorRole ? 'flex' : 'none';
}
</script>
</body>
</html>
