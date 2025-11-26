<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmailCustom extends Mailable
{
    use SerializesModels;

    public $url;

    public function __construct($url)
    {
        $this->url = $url;  
    }

    public function build()
    {
        return $this->subject('Verify Your Email Address')
                    ->view('emails.verify-custom'); 
    }
}
