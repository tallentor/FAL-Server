<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'lawyer_profile_id',
        'day'
    ];

    protected $casts = [
        'day' => 'array'
    ];

    /**
     * Get the lawyer profile that owns the calendar slot
     */
    public function lawyerProfile()
    {
        return $this->belongsTo(LawyerProfile::class);
    }
}