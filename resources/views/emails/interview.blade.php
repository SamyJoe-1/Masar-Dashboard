<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interview Invitation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .content {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .applicant-info {
            background-color: #f1f3f4;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .highlight {
            color: #007bff;
            font-weight: bold;
        }
        .next-step-banner {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        .interview-button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
            margin: 20px 0;
            text-align: center;
            transition: background-color 0.3s ease;
        }
        .interview-button:hover {
            background-color: #0056b3;
            color: white;
            text-decoration: none;
        }
        .button-container {
            text-align: center;
            margin: 25px 0;
        }
    </style>
</head>
<body>
<div style="max-width: 600px;margin: auto auto">
    <div class="header">
        <h2>{{ $companyName }}</h2>
        <p>Interview Invitation</p>
    </div>

    <div class="content">
        <h3>Dear {{ $applicantName }},</h3>

        <div class="next-step-banner">
            <h3 style="color: #3464b0; margin: 0;">Moving Forward!</h3>
            <p style="margin: 10px 0 0 0;">You've been selected for the next step in our hiring process.</p>
        </div>

        <p>We are pleased to inform you that after reviewing your application for the <strong>{{ $jobTitle }}</strong> position, we would like to invite you to proceed to the interview stage.</p>

        <div class="applicant-info">
            <h4>Application Details:</h4>
            {{--        <p><strong>Applicant ID:</strong> #{{ $applicantId }}</p>--}}
            <p><strong>Position:</strong> {{ $jobTitle }}</p>
            <p><strong>Email:</strong> {{ $applicantEmail }}</p>
            @if($applicantPhone)
                <p><strong>Phone:</strong> {{ $applicantPhone }}</p>
            @endif
            <p><strong>Status:</strong> <span class="highlight">Interview Stage</span></p>
        </div>

        <p>Your qualifications and experience have impressed our hiring team, and we're excited to learn more about you and discuss how you can contribute to {{ $companyName }}.</p>

        <div class="button-container">
            <a href="{{ $link }}" class="interview-button">Start Interview Session</a>
        </div>

        <p><strong>What to expect:</strong></p>
        <ul>
            <li>The interview will take approximately {{ $estimatedDuration ?? '30-45 minutes' }}</li>
            <li>Please ensure you have a stable internet connection</li>
            <li>Have your resume and any relevant documents ready</li>
            <li>Prepare questions about the role and our company</li>
        </ul>

        <p><strong>Important:</strong> Please complete the interview within the next {{ $deadlineDays ?? '7 days' }} to ensure your application remains active in our system.</p>

        <p>If you encounter any technical issues or need to reschedule, please contact our HR team immediately.</p>

        <p>We look forward to speaking with you soon!</p>

        <p>Best regards,<br>
            <strong>{{ $companyName }} HR Team</strong></p>
    </div>

    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
        <p>&copy; {{ date('Y') }} {{ $companyName }}. All rights reserved.</p>
    </div>
</div>
</body>
</html>
