<?php

namespace App\Mail;

use App\Helpers\MailAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AlertLimits extends Mailable
{
    use Queueable, SerializesModels;

    public $mailAlert;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(MailAlert $mailAlert)
    {
        $this->mailAlert = $mailAlert;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.alerts.limits');
    }
}
