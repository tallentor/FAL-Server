<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Payment Received</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f7fa; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
        <tr>
            <td style="padding: 40px 20px;">

                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; border-radius: 14px; background: #ffffff; overflow: hidden; box-shadow: 0px 4px 12px rgba(0,0,0,0.08);">

                    <!-- HEADER -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #c41019 0%, #a00d15 100%); padding: 25px 40px; position: relative; overflow: hidden;">

                            <!-- Decorative Shapes -->
                            <div style="position: absolute; top: -40px; right: -40px; width: 120px; height: 120px; background: rgba(255,255,255,0.08); border-radius: 50%; pointer-events: none;"></div>
                            <div style="position: absolute; bottom: -20px; left: -20px; width: 100px; height: 100px; background: rgba(255,255,255,0.06); border-radius: 50%; pointer-events: none;"></div>
                            <div style="position: absolute; top: 35%; right: 15%; width: 50px; height: 50px; background: rgba(255,255,255,0.05); transform: translateY(-50%) rotate(45deg); pointer-events: none;"></div>

                            <!-- LOGO (Aligned Left Top) -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="position: relative; z-index: 1;">
                                <tr>
                                    <td>
                                        <img src="https://i.postimg.cc/8C8KbwrM/Hotline-lk-Logo-PNG-(1).png"
                                            alt="Hotline.lk Logo"
                                            style="width: 120px; height: auto; margin-bottom: 15px; display: block; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">
                                    </td>
                                </tr>
                            </table>

                            <!-- NOTIFICATION ROW -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="position: relative; z-index: 1;">
                                <tr>
                                    <td style="text-align: center; padding: 5px 0 0;">

                                        <!-- Bell Icon with Circle Background -->
                                        <div style="display: inline-block; background: rgba(255,255,255,0.15); border-radius: 50%; padding: 10px; margin-bottom: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.2), 0 0 0 5px rgba(255,255,255,0.08);">
                                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                                stroke="white" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" style="display: block;">
                                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                                <circle cx="18" cy="6" r="3" fill="white"></circle>
                                            </svg>
                                        </div>

                                        <!-- Heading -->
                                        <div>
                                            <h1 style="margin: 0; color: #ffffff; font-size: 26px; font-weight: 700; letter-spacing: -0.5px; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                                New Payment Received
                                            </h1>
                                            <p style="margin: 5px 0 0; color: rgba(255,255,255,0.9); font-size: 13px; font-weight: 400;">
                                                Admin notification
                                            </p>
                                        </div>

                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- CONTENT -->
                    <tr>
                        <td style="padding: 40px;">

                            <p style="font-size: 16px; color: #2d3748; margin: 0 0 20px; line-height: 1.6;">
                                Hello <strong>Admin</strong>,
                            </p>

                            <p style="font-size: 16px; color: #2d3748; margin: 0 0 28px; line-height: 1.6;">
                                A new payment has been made by
                                <strong style="color: #c41019;">{{ $appointment->user->name }}</strong>
                                for appointment titled
                                <strong style="color: #c41019;">"{{ $appointment->case_title }}"</strong>.
                            </p>

                            <!-- PAYMENT DETAILS BOX -->
                            <p style="margin: 0 0 12px; color: #4a5568; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Payment Details:</p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 30px; background-color: #f7fafc; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0;">
                                <tr>
                                    <td style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="color: #718096; font-size: 14px;">Amount:</td>
                                                <td style="color: #2d3748; font-size: 15px; font-weight: 600; text-align: right;">{{ $payment->amount }} {{ $payment->currency }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="color: #718096; font-size: 14px;">Status:</td>
                                                <td style="color: #2d3748; font-size: 15px; font-weight: 600; text-align: right;">{{ $payment->status }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="color: #718096; font-size: 14px; width: 40%;">Payment ID:</td>
                                                <td style="color: #2d3748; font-size: 15px; font-weight: 600; text-align: right; word-break: break-all;">{{ $payment->stripe_payment_intent_id }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="color: #718096; font-size: 14px;">Paid at:</td>
                                                <td style="color: #2d3748; font-size: 15px; font-weight: 600; text-align: right;">{{ $payment->paid_at ?? 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- INFO BOX -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 20px;">
                                <tr>
                                    <td style="background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 18px 20px; border-radius: 6px;">
                                        <p style="margin: 0; color: #1e40af; font-size: 15px; line-height: 1.6;">
                                            <strong>Action Required:</strong>
                                            Please check the admin panel for more details and take necessary actions.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td style="background: #fafafa; padding: 28px 40px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; font-size: 13px; color: #6b7280; line-height: 1.5;">
                                This is an automated notification. Please do not reply to this email.
                            </p>
                            <p style="margin: 8px 0 0; font-size: 12px; color: #9ca3af;">
                                Hotline.lk · All Rights Reserved
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>
