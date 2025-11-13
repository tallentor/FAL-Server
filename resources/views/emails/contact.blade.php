{{-- <!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>New Contact Form Submission</title>
</head>

<body>
    <h2>New Contact Message Received</h2>
    <p><strong>Name:</strong> {{ $contact->name }}</p>
    <p><strong>Email:</strong> {{ $contact->email }}</p>
    <p><strong>Phone:</strong> {{ $contact->phone ?? 'N/A' }}</p>
    <p><strong>Subject:</strong> {{ $contact->subject }}</p>
    <p><strong>Message:</strong></p>
    <p>{{ $contact->message }}</p>
    <hr>
    <p>Sent at: {{ $contact->created_at->format('Y-m-d H:i') }}</p>
</body>

</html> --}}

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Form Submission</title>
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
        }

        .email-container {
            max-width: 650px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #f093fb 0%, #667eea 100%);
            padding: 35px 30px;
            text-align: center;
            color: #ffffff;
        }

        .notification-icon {
            display: inline-block;
            width: 55px;
            height: 55px;
            background-color: rgba(255, 255, 255, 0.25);
            border-radius: 50%;
            margin-bottom: 15px;
            line-height: 55px;
            font-size: 30px;
        }

        .header h1 {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .header p {
            font-size: 15px;
            opacity: 0.95;
        }

        .content {
            padding: 40px 30px;
        }

        .alert-badge {
            display: inline-block;
            background: linear-gradient(135deg, #f093fb 0%, #667eea 100%);
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
        }

        .intro-text {
            font-size: 16px;
            color: #4a5568;
            margin-bottom: 30px;
            line-height: 1.7;
        }

        .details-grid {
            background-color: #f7fafc;
            border-radius: 10px;
            padding: 28px;
            margin: 25px 0;
            border: 1px solid #e2e8f0;
        }

        .detail-row {
            display: flex;
            padding: 14px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .detail-row:first-child {
            padding-top: 0;
        }

        .detail-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .detail-label {
            font-size: 13px;
            font-weight: 600;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            width: 120px;
            flex-shrink: 0;
            padding-top: 2px;
        }

        .detail-value {
            font-size: 15px;
            color: #2d3748;
            font-weight: 500;
            flex: 1;
            word-wrap: break-word;
        }

        .detail-value a {
            color: #667eea;
            text-decoration: none;
            transition: opacity 0.2s;
        }

        .detail-value a:hover {
            opacity: 0.8;
            text-decoration: underline;
        }

        .message-section {
            background-color: #fffbf5;
            border-left: 4px solid #667eea;
            border-radius: 8px;
            padding: 24px;
            margin: 25px 0;
        }

        .message-label {
            font-size: 13px;
            font-weight: 600;
            color: #667eea;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
            display: block;
        }

        .message-content {
            font-size: 15px;
            color: #2d3748;
            line-height: 1.7;
            white-space: pre-wrap;
        }

        .timestamp-section {
            background-color: #f7fafc;
            padding: 16px 20px;
            border-radius: 8px;
            margin-top: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid #e2e8f0;
        }

        .timestamp-label {
            font-size: 12px;
            font-weight: 600;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .timestamp-value {
            font-size: 14px;
            color: #2d3748;
            font-weight: 600;
            font-family: 'Courier New', monospace;
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

        .action-hint {
            display: inline-block;
            margin-top: 12px;
            padding: 10px 20px;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 13px;
            color: #4a5568;
            font-weight: 500;
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
                padding: 30px 25px;
            }

            .content {
                padding: 35px 25px;
            }

            .details-grid {
                padding: 24px;
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
                padding: 25px 20px;
            }

            .header h1 {
                font-size: 22px;
            }

            .header p {
                font-size: 14px;
            }

            .notification-icon {
                width: 50px;
                height: 50px;
                line-height: 50px;
                font-size: 26px;
                margin-bottom: 12px;
            }

            .content {
                padding: 30px 20px;
            }

            .alert-badge {
                font-size: 11px;
                padding: 5px 14px;
                margin-bottom: 16px;
            }

            .intro-text {
                font-size: 15px;
                margin-bottom: 25px;
            }

            .details-grid {
                padding: 20px 16px;
                margin: 20px 0;
            }

            .detail-row {
                flex-direction: column;
                padding: 12px 0;
            }

            .detail-label {
                width: 100%;
                margin-bottom: 6px;
                font-size: 12px;
            }

            .detail-value {
                font-size: 14px;
            }

            .message-section {
                padding: 20px 16px;
                margin: 20px 0;
                border-left-width: 3px;
            }

            .message-label {
                font-size: 12px;
                margin-bottom: 10px;
            }

            .message-content {
                font-size: 14px;
            }

            .timestamp-section {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
                padding: 14px 16px;
                margin-top: 20px;
            }

            .timestamp-label {
                font-size: 11px;
            }

            .timestamp-value {
                font-size: 13px;
            }

            .footer {
                padding: 20px;
            }

            .footer-text {
                font-size: 12px;
            }

            .action-hint {
                padding: 8px 16px;
                font-size: 12px;
                margin-top: 10px;
            }
        }

        /* Small mobile breakpoint */
        @media only screen and (max-width: 400px) {
            body {
                padding: 8px;
            }

            .header {
                padding: 20px 16px;
            }

            .content {
                padding: 25px 16px;
            }

            .details-grid {
                padding: 16px 12px;
            }

            .message-section {
                padding: 16px 12px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <div class="notification-icon">📬</div>
            <h1>New Contact Submission</h1>
            <p>You've received a new message from your website</p>
        </div>

        <div class="content">
            <span class="alert-badge">New Message</span>

            <p class="intro-text">
                A visitor has submitted the contact form on your website. Here are the details:
            </p>

            <div class="details-grid">
                <div class="detail-row">
                    <span class="detail-label">Name</span>
                    <span class="detail-value">{{ $contact->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email</span>
                    <span class="detail-value"><a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Phone</span>
                    <span class="detail-value">{{ $contact->phone ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Subject</span>
                    <span class="detail-value">{{ $contact->subject }}</span>
                </div>
            </div>

            <div class="message-section">
                <span class="message-label">Message</span>
                <div class="message-content">{{ $contact->message }}</div>
            </div>

            <div class="timestamp-section">
                <span class="timestamp-label">Received At</span>
                <span class="timestamp-value">{{ $contact->created_at->format('Y-m-d H:i') }}</span>
            </div>
        </div>

        <div class="footer">
            <p class="footer-text">
                This is an automated notification from your contact form system.
            </p>
            <div class="action-hint">
                💡 Reply directly to {{ $contact->email }} to respond
            </div>
        </div>
    </div>
</body>

</html>
