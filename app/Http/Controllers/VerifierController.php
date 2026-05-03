<?php

namespace App\Http\Controllers;

use App\Models\{User, Notification, CertificateRequest, Certificate, MentorSession, Mentorship};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifierController extends Controller
{
    public function index()
    {
        abort_unless(Auth::user()->isAdmin() || Auth::user()->isVerifier(), 403);

        $pendingMentors = User::whereIn('role', ['mentor', 'alumni'])
            ->where('mentor_status', 'pending')
            ->latest()
            ->get();

        $pendingCertRequests = CertificateRequest::where('status', 'pending_verifier')
            ->with(['mentee', 'mentor', 'learningPath'])
            ->latest()
            ->get();

        return view('verifier.index', compact('pendingMentors', 'pendingCertRequests'));
    }

    // ── Mentor portfolio approval ──────────────────────────────────

    public function approve(User $user)
    {
        abort_unless(Auth::user()->isAdmin() || Auth::user()->isVerifier(), 403);
        abort_unless($user->isPendingVerification(), 422);

        $user->update(['mentor_status' => 'active']);

        Notification::create([
            'user_id' => $user->id,
            'type'    => 'mentor_portfolio_approved',
            'title'   => 'Portfolio Verified! 🎉',
            'body'    => 'Your mentor portfolio has been reviewed and approved. Your account is now active — you can start accepting mentees.',
            'data'    => [],
        ]);

        return back()->with('success', "{$user->full_name}'s mentor account is now active.");
    }

    public function reject(Request $request, User $user)
    {
        abort_unless(Auth::user()->isAdmin() || Auth::user()->isVerifier(), 403);
        abort_unless($user->isPendingVerification(), 422);

        $data = $request->validate([
            'reason' => 'required|string|min:10|max:500',
        ]);

        Notification::create([
            'user_id' => $user->id,
            'type'    => 'mentor_portfolio_rejected',
            'title'   => 'Portfolio Review Update',
            'body'    => "Your mentor portfolio was not approved at this time. Reason: {$data['reason']}. Please update your GitHub, LinkedIn, or portfolio description and contact a verifier.",
            'data'    => ['reason' => $data['reason']],
        ]);

        return back()->with('success', "Rejection note sent to {$user->full_name}.");
    }

    // ── Certificate request approval ───────────────────────────────

    public function approveCert(Request $request, CertificateRequest $certRequest)
    {
        abort_unless(Auth::user()->isAdmin() || Auth::user()->isVerifier(), 403);
        abort_unless($certRequest->isPendingVerifier(), 422);

        // Gate 1: mentor must have submitted a reflection
        if (!$certRequest->hasReflection()) {
            return back()->with('error', 'Cannot approve: the mentor has not submitted their reflection yet.');
        }

        // Gate 2: at least 3 completed sessions between this mentor and mentee
        $mentorship = Mentorship::where('mentor_id', $certRequest->mentor_id)
            ->where('mentee_id', $certRequest->mentee_id)
            ->first();

        $sessionCount = $mentorship
            ? MentorSession::where('mentorship_id', $mentorship->id)
                ->where('status', 'completed')
                ->count()
            : 0;

        if ($sessionCount < 3) {
            return back()->with('error', "Cannot approve: only {$sessionCount}/3 required sessions completed between this mentor and mentee.");
        }

        $data = $request->validate([
            'verifier_note' => 'nullable|string|max:500',
        ]);

        $certRequest->update([
            'status'        => 'approved',
            'verifier_id'   => Auth::id(),
            'verifier_note' => $data['verifier_note'] ?? null,
            'verified_at'   => now(),
        ]);

        $this->issueCertificate($certRequest);

        return back()->with('success', "Certificate approved and issued to {$certRequest->mentee->full_name}.");
    }

    public function rejectCert(Request $request, CertificateRequest $certRequest)
    {
        abort_unless(Auth::user()->isAdmin() || Auth::user()->isVerifier(), 403);
        abort_unless($certRequest->isPendingVerifier(), 422);

        $data = $request->validate([
            'verifier_note' => 'required|string|min:10|max:500',
        ]);

        $certRequest->update([
            'status'        => 'rejected',
            'verifier_id'   => Auth::id(),
            'verifier_note' => $data['verifier_note'],
            'verified_at'   => now(),
        ]);

        Notification::create([
            'user_id' => $certRequest->mentee_id,
            'type'    => 'certificate_rejected',
            'title'   => 'Certificate Request Update',
            'body'    => "Your certificate for \"{$certRequest->learningPath->title}\" was not approved at this time. Reason: {$data['verifier_note']}. Please contact your mentor.",
            'data'    => ['certificate_request_id' => $certRequest->id],
        ]);

        return back()->with('success', "Certificate request rejected. Mentee has been notified.");
    }

    // ── Private: issue actual certificate records ──────────────────

    private function issueCertificate(CertificateRequest $certRequest): void
    {
        $path   = $certRequest->learningPath;
        $mentee = $certRequest->mentee;

        if ($path->certificates()->where('type', 'mentee')->exists()) return;

        $year         = date('Y');
        $padded       = str_pad($path->id, 5, '0', STR_PAD_LEFT);
        $menteeCertId = 'PAAU-' . $year . '-' . $padded;
        $mentorCertId = 'PAAU-M-' . $year . '-' . $padded;

        Certificate::create([
            'user_id'          => $mentee->id,
            'learning_path_id' => $path->id,
            'type'             => 'mentee',
            'certificate_id'   => $menteeCertId,
            'issued_at'        => now(),
        ]);

        Certificate::create([
            'user_id'          => $path->mentor_id,
            'learning_path_id' => $path->id,
            'type'             => 'mentor',
            'certificate_id'   => $mentorCertId,
            'issued_at'        => now(),
        ]);

        $path->update(['status' => 'completed']);

        Notification::create([
            'user_id' => $mentee->id,
            'type'    => 'certificate_issued',
            'title'   => 'Certificate Issued! 🏆',
            'body'    => "Congratulations! Your verified certificate for \"{$path->title}\" is ready to download.",
            'data'    => ['certificate_id' => $menteeCertId],
        ]);

        Notification::create([
            'user_id' => $path->mentor_id,
            'type'    => 'certificate_issued',
            'title'   => 'Mentorship Certificate Issued!',
            'body'    => "{$mentee->full_name} completed \"{$path->title}\" and was verified. Your Certificate of Mentorship is ready.",
            'data'    => ['certificate_id' => $mentorCertId],
        ]);
    }
}
