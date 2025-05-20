{{-- resources/views/letters/result.blade.php --}}
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admission Confirmation Letter</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 0; padding: 0 40px; color: #333; }
    header { background: #0b3d2e; padding: 20px 40px; display: flex; align-items: center; }
    header img { height: 50px; margin-right: 15px; }
    header h1 { font-size: 24px; color: #fff; margin: 0; font-weight: normal; }
    .meta { margin-top: 30px; line-height: 1.4; }
    .meta .date, .meta .subject, .meta .academic-year { margin-bottom: 5px; }
    h2 { margin-top: 40px; font-size: 20px; color: #0b3d2e; }
    p { margin: 15px 0; line-height: 1.6; }
    .signature { margin-top: 60px; }
    .signature p { margin: 2px 0; }
  </style>
</head>
<body>

  {{-- HEADER --}}
  <header>
    <img src="{{ public_path('images/logo.png') }}" alt="BeastLink Logo">
    <h1>BeastLink University</h1>
  </header>

  {{-- TITLE --}}
  <h2>Admission Confirmation Letter</h2>

  {{-- META --}}
  <div class="meta">
    <div class="date"><strong>Date:</strong> {{ now()->format('F j, Y') }}</div>
    <div class="subject"><strong>Subject:</strong> Admission Result</div>
    <div class="academic-year"><strong>Academic Year:</strong> {{ $applicant->Academic_Year }}</div>
  </div>

  {{-- GREETING --}}
  <p>
    Dear
    {{ $applicant->First_Name }}
    @if($applicant->Middle_Name) {{ $applicant->Middle_Name }} @endif
    {{ $applicant->Last_Name }},
  </p>

  {{-- PROGRAM INFO --}}
  <p>
    <strong>Program:</strong>
    {{ $application->program->program_code }} &mdash; {{ $application->program->program_name }}
  </p>

  {{-- PASS / FAIL MESSAGE --}}
  @if(strtoupper($admission->admission_status) === 'PASSED')
    <p>
      Congratulations! You have <strong>PASSED</strong> the examination for the
      <strong>{{ $application->program->program_name }}</strong> program.
    </p>
  @else
    <p>
      Thank you for your interest in joining BeastLink University. After careful consideration, we regret to inform you that you have <strong>FAILED</strong> the examination for the <strong>{{ $application->program->program_name }}</strong> program.
    </p>
  @endif

  {{-- FOOTER NOTE --}}
  <p>
    If you have any questions, please contact our Office of Admissions at
    <a href="mailto:admissions@beastlink.edu.ph">admissions@beastlink.edu.ph</a>.
  </p>

  {{-- SIGNATURE --}}
  <div c
