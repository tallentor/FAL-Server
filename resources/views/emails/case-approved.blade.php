<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>Your Case Has Been Approved</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background-color: #f0f4f8;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
        }

        .header {
            padding: 32px;
            text-align: center;
            background: #4F46E5;
        }

        .brand {
            font-size: 24px;
            font-weight: 600;
            color: #ffffff;
        }

        .content {
            padding: 40px 32px;
        }

        h1 {
            margin: 0 0 16px 0;
            font-size: 24px;
            font-weight: 600;
            color: #111;
        }

        p {
            margin: 0 0 16px 0;
            line-height: 1.6;
            color: #666;
            font-size: 16px;
        }

        .details {
            background: #EEF2FF;
            padding: 24px;
            border-radius: 6px;
            margin: 24px 0;
            border-left: 4px solid #4F46E5;
        }

        .detail-row {
            margin: 12px 0;
            font-size: 15px;
            color: #333;
        }

        .btn {
            display: inline-block;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 6px;
            font-weight: 500;
            background: #4F46E5;
            color: #fff;
            font-size: 15px;
            margin: 24px 0;
        }

        .footer {
            padding: 24px 32px;
            text-align: center;
            font-size: 13px;
            color: #999;
            background: #F9FAFB;
        }

        @media screen and (max-width:480px) {
            .container {
                margin: 20px;
            }

            .content {
                padding: 32px 24px;
            }
        }
    </style>
</head>

<body>
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="padding:20px;">
        <tr>
            <td align="center">
                <div class="container">
                    <div class="header">
                        <div class="brand">LegalConnect</div>
                    </div>

                    <div class="content">
                        <h1>Hello {{ $client->name }},</h1>

                        <p>Your case has been approved by your lawyer. Please join the meeting at the scheduled time.
                        </p>

                        <div class="details">
                            <div class="detail-row"><strong>Date:</strong>
                                {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('F j, Y') }}</div>
                            <div class="detail-row"><strong>Time:</strong>
                                {{ \Carbon\Carbon::parse($meeting->meeting_time)->format('g:i A') }}</div>
                            <div class="detail-row"><strong>Lawyer:</strong>
                                {{ $meeting->lawyer->name ?? 'Your Lawyer' }}</div>
                        </div>

                        <a class="btn" href="{{ $meeting->zoom_link }}" target="_blank">Join Meeting</a>

                        <p style="margin-top:24px; font-size:14px;">If you need to reschedule, please reply to this
                            email.</p>

                        <p style="margin-top:24px; color:#999;">Link: <a href="{{ $meeting->zoom_link }}"
                                style="color:#4F46E5; text-decoration:none;">{{ $meeting->zoom_link }}</a></p>
                    </div>

                    <div class="footer">
                        © {{ date('Y') }} LegalConnect
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>

</html>
