<?php

namespace App\Jobs;

use App\Helpers\MailAlert;
use App\Mail\AlertLimits;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ProcessMails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $mailAlert;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(MailAlert $mailAlert)
    {
        $this->mailAlert = $mailAlert;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->mailAlert->email)->queue(new AlertLimits($this->mailAlert));
    }
}
