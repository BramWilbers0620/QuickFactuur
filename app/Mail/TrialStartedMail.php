<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Carbon;

class TrialStartedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $trialEndsAt;

    public function __construct($user)
    {
        $this->user = $user;
        $this->trialEndsAt = Carbon::parse($user->trial_ends_at)->format('d-m-Y');
    }

    public function build()
    {
        return $this->subject('🎉 Je gratis proefperiode is gestart!')
                    ->view('emails.trial_started');
    }
}
