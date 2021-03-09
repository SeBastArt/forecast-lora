<?php

namespace App\Console\Commands;

use App\Models\Alert;
use App\Models\Node;
use App\Services\AlertService;
use App\Services\FieldService;
use App\Services\NodeService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessLimits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:limits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check all lower and upper limits';


    private $nodeService = null;
    private $fieldService = null;
    private $alertService = null;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(NodeService $nodeService, FieldService $fieldService, AlertService $alertService)
    {
        parent::__construct();
        $this->nodeService = $nodeService;
        $this->fieldService = $fieldService;
        $this->alertService = $alertService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $nodes = Node::all();
        //each Node
        foreach ($nodes as $nodeKey => $node) {
           
            //look 24h back because cron runs every day at 23:55
            $rawDataFields = $this->nodeService->getRawData($node, Carbon::now()->subHours(24), Carbon::now());
            /* 
                    $rawDataFields: 
                        0 = data
                        1 = data
                        ...
                        last = timestamp
                */
            $rawDataCount = $rawDataFields->count() - 1;
            $timestamp = Carbon::now();
            $foundLimit = false;
            foreach ($node->fields as $fieldKey => $field) {
                if ($fieldKey < $rawDataCount && $foundLimit == false) { //prevent overshoot if more fields are given
                    $rawDataField = $rawDataFields[$fieldKey]; //->reverse(); //oldest alert first
                    foreach ($rawDataField as $rawKey => $rawData) {
                        if($foundLimit == true) { continue; }
                        $timestamp = $rawDataFields->last()[$rawKey]->format('Y-m-d H:i:s.u0');
                        //lower_limit
                        if ($field->check_lower_limit && $foundLimit == false) {
                            $value = number_format($rawData, 1, '.', '');
                            if ($value <= $field->lower_limit) {
                                $foundLimit = true;
                                //dd($timestamp);
                            }
                        }

                        //upper_limit
                        if ($field->check_upper_limit && $foundLimit == false) {
                            $value = number_format($rawData, 1, '.', '');
                            if ($value >= $field->upper_limit) {
                                $foundLimit = true;
                                //dd($timestamp);
                            }
                        }
                    }
                }
                if ($foundLimit == true) {
                    //create Alert 
                    $this->alertService->createAlert($field, $timestamp, Alert::ERROR_LEVEL_WARNING);
                    $foundLimit = false;
                    continue;
                }
            }
        }
    }
}
