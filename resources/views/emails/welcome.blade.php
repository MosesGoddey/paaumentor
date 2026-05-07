<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Welcome to PAAUMENTOR</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:'Segoe UI',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:40px 0">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08)">

      {{-- Header --}}
      <tr>
        <td style="background:linear-gradient(135deg,#1e3a8a,#2563eb);padding:40px 48px;text-align:center">
          <div style="display:inline-block;width:52px;height:52px;background:rgba(255,255,255,0.15);border-radius:14px;text-align:center;line-height:52px;font-family:'Segoe UI',Arial,sans-serif;font-weight:800;color:#fff;font-size:1.2rem;border:1px solid rgba(255,255,255,0.2);margin-bottom:16px">PM</div>
          <div style="font-family:'Segoe UI',Arial,sans-serif;font-weight:700;color:#fff;font-size:1.4rem;letter-spacing:0.02em">PAAU<span style="color:#f5a623">MENTOR</span></div>
          <p style="color:rgba(255,255,255,0.7);font-size:0.85rem;margin:6px 0 0">Prince Abubakar Audu University, Anyigba</p>
        </td>
      </tr>

      {{-- Body --}}
      <tr>
        <td style="padding:48px 48px 32px">
          <p style="font-size:1.5rem;font-weight:800;color:#0f172a;margin:0 0 8px">Welcome, {{ $user->first_name }}! 🎉</p>
          <p style="font-size:0.95rem;color:#475569;line-height:1.7;margin:0 0 28px">
            Your PAAUMENTOR account has been created successfully. You're now part of a peer mentorship community built specifically for students at Prince Abubakar Audu University.
          </p>

          {{-- Account details card --}}
          <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:14px;border:1px solid #e2e8f0;margin-bottom:28px">
            <tr><td style="padding:24px 28px">
              <p style="font-size:0.78rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#94a3b8;margin:0 0 16px">Your Account Details</p>
              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td style="padding:6px 0;font-size:0.88rem;color:#64748b;width:140px">Full Name</td>
                  <td style="padding:6px 0;font-size:0.88rem;color:#0f172a;font-weight:700">{{ $user->full_name }}</td>
                </tr>
                <tr>
                  <td style="padding:6px 0;font-size:0.88rem;color:#64748b">Student ID</td>
                  <td style="padding:6px 0;font-size:0.88rem;color:#0f172a;font-weight:700">{{ $user->student_id }}</td>
                </tr>
                <tr>
                  <td style="padding:6px 0;font-size:0.88rem;color:#64748b">Role</td>
                  <td style="padding:6px 0;font-size:0.88rem;color:#0f172a;font-weight:700">{{ ucfirst($user->role) }}</td>
                </tr>
                <tr>
                  <td style="padding:6px 0;font-size:0.88rem;color:#64748b">Department</td>
                  <td style="padding:6px 0;font-size:0.88rem;color:#0f172a;font-weight:700">{{ $user->department }}</td>
                </tr>
              </table>
            </td></tr>
          </table>

          {{-- What's next --}}
          <p style="font-size:0.78rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#94a3b8;margin:0 0 14px">What You Can Do</p>
          @if($user->isMentee())
          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px">
            @foreach([['🔍','Find a Mentor','Browse verified mentors and request one that matches your goals.'],['📚','Join a Learning Path','Follow structured paths with tasks graded by your mentor.'],['🏆','Earn Certificates','Complete paths, pass assessments, and get QR-verifiable certificates.'],['✨','Use the AI Study Buddy','Ask your AI assistant anything about your academics at PAAU.']] as [$icon,$title,$desc])
            <tr><td style="padding:8px 0;vertical-align:top">
              <table cellpadding="0" cellspacing="0"><tr>
                <td style="width:36px;height:36px;background:#eff6ff;border-radius:10px;text-align:center;vertical-align:middle;font-size:1rem">{{ $icon }}</td>
                <td style="padding-left:12px;vertical-align:middle">
                  <div style="font-size:0.88rem;font-weight:700;color:#0f172a">{{ $title }}</div>
                  <div style="font-size:0.8rem;color:#64748b;margin-top:2px">{{ $desc }}</div>
                </td>
              </tr></table>
            </td></tr>
            @endforeach
          </table>
          @else
          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px">
            @foreach([['🎓','Guide Your Mentees','Create learning paths, assign tasks, and grade submissions.'],['📅','Schedule Sessions','Conduct video, voice, or chat sessions with your mentees.'],['🏆','Earn Milestone Certificates','Get recognised for every 3 mentees you guide to completion.'],['📊','Track Progress','Monitor your mentees\' performance with charts and analytics.']] as [$icon,$title,$desc])
            <tr><td style="padding:8px 0;vertical-align:top">
              <table cellpadding="0" cellspacing="0"><tr>
                <td style="width:36px;height:36px;background:#f0fdf4;border-radius:10px;text-align:center;vertical-align:middle;font-size:1rem">{{ $icon }}</td>
                <td style="padding-left:12px;vertical-align:middle">
                  <div style="font-size:0.88rem;font-weight:700;color:#0f172a">{{ $title }}</div>
                  <div style="font-size:0.8rem;color:#64748b;margin-top:2px">{{ $desc }}</div>
                </td>
              </tr></table>
            </td></tr>
            @endforeach
          </table>
          @endif

          {{-- CTA button --}}
          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px">
            <tr><td align="center">
              <a href="{{ route('dashboard') }}" style="display:inline-block;background:#2563eb;color:#ffffff;font-weight:700;font-size:0.95rem;padding:14px 36px;border-radius:12px;text-decoration:none;letter-spacing:0.01em">Go to My Dashboard →</a>
            </td></tr>
          </table>

          <p style="font-size:0.85rem;color:#94a3b8;line-height:1.7;margin:0">
            If you didn't create this account, you can safely ignore this email.
          </p>
        </td>
      </tr>

      {{-- Footer --}}
      <tr>
        <td style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:24px 48px;text-align:center">
          <p style="font-size:0.78rem;color:#94a3b8;margin:0">© {{ date('Y') }} PAAUMENTOR · Department of Computer Science, PAAU, Anyigba, Nigeria</p>
          <p style="font-size:0.78rem;color:#94a3b8;margin:6px 0 0">This email was sent to <strong>{{ $user->email }}</strong></p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html>
