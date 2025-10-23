<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CaseNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'lawyer_id',
        'case_id',
        'lawyer_name',
        'client_name',
        'type',
        'date',
        'time',
        'zoom_link',
        'status',
    ];


    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function lawyer()
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }

    public function case()
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }
}
