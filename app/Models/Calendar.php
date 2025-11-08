<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    /** @use HasFactory<\Database\Factories\CalendarFactory> */
    use HasFactory;
    protected $guarded = [];
    

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function lawyerProfile(){
        return $this->belongsTo(LawyerProfile::class);
    }

}
