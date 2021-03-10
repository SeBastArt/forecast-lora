<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Helpers\DecodeHelper;

use App\Models\Forecast;
use App\Models\City;
use App\Models\ForecastItem;
use App\Models\Weather;

class FetchForecast extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:forecast';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fetch the actual forecast for Dresden';

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
        $baseUrl = env('FORECAST_API_URL');
        $appid = env('FORECAST_API_KEY');


        //$nodeCity = City::where('name', '=', $node->facility->company->city)->first();
        $nodeCity = City::where('name', '=', 'Dresden')->first();
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
                if (Carbon::parse($forecastItem['dt']) < Carbon::now()->addMinutes(120)) {
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
    }
}
