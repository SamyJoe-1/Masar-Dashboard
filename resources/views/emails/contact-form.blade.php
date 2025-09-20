<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Form Submission</title>
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
        .email-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .info-item {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #667eea;
        }
        .info-label {
            font-weight: bold;
            color: #667eea;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 16px;
            color: #333;
        }
        .message-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .message-label {
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .message-content {
            background-color: white;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
            font-size: 15px;
            line-height: 1.7;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #666;
            font-size: 14px;
        }
        .meta-info {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
            font-size: 13px;
            color: #666;
        }
        .meta-info strong {
            color: #333;
        }
        @media (max-width: 600px) {
            .info-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            body {
                padding: 10px;
            }
            .email-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
<div style="max-width: 600px;margin: auto auto">
    <div class="email-container">
        <div class="header">
            <h1>🚀 New Contact Form Submission</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Masar Platform</p>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">👤 Full Name</div>
                <div class="info-value">{{ $contactData['name'] }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">📧 Email Address</div>
                <div class="info-value">{{ $contactData['email'] }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">📞 Phone Number</div>
                <div class="info-value">{{ $contactData['phone'] ?? 'Not provided' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">📋 Subject</div>
                <div class="info-value">
                    @switch($contactData['subject'])
                        @case('general') General Inquiry @break
                        @case('technical') Technical Support @break
                        @case('billing') Billing and Payment @break
                        @case('partnership') Partnerships @break
                        @case('other') Other @break
                        @default {{ ucfirst($contactData['subject']) }}
                    @endswitch
                </div>
            </div>
        </div>

        <div class="message-section">
            <div class="message-label">💬 Message</div>
            <div class="message-content">
                <?php echo nl2br(@$contactData['message'])?>
            </div>
        </div>

        <div class="meta-info">
            <strong>Submission Details:</strong><br>
            <strong>Date:</strong> {{ $contactData['submitted_at']->format('F j, Y \a\t g:i A') }}<br>
            <strong>IP Address:</strong> {{ $contactData['ip_address'] }}<br>
            <strong>User Agent:</strong> {{ request()->userAgent() }}
        </div>

        <div class="footer">
            <p>This email was sent from the Masar platform contact form.</p>
            <p><strong>Reply directly to this email to respond to {{ $contactData['name'] }}</strong></p>
        </div>
    </div>
</div>
</body>
</html>
