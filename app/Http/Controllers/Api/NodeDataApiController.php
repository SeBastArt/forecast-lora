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

        $baseUrl = env('FORECAST_API_URL');
        $appid = env('FORECAST_API_KEY');

        $nodeCity = City::where('name', '=', $node->facility->company->city)->first();
        if (!isset($nodeCity)) {
            return 0;
        }
        $needUpdate = true;
        //Test if i get the first forcast item for the city stored in the node 
        $item = Forecast::where('city_id', $nodeCity->id)->first() ?? null;

        $item = (isset($item)) ? $item->forecastItems()->first('valid_from') : null;
        //$needUpdate = (isset($item)) ? Carbon::now() > Carbon::parse($item->valid_from)->addMinutes(120) : 'true';

        if ($needUpdate == true && isset($nodeCity)) {
            $forecast = Forecast::where('city_id', $nodeCity->id)->first();
            if (isset($forecast)) {
                $forecast->delete();
            }

            $client = new Client();
            $response = $client->request('GET', $baseUrl, [
                'query' => ['appid' => $appid, 'mode' => 'json', 'id' => $nodeCity->api_id]
            ]);
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $json = DecodeHelper::json_clean_decode($body);

            $nodeCity->api_id = $json['city']['id'];
            $nodeCity->name = $json['city']['name'];
            $nodeCity->country = $json['city']['country'];
            $nodeCity->lat = $json['city']['coord']['lat'];
            $nodeCity->lon = $json['city']['coord']['lon'];
            $nodeCity->save();

            $forecast = Forecast::create([
                'city_id' => $nodeCity->id,
                'sunrise' => Carbon::parse($json['city']['sunrise']),
                'sunset' => Carbon::parse($json['city']['sunset']),
            ]);

            foreach ($json['list'] as $forecastItem) {
                if (Carbon::parse($forecastItem['dt']) < Carbon::now()->addMinutes(60)) {
                    continue;
                }

                $weather = Weather::where('api_id', $forecastItem['weather'][0]['id'])->first();
                if (!isset($weather)) {
                    $weather = Weather::create([
                        'Api_id' => $forecastItem['weather'][0]['id'],
                        'main' => $forecastItem['weather'][0]['main'],
                        'description' => $forecastItem['weather'][0]['description'],
                        'icon' => $forecastItem['weather'][0]['icon'],
                    ]);
                }

                //Kelvin to Degree
                $degTemp = (float)$forecastItem['main']['temp'] - 273.15;
                ForecastItem::create([
                    'forecast_id' => $forecast->id,
                    'valid_from' => Carbon::parse($forecastItem['dt']),
                    'temp' =>  number_format($degTemp, 1, '.', ''), //format to one digit 
                    'humidity' =>  $forecastItem['main']['humidity'],
                    'weather_id' => $weather->id,
                ]);
            }
        }

        dd();


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
