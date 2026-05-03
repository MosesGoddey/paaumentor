<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\{QRCode, QROptions};
use chillerlan\QRCode\Output\QRStringJSON;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $certificates = $user->certificates()
                             ->with('learningPath.mentor', 'learningPath.mentee')
                             ->latest('issued_at')
                             ->get();

        return view('certificates.index', compact('user', 'certificates'));
    }

    public function download(Certificate $certificate)
    {
        abort_unless(
            $certificate->user_id === Auth::id() || Auth::user()->isAdmin(),
            403
        );

        $certificate->load(['user', 'learningPath.mentor', 'learningPath.mentee']);

        $verifyUrl = route('certificates.verify', $certificate->certificate_id);
        $qrCode    = $this->generateQr($verifyUrl);

        $rawLogoPath = public_path('images/paau-logo.png');
        $hasLogo     = file_exists($rawLogoPath);
        $logoPath    = $hasLogo
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($rawLogoPath))
            : null;

        $pdf = Pdf::loadView('certificates.pdf', compact('certificate', 'qrCode', 'logoPath', 'hasLogo'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download($certificate->certificate_id . '.pdf');
    }

    public function verify(string $certificateId)
    {
        $certificate = Certificate::where('certificate_id', $certificateId)
                                  ->with(['user', 'learningPath.mentor'])
                                  ->first();

        return view('certificates.verify', compact('certificate', 'certificateId'));
    }

    private function generateQr(string $data): ?string
    {
        try {
            $options = new QROptions;
            $options->outputInterface  = QRStringJSON::class;
            $options->quietzoneSize    = 2;
            $options->drawLightModules = true;
            $options->markupDark       = '#000000';
            $options->markupLight      = '#ffffff';

            $decoded = json_decode((new QRCode($options))->render($data), true);
            $size    = $decoded['qrcode']['matrix']['width'];

            // Build full grid defaulting to white
            $grid = array_fill(0, $size, array_fill(0, $size, false));
            foreach ($decoded['qrcode']['matrix']['rows'] as $row) {
                foreach ($row['modules'] as $mod) {
                    $grid[$row['y']][$mod['x']] = $mod['dark'];
                }
            }

            $cell  = 2; // px per module
            $td    = '<td style="width:%dpx;height:%dpx;background-color:%s;padding:0;border:0;font-size:0;line-height:0"></td>';
            $html  = '<table cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;line-height:0;font-size:0;margin:0 auto">';
            foreach ($grid as $row) {
                $html .= '<tr>';
                foreach ($row as $dark) {
                    $html .= sprintf($td, $cell, $cell, $dark ? '#000000' : '#ffffff');
                }
                $html .= '</tr>';
            }
            $html .= '</table>';

            return $html;
        } catch (\Throwable) {
            return null;
        }
    }
}
