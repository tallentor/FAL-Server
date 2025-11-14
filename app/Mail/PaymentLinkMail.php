<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $payment;

    public function __construct($appointment, $payment)
    {
        $this->appointment = $appointment;
        $this->payment = $payment;
    }

    public function build()
    {
        return $this->subject('Payment Link for Your Appointment')
                    ->markdown('emails.payment_link');
    }
}
