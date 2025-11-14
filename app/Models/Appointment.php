<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $table = 'appointments';

    protected $fillable = [
        'user_id',
        'lawyer_id',
        'appointment_date',
        'appointment_time',
        'full_name',
        'case_title',
        'case_description',
        'type_of_visa',
        'country_of_destination',
        'current_visa_status',
        'visa_expiry_date',
        'immigration_history',
        'reason_for_immigration',
        'previous_visa_denials',
        'number_of_dependents',
        'additional_notes',
        'status',
        'payment_status',
    ];

    // Client who booked the appointment
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Lawyer (who is a user)
    public function lawyer()
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }

    // Lawyer profile (linked via lawyer.user_id)
    public function lawyerProfile()
    {
        return $this->hasOne(LawyerProfile::class, 'user_id', 'lawyer_id');
    }

    public function payment()
{
    return $this->hasOne(StripePayment::class);
}
}