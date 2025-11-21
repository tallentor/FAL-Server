<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'lawyer_id',
        'appointment_id',
        'lawyer_name',
        'client_name',
        'type',
        'date',
        'time',
        'zoom_link',
        'status',
        'user_phone_number',
        'host_zoom_link',
    ];
}
