<?php

namespace App\Mail;

use App\Models\Applicant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdmissionResultMail extends Mailable
{
    use Queueable, SerializesModels;

    public $applicant;

    public function __construct(Applicant $applicant)
    {
        $this->applicant = $applicant;
    }

    public function build(): self
    {
        $filePath = storage_path("app/public/{$this->applicant->letter_path}");
        
        if (!file_exists($filePath)) {

            throw new \Exception("PDF file does not exist: {$filePath}");
        }

        return $this->subject('Your Admission Result - Beastlink University')
                    ->markdown('emails.admission_result')
                    ->attach($filePath, [
                        'as' => "letter_{$this->applicant->id}.pdf", 
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
