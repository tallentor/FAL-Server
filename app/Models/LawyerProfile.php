<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LawyerProfile extends Model
{
    /** @use HasFactory<\Database\Factories\LawyerProfileFactory> */
    use HasFactory;

    public function assignedCases()
{
    return $this->belongsToMany(CaseModel::class, 'assign_lawyers', 'lawyer_id', 'case_id');
}

    protected $guarded = [];


    public function user()
{
    return $this->belongsTo(\App\Models\User::class, 'user_id');
}


}
