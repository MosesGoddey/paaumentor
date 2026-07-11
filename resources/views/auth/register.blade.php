<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account — PAAUMENTOR</title>
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
      <h2 style="font-size:2.4rem;font-weight:800;color:#fff;line-height:1.2;margin-bottom:16px">Join your <span style="color:#fcd34d">mentorship journey</span> today.</h2>
      <p style="color:rgba(255,255,255,0.7);font-size:0.95rem;line-height:1.7;margin-bottom:36px">Create your account to get started with PAAU's official peer mentorship platform — connect with mentors and grow together.</p>
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
          @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
        </div>
      @endif

      <h2 style="font-size:1.7rem;font-weight:800;margin-bottom:6px">Create Account</h2>
      <p style="font-size:0.88rem;color:var(--text-3);margin-bottom:24px">Fill in the details below to get started on PAAUMENTOR.</p>

      <form method="POST" action="{{ route('register') }}" style="display:flex;flex-direction:column;gap:14px">
        @csrf

        <div class="form-group">
          <label class="form-label">First Name</label>
          <input type="text" name="first_name" class="form-input" placeholder="John" value="{{ old('first_name') }}" required>
        </div>

        <div class="form-group">
          <label class="form-label">Last Name</label>
          <input type="text" name="last_name" class="form-input" placeholder="Doe" value="{{ old('last_name') }}" required>
        </div>

        <div class="form-group">
          <label class="form-label">Student ID</label>
          <input type="text" name="student_id" class="form-input" placeholder="e.g. 23CS1004" value="{{ old('student_id') }}" required>
        </div>

        <div class="form-group">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" class="form-input" placeholder="you@paau.edu.ng" value="{{ old('email') }}" required>
        </div>

        <div class="form-group">
          <label class="form-label">Department</label>
          <select name="department" class="form-select" required>
            <option value="">Select your department</option>
            <option value="Computer Science" {{ old('department') === 'Computer Science' ? 'selected' : '' }}>Computer Science</option>
            <option value="Mathematics" {{ old('department') === 'Mathematics' ? 'selected' : '' }}>Mathematics</option>
            <option value="Physics" {{ old('department') === 'Physics' ? 'selected' : '' }}>Physics</option>
            <option value="Chemistry" {{ old('department') === 'Chemistry' ? 'selected' : '' }}>Chemistry</option>
            <option value="Biology" {{ old('department') === 'Biology' ? 'selected' : '' }}>Biology</option>
            <option value="Engineering" {{ old('department') === 'Engineering' ? 'selected' : '' }}>Engineering</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Level</label>
          <select name="level" class="form-select" required>
            <option value="">Select your level</option>
            <option value="100" {{ old('level') === '100' ? 'selected' : '' }}>100L</option>
            <option value="200" {{ old('level') === '200' ? 'selected' : '' }}>200L</option>
            <option value="300" {{ old('level') === '300' ? 'selected' : '' }}>300L</option>
            <option value="400" {{ old('level') === '400' ? 'selected' : '' }}>400L</option>
            <option value="500" {{ old('level') === '500' ? 'selected' : '' }}>500L</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">I want to join as</label>
          <select name="role" class="form-select" required>
            <option value="">Select your role</option>
            <option value="mentee" {{ old('role') === 'mentee' ? 'selected' : '' }}>Mentee (Learning)</option>
            <option value="mentor" {{ old('role') === 'mentor' ? 'selected' : '' }}>Mentor (Teaching)</option>
            <option value="alumni" {{ old('role') === 'alumni' ? 'selected' : '' }}>Alumni (Teaching)</option>
          </select>
          <p style="font-size:0.75rem;color:var(--text-3);margin-top:6px">Mentors and alumni accounts require portfolio verification</p>
        </div>

        {{-- Portfolio fields — shown only for Mentor / Alumni --}}
        <div id="portfolioFields" style="display:{{ in_array(old('role'), ['mentor','alumni']) ? 'flex' : 'none' }};flex-direction:column;gap:14px;background:var(--blue-100);border:1px solid var(--border);border-radius:12px;padding:16px;margin-top:2px">
          <div style="display:flex;align-items:center;gap:8px">
            <span style="font-size:0.82rem;font-weight:700;color:var(--blue-500)">Your Portfolio</span>
            <span style="font-size:0.72rem;color:var(--text-3)">Reviewed by a verifier before approval</span>
          </div>

          <div class="form-group">
            <label class="form-label">GitHub Profile</label>
            <input type="url" name="github_url" class="form-input" placeholder="https://github.com/yourusername" value="{{ old('github_url') }}">
          </div>

          <div class="form-group">
            <label class="form-label">LinkedIn Profile</label>
            <input type="url" name="linkedin_url" class="form-input" placeholder="https://linkedin.com/in/yourusername" value="{{ old('linkedin_url') }}">
          </div>

          <div class="form-group">
            <label class="form-label">Short Bio</label>
            <textarea name="bio" class="form-input" rows="3" placeholder="Tell us about your experience, skills, and what you can mentor others in.">{{ old('bio') }}</textarea>
            <p style="font-size:0.72rem;color:var(--text-3);margin-top:4px">Describe your expertise — this helps verifiers assess your application.</p>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Password</label>
          <div class="input-wrap">
            <input type="password" name="password" id="regPw" class="form-input" placeholder="Create a strong password" required>
            <button type="button" onclick="togglePw('regPw')" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:0.78rem;color:var(--text-3);font-weight:600">Show</button>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Confirm Password</label>
          <div class="input-wrap">
            <input type="password" name="password_confirmation" id="regPwConf" class="form-input" placeholder="Confirm your password" required>
            <button type="button" onclick="togglePw('regPwConf')" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:0.78rem;color:var(--text-3);font-weight:600">Show</button>
          </div>
        </div>

        <label style="display:flex;align-items:flex-start;gap:8px;font-size:0.82rem;cursor:pointer;margin-top:4px">
          <input type="checkbox" style="margin-top:3px;accent-color:var(--blue-500)">
          <span>I agree to the <a href="#" style="color:var(--blue-500);font-weight:600">Terms of Service</a> and <a href="#" style="color:var(--blue-500);font-weight:600">Privacy Policy</a></span>
        </label>

        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:8px">Create My Account</button>
      </form>

      <p style="font-size:0.85rem;color:var(--text-3);text-align:center;margin-top:16px">Already have an account? <a href="{{ route('login') }}" style="color:var(--blue-500);font-weight:600">Sign in here</a></p>
    </div>
  </div>
</div>

<script src="{{ asset('js/app.js') }}"></script>
<script>
function togglePw(id) {
  const el = document.getElementById(id);
  const btn = event.target;
  if (el.type === 'password') {
    el.type = 'text';
    btn.textContent = 'Hide';
  } else {
    el.type = 'password';
    btn.textContent = 'Show';
  }
}

// Show portfolio fields only when registering as mentor or alumni
(function () {
  const roleSelect = document.querySelector('select[name="role"]');
  const portfolio  = document.getElementById('portfolioFields');
  if (!roleSelect || !portfolio) return;

  roleSelect.addEventListener('change', function () {
    const needsPortfolio = this.value === 'mentor' || this.value === 'alumni';
    portfolio.style.display = needsPortfolio ? 'flex' : 'none';
  });
})();
</script>
@include('partials.mentor-ai-widget')
</body>
</html>
