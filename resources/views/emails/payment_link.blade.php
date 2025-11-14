<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Approved</title>
</head>

<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f4; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0"
                    style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); max-width: 100%;">

                    <!-- Header -->
                    <tr>
                        <td style="background-color: #4CAF50; padding: 30px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: normal;">Appointment
                                Approved</h1>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 30px;">
                            <p style="margin: 0 0 20px 0; font-size: 16px; color: #333333;">
                                Hello <strong>{{ $appointment->full_name }}</strong>,
                            </p>

                            <p style="margin: 0 0 20px 0; font-size: 14px; color: #555555; line-height: 1.6;">
                                Your appointment has been approved. Please complete your payment to confirm your
                                booking.
                            </p>

                            <!-- Info Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="background-color: #f9f9f9; border-left: 4px solid #4CAF50; margin: 20px 0;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 8px 0; font-size: 14px; color: #555555;">
                                            <strong style="color: #333333;">Appointment Title:</strong>
                                            {{ $appointment->case_title }}
                                        </p>
                                        <p style="margin: 8px 0; font-size: 14px; color: #555555;">
                                            <strong style="color: #333333;">Appointment ID:</strong>
                                            #{{ $appointment->id }}
                                        </p>
                                        <p style="margin: 8px 0; font-size: 14px; color: #555555;">
                                            <strong style="color: #333333;">Date:</strong>
                                            {{ $appointment->appointment_date ?? 'To be confirmed' }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $payment->payment_link }}"
                                            style="display: inline-block; background-color: #4CAF50; color: #ffffff; text-decoration: none; padding: 15px 40px; border-radius: 5px; font-weight: bold; font-size: 16px;">Make
                                            Payment</a>
                                    </td>
                                </tr>
                            </table>

                            <p
                                style="margin: 20px 0 0 0; text-align: center; color: #666666; font-size: 14px; line-height: 1.6;">
                                If the button doesn't work, copy this link:<br>
                                <span style="color: #4CAF50; word-break: break-all;">{{ $payment->payment_link }}</span>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td
                            style="background-color: #f9f9f9; padding: 20px; text-align: center; border-top: 1px solid #eeeeee;">
                            <p style="margin: 0 0 5px 0; color: #666666; font-size: 14px;">
                                Thank you,<br><strong style="color: #333333;">Find a Lawyer Team</strong>
                            </p>
                            <p style="margin: 15px 0 0 0; color: #666666; font-size: 12px;">
                                If you have any questions, please reply to this email.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>
