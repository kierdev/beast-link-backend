<?php

namespace App\Jobs;

use App\Models\Applicant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdmissionResultMail;

class SendAdmissionLetterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $applicant;

    public function __construct(Applicant $applicant)
    {
        $this->applicant = $applicant;
    }

    public function handle(): void
    {
        Mail::to($this->applicant->email)
            ->send(new AdmissionResultMail($this->applicant));

        $this->applicant->update(['letter_status' => 'SENT']);
    }
}


