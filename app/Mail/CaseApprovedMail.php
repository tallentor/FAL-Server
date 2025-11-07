<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CaseApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $meeting;

    /**
     * Create a new message instance.
     */
    public function __construct($client, $meeting)
    {
        $this->client = $client;
        $this->meeting = $meeting;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Case Has Been Approved')
            ->view('emails.case-approved');
    }
}
