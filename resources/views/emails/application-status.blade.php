<!DOCTYPE html>
<html>
<head>
    <title>Your Application Status Has Been Updated</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
     
        .header {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .content {
            margin-bottom: 20px;
        }
        .footer {
            font-size: 14px;
            color: #555;
            margin-top: 20px;
        }
        .highlight {
            font-weight: bold;
        }
        .link {
            color: #007bff;
            text-decoration: none;
        }
        .link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
        Collage Admission Application Status Update
        </div>
        <div class="content">
            <p>Dear {{ $body['applicant_name'] }},</p>
            <p>We hope this email finds you well. We are reaching out to inform you that there has been an update to your admission application status at <span class="highlight">Beastlink University</span>.</p>
            <p>
                <strong>Application ID:</strong> {{ $body['applicant_id'] }}<br>
                <strong>Updated Status:</strong> {{ $body['status'] }}<br>
                <strong>Details:</strong> {{ $body['remarks'] }}
            </p>
            <p>Please check your account for further details and complete any pending requirements.</p>
            <p>You can track your application progress by logging into your account:</p>
            <p>
                🌟 <a href="https://beastlinkuniversity.com" class="link">https://beastlinkuniversity.com</a>
            </p>
        </div>
        <div class="footer">
            <p>If you have any questions or need assistance, feel free to contact us at <span class="highlight">beastlinkuniversity@gmail.com</span> or visit our admissions office.</p>
            <p>Best regards,<br>Beastlink University Admissions Team</p>
            <p>
                📞 +1 234 567 890 | 📧 beastlinkuniversity@gmail.com | 🌐 <a href="https://beastlinkuniversity.com" class="link">https://beastlinkuniversity.com</a>
            </p>
        </div>
    </div>
</body>
</html>