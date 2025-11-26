<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Received</title>
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

                            <!-- SUCCESS ROW -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="position: relative; z-index: 1;">
                                <tr>
                                    <td style="text-align: center; padding: 5px 0 0;">

                                        <!-- Success Icon with Circle Background -->
                                        <div style="display: inline-block; background: rgba(255,255,255,0.15); border-radius: 50%; padding: 10px; margin-bottom: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.2), 0 0 0 5px rgba(255,255,255,0.08);">
                                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                                stroke="white" stroke-width="2.5" stroke-linecap="round"
                                                stroke-linejoin="round" style="display: block;">
                                                <circle cx="12" cy="12" r="10" fill="rgba(255,255,255,0.2)"></circle>
                                                <polyline points="16 8 10 14 8 12"></polyline>
                                            </svg>
                                        </div>

                                        <!-- Heading -->
                                        <div>
                                            <h1 style="margin: 0; color: #ffffff; font-size: 26px; font-weight: 700; letter-spacing: -0.5px; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                                Message Received!
                                            </h1>
                                            <p style="margin: 5px 0 0; color: rgba(255,255,255,0.9); font-size: 13px; font-weight: 400;">
                                                We've got your message and we're on it
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

                            <p style="font-size: 20px; color: #2d3748; margin: 0 0 20px; line-height: 1.6; font-weight: 600;">
                                Hi {{ $contact->name }},
                            </p>

                            <p style="font-size: 16px; color: #4a5568; margin: 0 0 28px; line-height: 1.7;">
                                Thank you for reaching out to us. We've received your message and wanted to confirm the details with you:
                            </p>

                            <!-- MESSAGE DETAILS CARD -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 30px;">
                                <tr>
                                    <td style="background-color: #f7fafc; border-left: 4px solid #c41019; padding: 24px 20px; border-radius: 8px;">

                                        <!-- Subject Field -->
                                        <div style="margin-bottom: 20px;">
                                            <div style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #c41019; margin-bottom: 8px;">
                                                Subject
                                            </div>
                                            <div style="font-size: 15px; color: #2d3748; line-height: 1.6; word-wrap: break-word;">
                                                {{ $contact->subject }}
                                            </div>
                                        </div>

                                        <!-- Divider -->
                                        <div style="height: 1px; background: linear-gradient(to right, transparent, #e2e8f0, transparent); margin: 20px 0;"></div>

                                        <!-- Message Field -->
                                        <div>
                                            <div style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #c41019; margin-bottom: 8px;">
                                                Your Message
                                            </div>
                                            <div style="font-size: 15px; color: #2d3748; line-height: 1.6; word-wrap: break-word;">
                                                {{ $contact->message }}
                                            </div>
                                        </div>

                                    </td>
                                </tr>
                            </table>

                            <p style="font-size: 16px; color: #4a5568; margin: 0 0 20px; line-height: 1.7;">
                                Our team is reviewing your message and will get back to you as soon as possible. We typically respond within 24-48 hours during business days.
                            </p>

                            <p style="margin: 0; color: #2d3748; font-size: 16px; line-height: 1.6; font-weight: 500;">
                                Best regards,<br>
                                <span style="color: #c41019; font-weight: 600;">Hotline.lk</span>
                            </p>

                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td style="background: #fafafa; padding: 28px 40px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; font-size: 13px; color: #6b7280; line-height: 1.5;">
                                This is an automated confirmation email. Please do not reply directly to this message.
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
