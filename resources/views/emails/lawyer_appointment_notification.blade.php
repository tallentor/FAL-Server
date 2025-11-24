<h2>New Appointment Received</h2>

<p>Hello {{ $appointment->lawyer->name }},</p>

<p>You have received a new appointment from <strong>{{ $appointment->full_name }}</strong>.</p>

<p><strong>Appointment Details:</strong></p>
<ul>
    <li>Date: {{ $appointment->appointment_date }}</li>
    <li>Time: {{ $appointment->appointment_time }}</li>
    <li>Case Title: {{ $appointment->case_title }}</li>
</ul>

<p>Please log in to your lawyer dashboard to view more details.</p>

<p>Thank you.</p>
