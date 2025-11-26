<?php

namespace App\Models;


use App\Mail\VerifyEmailCustom;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable , HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'role',
        'email',
        'phone_number',
        'profile_image',
        'address',
        'gender',
        'nationality',
        'married_status',
        'date_of_birth',
        'passport_number',
        'password',
    ];

    protected $appends = ['email_verified'];

    public function getEmailVerifiedAttribute()
    {
        return $this->hasVerifiedEmail();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_activity' => 'datetime',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Lawyer assigned cases
    public function lawyerProfile()
{
    return $this->hasOne(LawyerProfile::class);
}

    // Client’s own cases
    public function cases()
    {
        return $this->hasMany(CaseModel::class, 'user_id');
    }


    public function assignedCases()
{
    return $this->belongsToMany(CaseModel::class, 'assign_lawyers', 'lawyer_id', 'case_id');
}

    // verify email
    public function sendEmailVerificationNotification()
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $this->getKey(),
                'hash' => sha1($this->email),
            ]
        );

        Mail::to($this->email)->send(new VerifyEmailCustom($verificationUrl));
    }

}
