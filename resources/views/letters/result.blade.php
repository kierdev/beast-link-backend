{{-- resources/views/letters/result.blade.php --}}
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admission Confirmation Letter</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0 40px;
      color: #333;
    }
    header {
      background: #0b3d2e;
      padding: 20px 40px;
      display: flex;
      align-items: center;
    }
    header img {
      height: 50px;
      margin-right: 15px;
    }
    header h1 {
      font-size: 24px;
      color: #fff;
      margin: 0;
      font-weight: normal;
    }
    .meta {
      margin-top: 30px;
      line-height: 1.4;
    }
    .meta .date,
    .meta .subject {
      margin-bottom: 5px;
    }
    h2 {
      margin-top: 40px;
      font-size: 20px;
      color: #0b3d2e;
    }
    p {
      margin: 15px 0;
      line-height: 1.6;
    }
    .signature {
      margin-top: 60px;
    }
    .signature p {
      margin: 2px 0;
    }
  </style>
</head>
<body>

  {{-- HEADER --}}
  <header>
    <img src="{{ public_path('images/beastlink-logo.png') }}" alt="BeastLink Logo">
    <h1>BeastLink University</h1>
  </header>

  {{-- TITLE + META --}}
  <h2>Admission Confirmation Letter</h2>

  <div class="meta">
    <div class="date"><strong>Date:</strong> {{ now()->format('F j, Y') }}</div>
    <div class="subject"><strong>Subject:</strong> Admission Result</div>
  </div>

  {{-- GREETING --}}
  <p>Dear {{ $applicant->name }},</p>

  {{-- CONDITIONAL BODY --}}
  @if(strtoupper($applicant->status) === 'PASSED')
    <p>Congratulations! We are pleased to inform you that you have <strong>PASSED</strong> the examination for the <strong>{{ $applicant->course }}</strong> program at BeastLink University.</p>
    <p>We look forward to welcoming you on campus. Please watch your email for next steps on enrollment and orientation.</p>
  @else
    <p>Thank you for your interest in joining BeastLink University. After careful consideration of your application, we regret to inform you that you <strong>FAILED</strong> the examination for the <strong>{{ $applicant->course }}</strong> course at this time.</p>
    <p>We understand this may be disappointing, and we encourage you to continue working toward your goals. You are welcome to reapply in the next admission cycle.</p>
  @endif

  <p>If you have any questions, please contact our Office of Admissions at <a href="mailto:admissions@beastlink.edu.ph">admissions@beastlink.edu.ph</a>.</p>

  {{-- SIGNATURE --}}
  <div class="signature">
    <p>Sincerely,</p>
    <p><strong>Office of Admissions</strong></p>
    <p>BeastLink University</p>
  </div>

</body>
</html>
