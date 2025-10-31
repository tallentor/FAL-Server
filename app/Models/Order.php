<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'lawyer_id', 'lawyer_profile_id',
        'lawyer_fee', 'system_fee', 'total_amount',
        'status', 'payhere_order_id',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function lawyer() {
        return $this->belongsTo(User::class, 'lawyer_id');
    }

    public function lawyerProfile() {
        return $this->belongsTo(LawyerProfile::class);
    }
}
