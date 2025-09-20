<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Status Update</title>
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
        .feedback-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .feedback-item {
            margin-bottom: 15px;
        }
        .feedback-title {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .strengths .feedback-title {
            color: #28a745;
        }
        .weaknesses .feedback-title {
            color: #dc3545;
        }
        .overall-fit .feedback-title {
            color: #007bff;
        }
        .highlight {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div style="max-width: 600px;margin: auto auto">
    <div class="header">
        <h2>{{ $companyName }}</h2>
        <p>Application Status Update</p>
    </div>

    <div class="content">
        <h3>Dear {{ $applicantName }},</h3>

        <p>Thank you for your interest in the <strong>{{ $jobTitle }}</strong> position with {{ $companyName }}.</p>

        <p>After careful consideration of your application and qualifications, we have decided to move forward with other candidates whose experience more closely matches our current needs.</p>

        <div class="applicant-info">
            <h4>Application Details:</h4>
            {{--        <p><strong>Applicant ID:</strong> #{{ $applicantId }}</p>--}}
            <p><strong>Position:</strong> {{ $jobTitle }}</p>
            <p><strong>Email:</strong> {{ $applicantEmail }}</p>
            @if($applicantPhone)
                <p><strong>Phone:</strong> {{ $applicantPhone }}</p>
            @endif
            <p><strong>Status:</strong> <span class="highlight">Rejected</span></p>
        </div>

        @if($feedback)
            @php
                $feedbackData = is_string($feedback) ? json_decode($feedback, true) : $feedback;
            @endphp

            @if($feedbackData)
                <div class="feedback-section">
                    <h4>Application Feedback:</h4>

                    @if(isset($feedbackData['strengths']))
                        <div class="feedback-item strengths">
                            <div class="feedback-title">Strengths:</div>
                            <p>{{ $feedbackData['strengths'] }}</p>
                        </div>
                    @endif

                    @if(isset($feedbackData['weaknesses']))
                        <div class="feedback-item weaknesses">
                            <div class="feedback-title">Areas for Development:</div>
                            <p>{{ $feedbackData['weaknesses'] }}</p>
                        </div>
                    @endif

                    @if(isset($feedbackData['overall_fit_justification']))
                        <div class="feedback-item overall-fit">
                            <div class="feedback-title">Overall Assessment:</div>
                            <p>{{ $feedbackData['overall_fit_justification'] }}</p>
                        </div>
                    @endif
                </div>
            @endif
        @endif

        <p>We want to emphasize that this decision does not reflect your capabilities or potential. The selection process is highly competitive, and we often have to make difficult choices among many qualified candidates.</p>

        <p>We encourage you to:</p>
        <ul>
            <li>Continue monitoring our career opportunities</li>
            <li>Apply for future positions that match your skills and experience</li>
            <li>Connect with us on our professional networks</li>
        </ul>

        <p>We appreciate the time and effort you invested in the application process and wish you the best of luck in your job search.</p>

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
