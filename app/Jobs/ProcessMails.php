<?php

namespace App\Jobs;

use App\Helpers\Alert;
use App\Mail\AlertTemperature;
use App\Mail\AlertTest;
use App\Node;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ProcessMails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $node;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Node $node)
    {
        $this->node = $node;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->node == null){
            return 0;
        }
        $user = $this->node->facility->company->user;

        $alert = new Alert;
        $alert->text = 'Node '.$this->node->name.' brennt';
        $alert->nodeId = $this->node->id;
        $alert->errorId = 2;
        Mail::to($user->email)->queue(new AlertTemperature($alert));
    }
}
