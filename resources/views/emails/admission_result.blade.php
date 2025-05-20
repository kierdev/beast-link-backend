@component('mail::message')
# Admission Result

Dear {{ $applicant->First_Name }}
    @if($applicant->Middle_Name) {{ $applicant->Middle_Name }} @endif
    {{ $applicant->Last_Name }}{{ $applicant->Name_Extension ? ' '.$applicant->Name_Extension : '' }},

We are writing to inform you that you have **{{ $admission->admission_status }}** the entrance examination for the **{{ $program->program_code }} – {{ $program->program_name }}** program.

You can download and print the attached letter for your records.

Regards,  
**BeastLink University @AdmissionsOffice**
@endcomponent
