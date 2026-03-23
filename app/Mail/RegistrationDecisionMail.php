<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationDecisionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $eventTitle,
        public readonly string $status, // approved|rejected
    ) {
    }

    public function build(): self
    {
        $subject = $this->status === 'approved'
            ? "Your registration was approved"
            : "Your registration was declined";

        return $this->subject($subject)
            ->view('emails.registration-decision');
    }
}

