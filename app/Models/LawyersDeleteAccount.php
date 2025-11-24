<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LawyersDeleteAccount extends Model
{
    protected $table = 'lawyers_deleted_accounts';
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'reason'
    ];
}
