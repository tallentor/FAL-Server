<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewLawyerProfileNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $adminUser;
    public $lawyerUser;
    public $specialty;
    public $experience;
    public $education;
    public $description;
    public $barAssociationId;
    public $dashboardUrl;

    public function __construct($adminUser, $lawyerUser, $profileData)
    {
        $this->adminUser = $adminUser;
        $this->lawyerUser = $lawyerUser;
        $this->specialty = $profileData['specialty'] ?? null;
        $this->experience = $profileData['experience'] ?? null;
        $this->education = $profileData['education'] ?? null;
        $this->description = $profileData['description'] ?? null;
        $this->barAssociationId = $profileData['bar_association_id'] ?? null;
        $this->dashboardUrl = 'https://hotline.lk/admin-dashboard';
    }

    public function build()
    {
        return $this->subject('New Lawyer Profile Pending Approval')
                    ->view('emails.new_lawyer_profile');
    }
}