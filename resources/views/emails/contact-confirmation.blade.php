<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank you for contacting Masar</title>
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
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 26px;
        }
        .header p {
            margin: 0;
            opacity: 0.9;
            font-size: 16px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #333;
        }
        .message-summary {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            margin: 20px 0;
        }
        .summary-label {
            font-weight: bold;
            color: #667eea;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .summary-value {
            color: #333;
            font-size: 16px;
            margin-bottom: 15px;
        }
        .summary-value:last-child {
            margin-bottom: 0;
        }
        .next-steps {
            background-color: #e7f3ff;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }
        .next-steps h3 {
            color: #0066cc;
            margin: 0 0 15px 0;
            font-size: 18px;
        }
        .next-steps ul {
            margin: 0;
            padding-left: 20px;
        }
        .next-steps li {
            margin-bottom: 8px;
            color: #333;
        }
        .contact-info {
            background-color: #f1f1f1;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-top: 25px;
        }
        .contact-info h3 {
            color: #333;
            margin: 0 0 15px 0;
        }
        .contact-details {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 15px;
        }
        .contact-item {
            text-align: center;
            min-width: 120px;
        }
        .contact-item-icon {
            font-size: 20px;
            margin-bottom: 5px;
        }
        .contact-item-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }
        .contact-item-value {
            font-weight: bold;
            color: #333;
            font-size: 14px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #666;
            font-size: 14px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .email-container {
                padding: 20px;
            }
            .header {
                padding: 20px;
            }
            .contact-details {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
<div style="max-width: 600px;margin: auto auto">
    <div class="email-container">
        <div class="header">
            <div class="logo">🚀 MASAR</div>
            <h1>Thank You for Contacting Us!</h1>
            <p>We've received your message and will get back to you soon</p>
        </div>

        <div class="greeting">
            Hi {{ $contactData['name'] }},
        </div>

        <p>Thank you for reaching out to the Masar team! We have successfully received your message and wanted to confirm that it's now in our queue for review.</p>

        <div class="message-summary">
            <div class="summary-label">📧 Your Message Summary</div>
            <div class="summary-value">
                <strong>Subject:</strong>
                @switch($contactData['subject'])
                    @case('general') General Inquiry @break
                    @case('technical') Technical Support @break
                    @case('billing') Billing and Payment @break
                    @case('partnership') Partnerships @break
                    @case('other') Other @break
                    @default {{ ucfirst($contactData['subject']) }}
                @endswitch
            </div>
            <div class="summary-value">
                <strong>Submitted:</strong> {{ $contactData['submitted_at']->format('F j, Y \a\t g:i A') }}
            </div>
            @if($contactData['phone'])
                <div class="summary-value">
                    <strong>Phone:</strong> {{ $contactData['phone'] }}
                </div>
            @endif
        </div>

        <div class="next-steps">
            <h3>🎯 What happens next?</h3>
            <ul>
                <li><strong>Review:</strong> Our team will review your message within 24 hours</li>
                <li><strong>Response:</strong> We'll send a detailed response to your email address</li>
                @if($contactData['subject'] == 'technical')
                    <li><strong>Technical Support:</strong> For urgent technical issues, our team prioritizes these requests</li>
                @elseif($contactData['subject'] == 'partnership')
                    <li><strong>Partnership:</strong> Our business development team will reach out to discuss opportunities</li>
                @endif
                <li><strong>Follow-up:</strong> If needed, we may contact you via phone for clarification</li>
            </ul>
        </div>

        <p>In the meantime, feel free to explore our platform features or check our FAQ section for immediate answers to common questions.</p>

        <div class="contact-info">
            <h3>📞 Need Immediate Assistance?</h3>
            <div class="contact-details">
                <div class="contact-item">
                    <div class="contact-item-icon">📧</div>
                    <div class="contact-item-label">Email</div>
                    <div class="contact-item-value">support@massar.biz</div>
                </div>
                <div class="contact-item">
                    <div class="contact-item-icon">📞</div>
                    <div class="contact-item-label">Phone</div>
                    <div class="contact-item-value">+968 95 160 789</div>
                </div>
                <div class="contact-item">
                    <div class="contact-item-icon">🕑</div>
                    <div class="contact-item-label">Available</div>
                    <div class="contact-item-value">24/7</div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p><strong>Best regards,</strong><br>The Masar Platform Team</p>
            <p style="margin-top: 15px; font-size: 12px; color: #999;">
                This is an automated confirmation email. Please do not reply to this email directly.
            </p>
        </div>
    </div>
</div>
</body>
</html>
