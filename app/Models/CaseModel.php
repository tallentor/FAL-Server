<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CaseModel extends Model
{
    use HasFactory;

    protected $table = 'cases';

    protected $fillable = [
        'user_id',
        'type_of_visa',
        'country_of_destination',
        'visa_expiry_date',
        'immigration_history',
        'case_title',
        'case_description',
        'reason_for_immigration',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedLawyer()
    {
    return $this->hasOne(AssignLawyer::class);
    }

}