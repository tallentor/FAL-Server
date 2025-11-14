<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminPaymentNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;
    public $appointment;

    public function __construct($payment)
    {
        $this->payment = $payment;
        $this->appointment = $payment->appointment;
    }

    public function build()
    {
        return $this->subject('New Payment Received')
                    ->view('emails.admin-payment-notification'); 
    }
}