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
    ];


    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    // public function assignedLawyer()
    // {
    // return $this->hasOne(AssignLawyer::class);
    // }

    // Relationship to client
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship to assigned lawyer
    public function assignedLawyer()
    {
        return $this->hasOne(AssignLawyer::class, 'case_id');
    }

    public function lawyers()
{
    return $this->belongsToMany(LawyerProfile::class, 'assign_lawyers', 'case_id', 'lawyer_id');
}

}
