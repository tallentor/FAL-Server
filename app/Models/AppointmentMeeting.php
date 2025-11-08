<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentMeeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'lawyer_id',
        'user_id',
        'meeting_date',
        'meeting_time',
        'zoom_link',
        'host_link',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
