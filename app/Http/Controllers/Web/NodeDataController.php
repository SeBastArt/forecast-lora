<?php

namespace App\Http\Controllers\Web;

use App\City;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Node;
use App\Field;
use App\FieldData;
use App\Http\Resources\FieldResource;
use Illuminate\Http\Request;
use App\Http\Resources\NodeResource;
use App\NodeType;
use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Helpers\DecodeHelper;

use App\Forecast;
use App\ForecastItem;
use App\Weather;
use App\WeatherType;
use PhpParser\Node\Expr\Isset_;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class NodeDataController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Get the specified resource.
     *
     * @param  \App\Node  $node
     * @param  \App\Field  $field
     * @return \Illuminate\Http\JSONResponse
     */
    public function fieldData(Node $node, Field $field)
    {
        $dataCollection = collect();
        foreach ($field->data as $data) {
            $dataCollection->push(
                collect([
                    'x' => $data->created_at,
                    'y' => $data->value,
                ])
            );
        }

        //return response()->json($field->data->last(),200,[],JSON_PRETTY_PRINT);
        return response()->json($dataCollection);
    }


    /**
     * Get the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Node  $node
     * @return \Illuminate\Http\JSONResponse
     */
    public function nodeData(Request $request)
    {
        if (!$request->has('nodeId')) {
            return response()->json([
                'error' => 'nodeId required',
            ], 500, [], JSON_PRETTY_PRINT);
        }
        $node = Node::where('id', $request->query('nodeId'))->first();
        if (!isset($node)) {
            return response()->json([
                'error' => 'node not found',
            ], 404, [], JSON_PRETTY_PRINT);
        }
        if (Auth::user()->id !== $node->user_id) {
            return response()->json([
                'error' => 'not allowed',
            ], 405, [], JSON_PRETTY_PRINT);
        }

        /* $baseUrl = env('FORECAST_API_URL');
        $appid = env('FORECAST_API_KEY');

        $needUpdate = true;
        //Test if i get the first forcast item for the city stored in the node 
        $item = Forecast::where(
            'city_id',
            $node
                ->city()
                ->first()
                ->id
        )->first() ?? null;
        $item = (isset($item)) ? $item->forecastItems()->first('valid_from') : null;
        $needUpdate = (isset($item)) ? Carbon::now() > Carbon::parse($item->valid_from)->addMinutes(120) : 'true';

        //dd(Carbon::now());
        //dd(Carbon::parse($forecastItem->valid_from));  
        //dd($needUpdate);   
        $nodeCity = $node->city()->first();
        if ($needUpdate == true && isset($nodeCity)) {
            $forecast = Forecast::where('city_id', $nodeCity->id)->first();
            if (isset($forecast)) {
                $forecast->delete();
            }
            $client = new Client();
            $response = $client->request('GET', $baseUrl, [
                'query' => ['appid' => $appid, 'q' => $nodeCity->name]
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
        } */

        $nodeData = collect();
        foreach ($node->fields->sortBy('position') as $field) {
            $dataCollection = collect();
            foreach ($field->data->where('created_at', '>', Carbon::now()->subMinutes(1440)) as $data) {
                $dataCollection->push(
                    collect([
                        'x' => $data->created_at->format('c'),
                        'y' => $data->value,
                    ])
                );
            };

            if (Str::contains($field->name, ['Temperatur', 'temperature'])) {
                $city = $node->city()->first();
                if (isset($city)) {
                    $forecast = Forecast::where('city_id', $city->id)->first();
                    if (isset($forecast)) {
                        foreach ($forecast->forecastItems->where('valid_from', '<', Carbon::now()->addMinutes(1440)) as $forecastItem) {
                            $dataCollection->push(
                                collect([
                                    'x' => (Carbon::createFromFormat('Y-m-d H:i:s', $forecastItem->valid_from))->format('c'),
                                    'y' => floatval(number_format((float)$forecastItem->temp, 1, '.', '')), //format to one digit
                                ])
                            );
                        };
                    }
                }
            }
            $nodeData->push($dataCollection);
        }
        return response()->json($nodeData, 200, [], JSON_PRETTY_PRINT);
    }

    /**
     * Get the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JSONResponse
     */
    public function metaData(Request $request)
    {
        if (!$request->has('nodeId')) {
            return response()->json([
                'error' => 'nodeId required',
            ], 500, [], JSON_PRETTY_PRINT);
        }
        $node = Node::where('id', $request->query('nodeId'))->first();
        if (!isset($node)) {
            return response()->json([
                'error' => 'node not found',
            ], 404, [], JSON_PRETTY_PRINT);
        }
        if (Auth::user()->id !== $node->user_id) {
            return response()->json([
                'error' => 'not allowed',
            ], 405, [], JSON_PRETTY_PRINT);
        }

        $fieldsCollection = collect();
        foreach ($node->fields->sortBy('position') as $field) {
            $fieldItem = collect([
                'title' => $field->name,
                'unit' => $field->unit,
                'fill' => $field->isfilled,
                'primarycolor' => $field->primarycolor,
                'secondarycolor' => $field->secondarycolor,
                'min' => number_format($field->data->where('created_at', '>', Carbon::now()->subMinutes(1440))->min('value'), 1, '.', ''), //format to one digit,
                'max' => number_format($field->data->where('created_at', '>', Carbon::now()->subMinutes(1440))->max('value'), 1, '.', ''), //format to one digit,
            ]);
            $fieldsCollection->push($fieldItem);
        }
        $nodeCollection = collect([
            'id' => $node->id,
            'name' => $node->name,
            'deveui' => $node->dev_eui,
        ]);

        $response = collect([
            'fields' => $fieldsCollection,
            'node' => $nodeCollection
        ]);

        return response()->json($response, 200, [], JSON_PRETTY_PRINT);
    }

    public function convertPhpToJsMomentFormat(string $phpFormat): string
    {
        $replacements = [
            'A' => 'A',      // for the sake of escaping below
            'a' => 'a',      // for the sake of escaping below
            'B' => '',       // Swatch internet time (.beats), no equivalent
            'c' => 'YYYY-MM-DD[T]HH:mm:ssZ', // ISO 8601
            'D' => 'ddd',
            'd' => 'DD',
            'e' => 'zz',     // deprecated since version 1.6.0 of moment.js
            'F' => 'MMMM',
            'G' => 'H',
            'g' => 'h',
            'H' => 'HH',
            'h' => 'hh',
            'I' => '',       // Daylight Saving Time? => moment().isDST();
            'i' => 'mm',
            'j' => 'D',
            'L' => '',       // Leap year? => moment().isLeapYear();
            'l' => 'dddd',
            'M' => 'MMM',
            'm' => 'MM',
            'N' => 'E',
            'n' => 'M',
            'O' => 'ZZ',
            'o' => 'YYYY',
            'P' => 'Z',
            'r' => 'ddd, DD MMM YYYY HH:mm:ss ZZ', // RFC 2822
            'S' => 'o',
            's' => 'ss',
            'T' => 'z',      // deprecated since version 1.6.0 of moment.js
            't' => '',       // days in the month => moment().daysInMonth();
            'U' => 'X',
            'u' => 'SSSSSS', // microseconds
            'v' => 'SSS',    // milliseconds (from PHP 7.0.0)
            'W' => 'W',      // for the sake of escaping below
            'w' => 'e',
            'Y' => 'YYYY',
            'y' => 'YY',
            'Z' => '',       // time zone offset in minutes => moment().zone();
            'z' => 'DDD',
        ];

        // Converts escaped characters.
        foreach ($replacements as $from => $to) {
            $replacements['\\' . $from] = '[' . $from . ']';
        }

        return strtr($phpFormat, $replacements);
    }
}
