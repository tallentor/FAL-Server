<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed</title>
</head>

<body
    style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f4f7fa;">
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 40px 20px;">
                <table role="presentation"
                    style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); overflow: hidden;">
                    <!-- Header -->
                    <tr>
                        <td
                            style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); padding: 40px 40px 30px; text-align: center;">
                            <div style="margin-bottom: 12px;">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    style="display: inline-block;">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                            </div>
                            <h1
                                style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 600; letter-spacing: -0.5px;">
                                Payment Failed</h1>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="margin: 0 0 24px; color: #2d3748; font-size: 16px; line-height: 1.6;">Dear
                                {{ $appointment->user->name }},</p>

                            <p style="margin: 0 0 32px; color: #2d3748; font-size: 16px; line-height: 1.6;">
                                Your payment of <strong style="color: #1a202c;">{{ $payment->amount }}
                                    {{ $payment->currency }}</strong> for appointment <strong
                                    style="color: #1a202c;">"{{ $appointment->case_title }}"</strong> has failed.
                            </p>

                            <div
                                style="background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 16px 20px; border-radius: 6px; margin-bottom: 32px;">
                                <p style="margin: 0; color: #991b1b; font-size: 15px; line-height: 1.6;">
                                    <strong>Action Required:</strong> Please try again to complete your payment.
                                </p>
                            </div>

                            <p style="margin: 0; color: #2d3748; font-size: 16px; line-height: 1.6;">Please try again.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td
                            style="padding: 30px 40px; background-color: #f7fafc; border-top: 1px solid #e2e8f0; text-align: center;">
                            <p style="margin: 0; color: #718096; font-size: 13px; line-height: 1.5;">
                                If you continue to experience issues, please contact our support team.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
