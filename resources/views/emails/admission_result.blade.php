@component('mail::message')
# College Admission Result Status Update

Dear {{ $admission->applicant->First_Name }}
    @if($admission->applicant->Middle_Name) {{ $admission->applicant->Middle_Name }} @endif
    {{ $admission->applicant->Last_Name }}{{ $admission->applicant->Name_Extension ? ' '.$admission->applicant->Name_Extension : '' }},

We hope this email finds you well. We are reaching out to inform you that there has been an update to your admission application status at **BeastLink University**.

**Application ID:** {{ optional($admission->applicant->application)->application_id ?? '—' }}  
**Updated Status:** {{ $admission->admission_status }}  
**Details:** 
@if($admission->admission_status === 'PASSED')
Congratulations! You have passed the entrance examination for the {{ $admission->program->program_code }} – {{ $admission->program->program_name }} program. 
@else
We regret to inform you that your application was not successful at this time.
@endif

You may download your admission result letter from the link below:

@component('mail::button', ['url' => $downloadUrl])
Download Your Admission Letter
@endcomponent

If you have any questions or need assistance, feel free to contact us at [beastlinkuniversity@gmail.com](mailto:beastlinkuniversity@gmail.com) or visit our admissions office.

Best regards,  
**BeastLink University Admissions Team**

📞 +1 234 567 890 | 📧 [beastlinkuniversity@gmail.com](mailto:beastlinkuniversity@gmail.com) | 🌐 https://beastlinkuniversity.com
@endcomponent
