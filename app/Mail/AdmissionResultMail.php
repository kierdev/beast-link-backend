<?php

namespace App\Mail;

use App\Models\AdmissionResult;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdmissionResultMail extends Mailable
{
    use Queueable, SerializesModels;

    /** @var AdmissionResult */
    public $admission;

    public function __construct(AdmissionResult $admission)
    {
        $this->admission = $admission;
    }

    public function build(): self
    {
        // Grab the AdmissionResult, Applicant, and Program
        $admission = $this->admission;                  // AdmissionResult
        $applicant = $admission->applicant;             // Applicant
        $program = $admission->program;         
        $downloadUrl = asset('storage/' . $admission->letter_path);      // Program
        $filePath = storage_path('app/public/' . $admission->letter_path);
        
        if (!file_exists($filePath)) {
            throw new \Exception("PDF not found at {$filePath}");
        }

        return $this
            ->subject('Your Admission Result – BeastLink University')
            ->markdown('emails.admission_result', [
                'admission' => $admission,
                'applicant' => $applicant,
                'program' => $admission->program,
                'downloadUrl' => $downloadUrl,  // <<< pass this in
            ])
            ->attach($filePath, [
                'as' => "Admission_Result_{$applicant->applicant_id}.pdf",
                'mime' => 'application/pdf',
            ]);
    }

}


// class AdmissionResultMail extends Mailable
// {
//     public $applicant;
//     public $filePath;

//     public function __construct(Applicant $applicant)
//     {
//         $this->applicant = $applicant;
//         $this->filePath = storage_path("app/{$applicant->letter_path}");
//     }

//     public function build()
//     {
//         return $this->subject('Admission Result')
//                     ->view('emails.admission_result')
//                     ->attach($this->filePath, [
//                         'as' => "letter_{$this->applicant->id}.pdf",
//                         'mime' => 'application/pdf',
//                     ]);
//     }
// }
