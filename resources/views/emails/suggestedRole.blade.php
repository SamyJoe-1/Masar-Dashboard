<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Suggestion - Perfect Match Found!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 20px;
            text-align: center;
        }
        .header h2 {
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        .header p {
            margin: 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .job-highlight {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 25px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: center;
        }
        .job-highlight h3 {
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        .job-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .applicant-info {
            background-color: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #2196f3;
        }
        .apply-button {
            display: inline-block;
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            font-size: 16px;
            margin: 20px 0;
            transition: transform 0.2s;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
        }
        .apply-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
            text-decoration: none;
            color: white;
        }
        .footer {
            background-color: #343a40;
            color: #fff;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            font-size: 12px;
        }
        .highlight {
            color: #28a745;
            font-weight: bold;
        }
        .job-description {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }
        .match-reason {
            background: linear-gradient(135deg, #74b9ff, #0984e3);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .info-row {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            color: #495057;
        }
    </style>
</head>
<body>
<div style="max-width: 600px;margin: auto auto">
    <div class="header">
        <h2>{{ $companyName }}</h2>
        <p>🎯 Perfect Job Match Found For You!</p>
    </div>

    <div class="content">
        <h3>Hello {{ $applicantName }}! 👋</h3>

        <p>We're excited to share that we found a fantastic job opportunity that perfectly matches your profile and skills!</p>

        <div class="job-highlight">
            <h3>{{ $jobTitle }}</h3>
            <p>This role is waiting for someone just like you!</p>
        </div>

        @if($jobDescription)
            <div class="job-details">
                <h4>📋 Job Description:</h4>
                <div class="job-description">
                    {!! nl2br(e($jobDescription)) !!}
                </div>
            </div>
        @endif

        @if($matchReason)
            <div class="match-reason">
                <h4>✨ Why This Job is Perfect For You:</h4>
                <p>{{ $matchReason }}</p>
            </div>
        @endif

        <div class="applicant-info">
            <h4>👤 Your Profile Summary:</h4>
            <div class="info-row">
                <span class="info-label">Name:</span> {{ $applicantName }}
            </div>
            @if($applicantEmail)
                <div class="info-row">
                    <span class="info-label">Email:</span> {{ $applicantEmail }}
                </div>
            @endif
            @if($applicantPhone)
                <div class="info-row">
                    <span class="info-label">Phone:</span> {{ $applicantPhone }}
                </div>
            @endif
            <div class="info-row">
                <span class="info-label">Status:</span> <span class="highlight">Recommended Candidate</span>
            </div>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $applyUrl }}" class="apply-button">
                🚀 Apply Now for This Amazing Opportunity!
            </a>
        </div>

        <div class="job-details">
            <h4>What happens next?</h4>
            <ul>
                <li>✅ Click the "Apply Now" button above</li>
                <li>📝 Complete your application for this specific role</li>
                <li>⚡ Our HR team will review your application within 24-48 hours</li>
                <li>📞 If you're a good fit, we'll schedule an interview</li>
            </ul>
        </div>

        <p><strong>Don't wait too long!</strong> Great opportunities like this don't stay open forever. We believe you have exactly what we're looking for.</p>

        <p>Best of luck, and we hope to welcome you to our team soon!</p>

        <p>Warm regards,<br>
            <strong>{{ $companyName }} Talent Acquisition Team</strong> 🎊</p>
    </div>

    <div class="footer">
        <p>This email was sent because we found a perfect match between your profile and our job opening.</p>
        <p>If you're not interested, simply ignore this email.</p>
        <p>&copy; {{ date('Y') }} {{ $companyName }}. All rights reserved.</p>
    </div>
</div>

</body>
</html>
