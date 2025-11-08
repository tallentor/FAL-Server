<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarSlot extends Model
{
    /** @use HasFactory<\Database\Factories\CalendarSlotFactory> */
    use HasFactory;

    protected $fillable = ['calendar_id', 'day', 'slot_1', 'slot_2', 'slot_3'];

    protected $casts = [
        'slot_1' => 'array',
        'slot_2' => 'array',
        'slot_3' => 'array',
    ];

    public function calendar()
    {
        return $this->belongsTo(Calendar::class);
    }
}
