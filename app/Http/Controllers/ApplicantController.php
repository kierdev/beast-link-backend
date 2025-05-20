<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdmissionResultMail;

class ApplicantController extends Controller
{
    public function generateLetter(Applicant $applicant)
    {
        $pdf = Pdf::loadView('letters.result', ['applicant' => $applicant]);
        $filename = "letters/letter_{$applicant->id}.pdf";
        Storage::disk('public')->put($filename, $pdf->output());

        $applicant->update([
            'letter_path' => $filename,
            'letter_status' => 'GENERATED',
        ]);

        return response()->json(['message' => 'Letter generated', 'path' => $filename]);
    }

    public function sendLetter(Applicant $applicant)
    {
        $filePath = "public/letters/letter_{$applicant->id}.pdf";

        if (!Storage::exists($filePath)) {
            return response()->json([
                'message' => 'PDF letter not found. Please generate it first.'
            ], 404);
        }

        Mail::to($applicant->email)->send(new AdmissionResultMail($applicant));
        $applicant->update(['letter_status' => 'SENT']);
        return response()->json(['message' => 'Letter sent successfully']);
    }

}
