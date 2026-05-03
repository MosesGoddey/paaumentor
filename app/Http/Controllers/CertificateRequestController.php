<?php

namespace App\Http\Controllers;

use App\Models\{CertificateRequest, Notification, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateRequestController extends Controller
{
    // Show the reflection form to the mentor
    public function showReflect(CertificateRequest $certRequest)
    {
        abort_unless(Auth::id() === $certRequest->mentor_id, 403);
        abort_unless($certRequest->isPendingMentorReflection(), 422);

        return view('certificate-requests.reflect', compact('certRequest'));
    }

    // Mentor submits their reflection
    public function submitReflect(Request $request, CertificateRequest $certRequest)
    {
        abort_unless(Auth::id() === $certRequest->mentor_id, 403);
        abort_unless($certRequest->isPendingMentorReflection(), 422);

        $data = $request->validate([
            'mentor_reflection' => 'required|string|min:80|max:2000',
        ]);

        $certRequest->update([
            'status'                         => 'pending_verifier',
            'mentor_reflection'              => $data['mentor_reflection'],
            'mentor_reflection_submitted_at' => now(),
        ]);

        // Notify verifiers and admins
        User::whereIn('role', ['admin', 'verifier'])->each(function ($v) use ($certRequest) {
            Notification::create([
                'user_id' => $v->id,
                'type'    => 'certificate_pending_review',
                'title'   => 'Certificate Request Ready for Review',
                'body'    => "{$certRequest->mentee->full_name}'s certificate for \"{$certRequest->learningPath->title}\" is ready. Assessment score: {$certRequest->assessment_score}%. Mentor reflection submitted.",
                'data'    => ['certificate_request_id' => $certRequest->id],
            ]);
        });

        // Notify the mentee
        Notification::create([
            'user_id' => $certRequest->mentee_id,
            'type'    => 'certificate_pending_verifier',
            'title'   => 'Certificate Under Verifier Review',
            'body'    => "Your mentor has submitted their reflection for \"{$certRequest->learningPath->title}\". A verifier is now reviewing your certificate request.",
            'data'    => ['certificate_request_id' => $certRequest->id],
        ]);

        return redirect()->route('learning.index')
            ->with('success', 'Reflection submitted. The verifier will now review the certificate request.');
    }
}
