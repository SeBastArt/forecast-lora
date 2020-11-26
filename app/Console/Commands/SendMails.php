<?php

namespace App\Console\Commands;

use App\Jobs\ProcessMails;
use App\Node;
use Illuminate\Console\Command;

class SendMails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:mails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending Alerts';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $nodes = Node::all();
        echo 'yo';
        foreach ($nodes as $key => $node) {
            if ($node->errorLevel > 1){
                if($node->facility->company->user->name == 'Sebastian Schüler' ){
                    echo 'yo';
                    ProcessMails::dispatch($node);
                }
            }
        }
    }
}
