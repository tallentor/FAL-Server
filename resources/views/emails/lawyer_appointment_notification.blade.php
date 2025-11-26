<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Appointment Received</title>
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

                                        <!-- Calendar Icon with Circle Background -->
                                        <div
                                            style="display: inline-block; background: rgba(255,255,255,0.15); border-radius: 50%; padding: 10px; margin-bottom: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.2), 0 0 0 5px rgba(255,255,255,0.08);">
                                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                                stroke="white" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" style="display: block;">
                                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                                <path d="M8 14h.01"></path>
                                                <path d="M12 14h.01"></path>
                                                <path d="M16 14h.01"></path>
                                                <path d="M8 18h.01"></path>
                                                <path d="M12 18h.01"></path>
                                            </svg>
                                        </div>

                                        <!-- Heading -->
                                        <div>
                                            <h1
                                                style="margin: 0; color: #ffffff; font-size: 26px; font-weight: 700; letter-spacing: -0.5px; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                                New Appointment Received
                                            </h1>
                                            <p
                                                style="margin: 5px 0 0; color: rgba(255,255,255,0.9); font-size: 13px; font-weight: 400;">
                                                A client has booked a consultation with you
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
                                Hello <strong>{{ $appointment->lawyer->name }}</strong>,
                            </p>

                            <p style="font-size: 16px; color: #2d3748; margin: 0 0 28px; line-height: 1.6;">
                                You have received a new appointment request from <strong style="color: #c41019;">{{ $appointment->full_name }}</strong>. Please review the details below and prepare for the upcoming consultation.
                            </p>

                            <!-- CLIENT INFO BOX -->
                            <p
                                style="margin: 0 0 12px; color: #4a5568; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                Client Information:</p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="margin-bottom: 25px; background-color: #f7fafc; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0;">
                                <tr>
                                    <td style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="color: #718096; font-size: 14px; width: 35%;">Client Name:</td>
                                                <td
                                                    style="color: #2d3748; font-size: 15px; font-weight: 600; text-align: right;">
                                                    {{ $appointment->full_name }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="color: #718096; font-size: 14px; width: 35%;">Email:</td>
                                                <td
                                                    style="color: #c41019; font-size: 15px; font-weight: 600; text-align: right;">
                                                    <a href="mailto:{{ $appointment->email }}"
                                                        style="color: #c41019; text-decoration: none;">{{ $appointment->email }}</a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="color: #718096; font-size: 14px; width: 35%;">Phone:</td>
                                                <td
                                                    style="color: #2d3748; font-size: 15px; font-weight: 600; text-align: right;">
                                                    {{ $appointment->phone ?? 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- APPOINTMENT DETAILS BOX -->
                            <p
                                style="margin: 0 0 12px; color: #4a5568; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                Appointment Details:</p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="margin-bottom: 25px; background-color: #fffbf5; border-radius: 8px; overflow: hidden; border: 2px solid #f59e0b;">
                                <tr>
                                    <td style="padding: 16px 20px; border-bottom: 1px solid #fcd34d;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="width: 40px; vertical-align: top;">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                        stroke="#f59e0b" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                                    </svg>
                                                </td>
                                                <td>
                                                    <p style="margin: 0; color: #78350f; font-size: 13px; font-weight: 600;">
                                                        Appointment Date
                                                    </p>
                                                    <p style="margin: 4px 0 0; color: #92400e; font-size: 16px; font-weight: 700;">
                                                        {{ $appointment->appointment_date }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 16px 20px; border-bottom: 1px solid #fcd34d;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="width: 40px; vertical-align: top;">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                        stroke="#f59e0b" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <circle cx="12" cy="12" r="10"></circle>
                                                        <polyline points="12 6 12 12 16 14"></polyline>
                                                    </svg>
                                                </td>
                                                <td>
                                                    <p style="margin: 0; color: #78350f; font-size: 13px; font-weight: 600;">
                                                        Appointment Time
                                                    </p>
                                                    <p style="margin: 4px 0 0; color: #92400e; font-size: 16px; font-weight: 700;">
                                                        {{ $appointment->appointment_time }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="width: 40px; vertical-align: top;">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                        stroke="#f59e0b" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                        <polyline points="14 2 14 8 20 8"></polyline>
                                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                                        <polyline points="10 9 9 9 8 9"></polyline>
                                                    </svg>
                                                </td>
                                                <td>
                                                    <p style="margin: 0; color: #78350f; font-size: 13px; font-weight: 600;">
                                                        Case Title
                                                    </p>
                                                    <p style="margin: 4px 0 0; color: #92400e; font-size: 16px; font-weight: 700;">
                                                        {{ $appointment->case_title }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- CASE DESCRIPTION (if available) -->
                            @if(isset($appointment->case_description) && $appointment->case_description)
                            <p
                                style="margin: 0 0 12px; color: #4a5568; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                Case Description:</p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="margin-bottom: 25px;">
                                <tr>
                                    <td
                                        style="background-color: #f7fafc; border-left: 4px solid #3b82f6; padding: 20px 24px; border-radius: 6px;">
                                        <p
                                            style="margin: 0; color: #2d3748; font-size: 15px; line-height: 1.7; white-space: pre-wrap;">
                                            {{ $appointment->case_description }}</p>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <!-- ACTION BUTTON -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="margin-bottom: 25px;">
                                <tr>
                                    <td style="text-align: center;">
                                        <a href="{{ $dashboardUrl ?? '#' }}"
                                            style="display: inline-block; padding: 16px 48px; background: linear-gradient(135deg, #c41019 0%, #a00d15 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; letter-spacing: 0.5px; box-shadow: 0 4px 12px rgba(196, 16, 25, 0.3);">
                                            View Full Details in Dashboard
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- REMINDER BOX -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td
                                        style="background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 16px 20px; border-radius: 6px;">
                                        <p style="margin: 0; color: #1e40af; font-size: 14px; line-height: 1.6;">
                                            <strong>📋 Reminder:</strong> Please log in to your lawyer dashboard to view complete appointment details.
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
                                This is an automated notification from your appointment management system.
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