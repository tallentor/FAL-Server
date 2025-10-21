<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssignLawyer extends Model
{
    use HasFactory;

    protected $fillable = [
        'case_id',
        'lawyer_id',
    ];

    public function case()
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }

     public function lawyer()
     {
         return $this->belongsTo(LawyerProfile::class, 'lawyer_id');
     }
}
