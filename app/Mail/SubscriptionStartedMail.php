<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionStartedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $planName;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $planName)
    {
        $this->user = $user;
        $this->planName = $planName;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Je abonnement is actief!')
                    ->view('emails.subscription_started');
    }
}
