<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email Address</title>
</head>

<body
    style="margin: 0; padding: 0; background-color: #f4f7fa; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
        <tr>
            <td style="padding: 40px 20px;">

                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                    style="max-width: 600px; margin: 0 auto; border-radius: 14px; background: #ffffff; overflow: hidden; box-shadow: 0px 4px 12px rgba(0,0,0,0.08);">

                    <!-- HEADER -->
                    <tr>
                        <td
                            style="background: linear-gradient(135deg, #c41019 0%, #a00d15 100%); padding: 25px 40px; position: relative; overflow: hidden;">

                            <!-- Decorative Shapes -->
                            <div
                                style="position: absolute; top: -40px; right: -40px; width: 120px; height: 120px; background: rgba(255,255,255,0.08); border-radius: 50%; pointer-events: none;">
                            </div>
                            <div
                                style="position: absolute; bottom: -20px; left: -20px; width: 100px; height: 100px; background: rgba(255,255,255,0.06); border-radius: 50%; pointer-events: none;">
                            </div>
                            <div
                                style="position: absolute; top: 35%; right: 15%; width: 50px; height: 50px; background: rgba(255,255,255,0.05); transform: translateY(-50%) rotate(45deg); pointer-events: none;">
                            </div>

                            <!-- LOGO (Aligned Left Top) -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="position: relative; z-index: 1;">
                                <tr>
                                    <td>
                                        <img src="https://i.postimg.cc/8C8KbwrM/Hotline-lk-Logo-PNG-(1).png"
                                            alt="Hotline.lk Logo"
                                            style="width: 120px; height: auto; margin-bottom: 15px; display: block; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">
                                    </td>
                                </tr>
                            </table>

                            <!-- NOTIFICATION ROW -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="position: relative; z-index: 1;">
                                <tr>
                                    <td style="text-align: center; padding: 5px 0 0;">

                                        <!-- Shield Check Icon with Circle Background -->
                                        <div
                                            style="display: inline-block; background: rgba(255,255,255,0.15); border-radius: 50%; padding: 10px; margin-bottom: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.2), 0 0 0 5px rgba(255,255,255,0.08);">
                                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                                stroke="white" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" style="display: block;">
                                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                                <polyline points="9 12 11 14 15 10"></polyline>
                                            </svg>
                                        </div>

                                        <!-- Heading -->
                                        <div>
                                            <h1
                                                style="margin: 0; color: #ffffff; font-size: 26px; font-weight: 700; letter-spacing: -0.5px; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                                Verify Your Email Address
                                            </h1>
                                            <p
                                                style="margin: 5px 0 0; color: rgba(255,255,255,0.9); font-size: 13px; font-weight: 400;">
                                                One more step to get started
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
                                Hello there,
                            </p>

                            <p style="font-size: 16px; color: #2d3748; margin: 0 0 28px; line-height: 1.6;">
                                Thank you for signing up with <strong>Hotline.lk</strong>! To complete your registration
                                and activate your account, please verify your email address by clicking the button
                                below.
                            </p>

                            <!-- VERIFY BUTTON -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="margin-bottom: 28px;">
                                <tr>
                                    <td style="text-align: center;">
                                        <a href="{{ $url }}"
                                            style="display: inline-block; padding: 16px 48px; background: linear-gradient(135deg, #c41019 0%, #a00d15 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; letter-spacing: 0.5px; box-shadow: 0 4px 12px rgba(196, 16, 25, 0.3); transition: all 0.3s ease;">
                                            Verify Email Address
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- ALTERNATIVE LINK BOX -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="margin-bottom: 25px;">
                                <tr>
                                    <td
                                        style="background-color: #f7fafc; padding: 20px 24px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                        <p
                                            style="margin: 0 0 10px; color: #4a5568; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Alternative Link:
                                        </p>
                                        <p style="margin: 0; color: #718096; font-size: 13px; line-height: 1.6;">
                                            If the button doesn't work, copy and paste this link into your browser:
                                        </p>
                                        <p style="margin: 10px 0 0; word-break: break-all;">
                                            <a href="{{ $url }}"
                                                style="color: #c41019; text-decoration: none; font-size: 13px;">{{ $url }}</a>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- EXPIRY INFO -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td
                                        style="background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 16px 20px; border-radius: 6px;">
                                        <p style="margin: 0; color: #1e40af; font-size: 14px; line-height: 1.6;">
                                            <strong>⏱️ Important:</strong> This verification link will expire in 60
                                            minutes for security purposes.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td
                            style="background: #fafafa; padding: 28px 40px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; font-size: 13px; color: #6b7280; line-height: 1.5;">
                                This is an automated email. Please do not reply to this message.
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
