@component('mail::message')
# Admission Result

Dear {{ $applicant->name }},

We are writing to inform you that you have **{{ $applicant->status }}** the entrance examination for the {{ $applicant->course }} program.

Please download and print the attached letter for your records.

Regards,  
**Beastlink University**
@endcomponent