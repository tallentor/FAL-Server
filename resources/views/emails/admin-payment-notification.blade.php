<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Payment Received</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f4f7fa;">
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 40px 20px;">
                <table role="presentation" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); overflow: hidden;">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 40px 30px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 600; letter-spacing: -0.5px;">New Payment Received</h1>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="margin: 0 0 24px; color: #2d3748; font-size: 16px; line-height: 1.6;">Hello Admin,</p>

                            <p style="margin: 0 0 32px; color: #2d3748; font-size: 16px; line-height: 1.6;">
                                A new payment has been made by <strong style="color: #1a202c;">{{ $appointment->user->name }}</strong> for appointment
                                <strong style="color: #1a202c;">{{ $appointment->case_title }}</strong>.
                            </p>

                            <p style="margin: 0 0 16px; color: #4a5568; font-size: 15px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Payment details:</p>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f7fafc; border-radius: 8px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0;">
                                        <span style="color: #718096; font-size: 14px;">Amount:</span>
                                        <span style="color: #2d3748; font-size: 15px; font-weight: 600; float: right;">{{ $payment->amount }} {{ $payment->currency }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0;">
                                        <span style="color: #718096; font-size: 14px;">Status:</span>
                                        <span style="color: #2d3748; font-size: 15px; font-weight: 600; float: right;">{{ $payment->status }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0;">
                                        <span style="color: #718096; font-size: 14px;">Payment ID:</span>
                                        <span style="color: #2d3748; font-size: 15px; font-weight: 600; float: right; word-break: break-all;">{{ $payment->stripe_payment_intent_id }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <span style="color: #718096; font-size: 14px;">Paid at:</span>
                                        <span style="color: #2d3748; font-size: 15px; font-weight: 600; float: right;">{{ $payment->paid_at ?? 'N/A' }}</span>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 32px 0 0; color: #2d3748; font-size: 16px; line-height: 1.6;">Check the admin panel for more details.</p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 30px 40px; background-color: #f7fafc; border-top: 1px solid #e2e8f0; text-align: center;">
                            <p style="margin: 0; color: #718096; font-size: 13px; line-height: 1.5;">
                                This is an automated notification. Please do not reply to this email.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
