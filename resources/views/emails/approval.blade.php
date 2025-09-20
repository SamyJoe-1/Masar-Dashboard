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
        .highlight {
            color: #28a745;
            font-weight: bold;
        }
        .congratulations {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
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

        <div class="congratulations">
            <h3 style="color: #28a745; margin: 0;">Congratulations!</h3>
            <p style="margin: 10px 0 0 0;">We are pleased to offer you the position!</p>
        </div>

        <p>We are delighted to inform you that after careful review of your application and qualifications, we would like to offer you the <strong>{{ $jobTitle }}</strong> position with {{ $companyName }}.</p>

        <div class="applicant-info">
            <h4>Application Details:</h4>
            {{--        <p><strong>Applicant ID:</strong> #{{ $applicantId }}</p>--}}
            <p><strong>Position:</strong> {{ $jobTitle }}</p>
            <p><strong>Email:</strong> {{ $applicantEmail }}</p>
            @if($applicantPhone)
                <p><strong>Phone:</strong> {{ $applicantPhone }}</p>
            @endif
            <p><strong>Status:</strong> <span class="highlight">Approved</span></p>
        </div>

        <p>Your skills, experience, and qualifications stood out among many excellent candidates, and we believe you will be a valuable addition to our team.</p>

        <p>Next steps:</p>
        <ul>
            <li>Our HR representative will contact you within 2-3 business days to discuss the offer details</li>
            <li>Please prepare any questions you may have about the role, compensation, or benefits</li>
            <li>We will provide you with all necessary onboarding information upon acceptance</li>
        </ul>

        <p>We are excited about the possibility of having you join our team and look forward to hearing from you soon.</p>

        <p>Congratulations once again, and welcome to the {{ $companyName }} family!</p>

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
