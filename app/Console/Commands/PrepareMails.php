<?php

namespace App\Console\Commands;

use App\Helpers\MailAlert;
use App\Services\AlertService;
use Illuminate\Console\Command;
use App\Jobs\ProcessMails;
use App\Models\Alert;

class PrepareMails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prepare:mails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prepare Mails and send to Queue';

    private $alertService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(AlertService $alertservice)
    {
        parent::__construct();
        $this->alertService = $alertservice;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $alerts = Alert::all();
        foreach ($alerts as $alertKey => $alert) {
            //if alert already reported, skip
            if($alert->send == true){ continue; }
            
            $field = $alert->field()->first();
            //try to get an emailaddress
            $users = $alert->field()->first()->nodes()->first()->facility->company->users()->get();
            foreach ($users as $userKey => $user) {
                $emailAddresses = $user->alertAddresses;
                if ($emailAddresses == null){
                    $emailAddresses = $user->email;
                }

                foreach ($emailAddresses as $key => $emailAddress) {
                     //new MailAlert Object
                    $mailAlert = new MailAlert;
                    $mailAlert->field = $field;
                    $mailAlert->alert = $alert;
                    $mailAlert->email = $emailAddress->email;
                    if ($user->id == 1){
                        ProcessMails::dispatch($mailAlert);
                    }
                    //mark Alert as reported
                    $this->alertService->AlertReported($alert);
                }   
            }
        }
    }
}
