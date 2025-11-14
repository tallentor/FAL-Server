<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentSuccessMail extends Mailable
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
        return $this->subject('Payment Successful')
                    ->view('emails.payment-success');
    }
}
