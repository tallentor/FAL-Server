<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Lawyer Profile Created</title>
</head>

<body style="margin: 0; padding: 0; background-color: #f4f7fa; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
        <tr>
            <td style="padding: 40px 20px;">

                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; border-radius: 14px; background: #ffffff; overflow: hidden; box-shadow: 0px 4px 12px rgba(0,0,0,0.08);">

                    <!-- HEADER -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #c41019 0%, #a00d15 100%); padding: 25px 40px; position: relative; overflow: hidden;">
                            
                            <!-- LOGO -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="position: relative; z-index: 1;">
                                <tr>
                                    <td>
                                        <img src="https://i.postimg.cc/8C8KbwrM/Hotline-lk-Logo-PNG-(1).png" alt="Hotline.lk Logo" style="width: 120px; height: auto; margin-bottom: 15px; display: block; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">
                                    </td>
                                </tr>
                            </table>

                            <!-- NOTIFICATION ROW -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="position: relative; z-index: 1;">
                                <tr>
                                    <td style="text-align: center; padding: 5px 0 0;">
                                        
                                        <!-- Icon -->
                                        <div style="display: inline-block; background: rgba(255,255,255,0.15); border-radius: 50%; padding: 10px; margin-bottom: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.2), 0 0 0 5px rgba(255,255,255,0.08);">
                                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: block;">
                                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="12" cy="7" r="4"></circle>
                                            </svg>
                                        </div>

                                        <!-- Heading -->
                                        <div>
                                            <h1 style="margin: 0; color: #ffffff; font-size: 26px; font-weight: 700; letter-spacing: -0.5px; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                                New Lawyer Profile Created
                                            </h1>
                                            <p style="margin: 5px 0 0; color: rgba(255,255,255,0.9); font-size: 13px; font-weight: 400;">
                                                A new lawyer has registered on the platform
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
                                Hello <strong>{{ $adminUser->name }}</strong>,
                            </p>

                            <p style="font-size: 16px; color: #2d3748; margin: 0 0 28px; line-height: 1.6;">
                                A new lawyer profile has been created and is pending approval. Please review the details below.
                            </p>

                            <!-- LAWYER INFO BOX -->
                            <p style="margin: 0 0 12px; color: #4a5568; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                Lawyer Information:
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 25px; background-color: #f7fafc; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0;">
                                <tr>
                                    <td style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="color: #718096; font-size: 14px; width: 40%;">User Name:</td>
                                                <td style="color: #2d3748; font-size: 15px; font-weight: 600; text-align: right;">
                                                    {{ $lawyerUser->name }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="color: #718096; font-size: 14px; width: 40%;">Email:</td>
                                                <td style="color: #c41019; font-size: 15px; font-weight: 600; text-align: right;">
                                                    <a href="mailto:{{ $lawyerUser->email }}" style="color: #c41019; text-decoration: none;">
                                                        {{ $lawyerUser->email }}
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                @if($specialty)
                                <tr>
                                    <td style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="color: #718096; font-size: 14px; width: 40%;">Specialty:</td>
                                                <td style="color: #2d3748; font-size: 15px; font-weight: 600; text-align: right;">
                                                    {{ $specialty }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                @endif
                                @if($experience)
                                <tr>
                                    <td style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="color: #718096; font-size: 14px; width: 40%;">Experience:</td>
                                                <td style="color: #2d3748; font-size: 15px; font-weight: 600; text-align: right;">
                                                    {{ $experience }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                @endif
                                @if($education)
                                <tr>
                                    <td style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="color: #718096; font-size: 14px; width: 40%;">Education:</td>
                                                <td style="color: #2d3748; font-size: 15px; font-weight: 600; text-align: right;">
                                                    {{ $education }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                @endif
                                @if($barAssociationId)
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="color: #718096; font-size: 14px; width: 40%;">Bar Association ID:</td>
                                                <td style="color: #2d3748; font-size: 15px; font-weight: 600; text-align: right;">
                                                    {{ $barAssociationId }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                @endif
                            </table>

                            @if($description)
                            <!-- DESCRIPTION -->
                            <p style="margin: 0 0 12px; color: #4a5568; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                Description:
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="background-color: #f7fafc; border-left: 4px solid #3b82f6; padding: 20px 24px; border-radius: 6px;">
                                        <p style="margin: 0; color: #2d3748; font-size: 15px; line-height: 1.7; white-space: pre-wrap;">
                                            {{ $description }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <!-- ACTION BUTTON -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="text-align: center;">
                                        <a href="{{ $dashboardUrl }}" style="display: inline-block; padding: 16px 48px; background: linear-gradient(135deg, #c41019 0%, #a00d15 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; letter-spacing: 0.5px; box-shadow: 0 4px 12px rgba(196, 16, 25, 0.3);">
                                            Review Profile in Dashboard
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- REMINDER BOX -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 16px 20px; border-radius: 6px;">
                                        <p style="margin: 0; color: #1e40af; font-size: 14px; line-height: 1.6;">
                                            <strong>📋 Action Required:</strong> Please log in to your admin dashboard to approve or reject this lawyer profile.
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
                                This is an automated notification from Hotline.lk
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