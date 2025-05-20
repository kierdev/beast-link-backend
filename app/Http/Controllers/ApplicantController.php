<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\AdmissionResult;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdmissionResultMail;

class ApplicantController extends Controller
{
    /// 1) Generate & store the PDF letter
    public function generateLetter(Applicant $applicant)
    {
        // 1) eager‐load application + program
        $applicant->load('application.program');
        $application = $applicant->application;
        $program = $application->program;

        // 2) normalize the status
        $raw = strtolower($application->status);
        $admissionStatus = in_array($raw, ['approved', 'passed'])
            ? 'PASSED'
            : 'FAILED';

        // 3) save it and grab the Eloquent model back
        $admission = AdmissionResult::updateOrCreate(
            ['applicant_id' => $applicant->applicant_id],
            [
                'program_id' => $program->program_id,
                'admission_status' => $admissionStatus,
                'letter_path' => "letters/letter_{$applicant->applicant_id}.pdf",
                'letter_status' => 'GENERATED',
                'sent_at' => null,
            ]
        );

        // 4) generate the PDF **after** we know admission_status
        $pdf = Pdf::loadView('letters.result', [
            'applicant' => $applicant,
            'application' => $application,
            'admission' => $admission,         // <<< new
        ]);

        // 5) store the PDF
        Storage::disk('public')
            ->put($admission->letter_path, $pdf->output());

        return response()->json([
            'message' => 'Letter generated',
            'path' => $admission->letter_path,
        ]);
    }


    /// 2) Send that PDF as an email attachment
    public function sendLetter(Applicant $applicant)
    {
        // a) fetch the admission_result record
        $admission = AdmissionResult::where('applicant_id', $applicant->applicant_id)
            ->first();

        if (
            !$admission ||
            !Storage::disk('public')->exists($admission->letter_path)
        ) {
            return response()->json([
                'message' => 'PDF letter not found. Generate it first.'
            ], 404);
        }

        // b) fire off the Mailable (attaches the PDF)
        Mail::to($applicant->Email)
            ->send(new AdmissionResultMail($admission));

        // c) mark it “SENT” and timestamp it
        $admission->update([
            'letter_status' => 'SENT',
            'sent_at' => now(),
        ]);

        return response()->json(['message' => 'Letter sent successfully']);
    }
}
