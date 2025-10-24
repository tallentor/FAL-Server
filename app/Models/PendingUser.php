<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingUser extends Model
{
    /** @use HasFactory<\Database\Factories\PendingUserFactory> */
    use HasFactory;
    protected $guarded =[];
}
