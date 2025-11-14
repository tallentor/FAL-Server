<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form Confirmation - Mobile Responsive</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f4f7f9;
            padding: 20px;
            min-height: 100vh;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
            color: #ffffff;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .header p {
            font-size: 16px;
            opacity: 0.95;
        }

        .content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 20px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 20px;
        }

        .intro-text {
            font-size: 16px;
            color: #4a5568;
            margin-bottom: 30px;
            line-height: 1.7;
        }

        .message-card {
            background-color: #f7fafc;
            border-left: 4px solid #667eea;
            border-radius: 8px;
            padding: 24px;
            margin: 25px 0;
        }

        .message-field {
            margin-bottom: 20px;
        }

        .message-field:last-child {
            margin-bottom: 0;
        }

        .field-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #667eea;
            margin-bottom: 8px;
            display: block;
        }

        .field-value {
            font-size: 15px;
            color: #2d3748;
            line-height: 1.6;
            word-wrap: break-word;
        }

        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #e2e8f0, transparent);
            margin: 20px 0;
        }

        .closing-text {
            font-size: 16px;
            color: #4a5568;
            margin: 30px 0 20px;
            line-height: 1.7;
        }

        .signature {
            font-size: 16px;
            color: #2d3748;
            font-weight: 500;
        }

        .company-name {
            color: #667eea;
            font-weight: 600;
        }

        .footer {
            background-color: #f7fafc;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }

        .footer-text {
            font-size: 13px;
            color: #718096;
            line-height: 1.6;
        }

        .checkmark {
            display: inline-block;
            width: 50px;
            height: 50px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            margin-bottom: 15px;
            line-height: 50px;
            font-size: 28px;
        }

        /* Tablet breakpoint */
        @media only screen and (max-width: 768px) {
            body {
                padding: 15px;
            }

            .email-container {
                border-radius: 10px;
            }

            .header {
                padding: 35px 25px;
            }

            .content {
                padding: 35px 25px;
            }
        }

        /* Mobile breakpoint */
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }

            .email-container {
                border-radius: 8px;
            }

            .header {
                padding: 30px 20px;
            }

            .header h1 {
                font-size: 24px;
            }

            .header p {
                font-size: 14px;
            }

            .checkmark {
                width: 45px;
                height: 45px;
                line-height: 45px;
                font-size: 24px;
                margin-bottom: 12px;
            }

            .content {
                padding: 30px 20px;
            }

            .greeting {
                font-size: 18px;
                margin-bottom: 16px;
            }

            .intro-text {
                font-size: 15px;
                margin-bottom: 25px;
            }

            .message-card {
                padding: 20px 16px;
                margin: 20px 0;
                border-left-width: 3px;
            }

            .field-label {
                font-size: 11px;
            }

            .field-value {
                font-size: 14px;
            }

            .divider {
                margin: 16px 0;
            }

            .closing-text {
                font-size: 15px;
                margin: 25px 0 16px;
            }

            .signature {
                font-size: 15px;
            }

            .footer {
                padding: 20px;
            }

            .footer-text {
                font-size: 12px;
            }
        }

        /* Small mobile breakpoint */
        @media only screen and (max-width: 400px) {
            body {
                padding: 8px;
            }

            .header {
                padding: 25px 16px;
            }

            .header h1 {
                font-size: 22px;
            }

            .content {
                padding: 25px 16px;
            }

            .message-card {
                padding: 16px 12px;
            }
        }

        /* Demo banner */
        .demo-banner {
            max-width: 600px;
            margin: 0 auto 20px;
            background: #667eea;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            text-align: center;
            font-size: 14px;
            font-weight: 500;
        }

        @media only screen and (max-width: 600px) {
            .demo-banner {
                font-size: 13px;
                padding: 10px 15px;
                margin-bottom: 15px;
            }
        }
    </style>
</head>

<body>

    <div class="email-container">
        <div class="header">
            <div class="checkmark">✓</div>
            <h1>Message Received!</h1>
            <p>We've got your message and we're on it</p>
        </div>

        <div class="content">
            <div class="greeting">Hi {{ $contact->name }},</div>

            <p class="intro-text">
                Thank you for reaching out to us. We've received your message and wanted to confirm the details with
                you:
            </p>

            <div class="message-card">
                <div class="message-field">
                    <span class="field-label">Subject</span>
                    <div class="field-value">{{ $contact->subject }}</div>
                </div>

                <div class="divider"></div>

                <div class="message-field">
                    <span class="field-label">Your Message</span>
                    <div class="field-value">{{ $contact->message }}</div>
                </div>
            </div>

            <p class="closing-text">
                Our team is reviewing your message and will get back to you as soon as possible. We typically respond
                within 24-48 hours during business days.
            </p>

            <p class="signature">
                Best regards,<br>
                <span class="company-name">Hotline.lk</span>
            </p>
        </div>

        <div class="footer">
            <p class="footer-text">
                This is an automated confirmation email. Please do not reply directly to this message.
            </p>
        </div>
    </div>
</body>

</html>
