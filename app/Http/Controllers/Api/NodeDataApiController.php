<?php

namespace App\Http\Controllers\Api;

use App\Helpers\DecodeHelper;
use App\Helpers\MailAlert;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessMails;
use App\Mail\AlertLimits;
use App\Models\Alert;
use App\Models\City;
use App\Models\Forecast;
use App\Models\ForecastItem;
use App\Models\Node;
use App\Models\Weather;
use App\Services\AlertService;
use App\Services\FieldService;
use App\Services\NodeService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;

class NodeDataApiController extends Controller
{
    const TIMEFORMAT = 'H:i:s';
    private $nodeService;
    private $alertService;
    private $fieldService;

    public function __construct(NodeService $nodeService, FieldService $fieldService, AlertService $alertService)
    {
        //$this->middleware('auth:sanctum');
        $this->nodeService = $nodeService;
        $this->alertService = $alertService;
        $this->fieldService = $fieldService;
    }


    public function csvdata(Node $node, Request $request)
    {
        //without Request the last 24h
        $start = Carbon::now()->subHours(24);
        $end = Carbon::now();

        if (isset($request['startDate'])) {
            $start = Carbon::parse($request['startDate']);
        }

        if (isset($request['endDate'])) {
            $end = Carbon::parse($request['endDate']);
        }
        $columns = $this->nodeService->getCSVColumnsName($node);
        $csvExport =  $this->nodeService->getCSVArray($node, $start, $end);
       
        $callback = function() use ($csvExport, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($csvExport as $dataKey => $data) {
                $tempArray = array();
                foreach ($columns as $key => $column) {
                    array_push($tempArray, $data[$column]);
                }
                fputcsv($file, $tempArray, ";");
            }
            fclose($file);
        };

        //Build Header and Filename
        $companyName = $node->facility->company->name;
        $facilityName = $node->facility->name;
        $nodeName = $node->name;
        $file_name = $companyName.'_'.$facilityName.'_'.$nodeName.'_'.Carbon::parse($start)->format('Y.m.d_H:i:s').'-'.Carbon::parse($end)->format('Y.m.d_H:i:s').'.csv';

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=".$file_name,
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );


        return Response::stream($callback, 200, $headers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\ResourceColl
     */
    public function data(Node $node, Request $request)
    {
        //without Request the last 24h
        $start = Carbon::now()->subHours(24);
        $end = Carbon::now();

        if (isset($request['startDate'])) {
            $start = Carbon::parse($request['startDate']);
        }

        if (isset($request['endDate'])) {
            $end = Carbon::parse($request['endDate']);
        }

        $nodeData = $this->nodeService->getData($node, $start, $end);
        return response()->json($nodeData, 200, [], JSON_PRETTY_PRINT);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\ResourceColl
     */
    public function meta(Node $node, Request $request)
    {
        $nodeMeta = $this->nodeService->getMeta($node);
        return response()->json($nodeMeta, 200, [], JSON_PRETTY_PRINT);
    }
}
