<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\NodeResource;
use Illuminate\Http\Request;
use App\Node;
use App\NodeData;
use App\FieldData;
use App\Forecast;
use App\Helpers\DecodeHelper;
use App\Helpers\MyHelper;
use App\Role\UserRole;
use App\Weather;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $colUserNode = collect(Auth::user()->nodes);

        $colNode = collect();

        foreach ($colUserNode as $userNode) {
            if($userNode->fields->count() == 0){ continue; }

            $mainField = $userNode->fields->sortBy('position')->first();
            $collMainField = collect([
                'unit' => $mainField->unit
            ]);
           
            if ($mainField->data->count() > 0) {
                $collMainField->put('min', number_format($mainField->data->where('created_at', '>', Carbon::now()->subMinutes(1440))->min('value'), 1, '.', ''));
                $collMainField->put('max', number_format($mainField->data->where('created_at', '>', Carbon::now()->subMinutes(1440))->max('value'), 1, '.', ''));
                $collMainField->put('last',  collect([
                    'value' => $mainField->data->last()->value,
                    'timestamp' => $mainField->data->last()->created_at->format('H:i:s')
                ])); 
            }
 
            if ($userNode->fields->count() > 1) {
                $secField = $userNode->fields->sortBy('position')->skip(1)->first();
                if ($secField->data->count() > 0) {
                    $colSecField = collect([
                        'unit' => $secField->unit,
                        'min' => number_format($secField->data->where('created_at', '>', Carbon::now()->subMinutes(1440))->min('value'), 1, '.', ''), //format to one digit,
                        'max' => number_format($secField->data->where('created_at', '>', Carbon::now()->subMinutes(1440))->max('value'), 1, '.', ''), //format to one digit,
                        'last' => $secField->data->last()->value,
                    ]);
                }
            }

            if ($userNode->city()->first() > 0) {
                $forecast = Forecast::where('city_id', $userNode->city()->first()->id)->first();
                if (isset($forecast)) {
                    $forecastitem = $forecast->forecastItems->first();
                    $mainWeatherIcon = MyHelper::getIconClass(Weather::where('id', $forecast->forecastItems[0]->weather_id)->first()->api_id);
                }
          
                $colForecast = collect();
                $dayArray = collect();
                $tempArray = collect();
                $inDay = false;
                $weekMap = [
                    0 => 'So',
                    1 => 'Mo',
                    2 => 'Di',
                    3 => 'Mi',
                    4 => 'Do',
                    5 => 'Fr',
                    6 => 'Sa',
                ];

                $actDay = $weekMap[Carbon::parse($forecast->forecastItems[0]->valid_from)->dayOfWeek];
                for ($i = 0; $i < $forecast->forecastItems->count(); $i++) {
                    $time = Carbon::parse($forecast->forecastItems[$i]->valid_from);
                    $dayArray->push(Weather::where('id', $forecast->forecastItems[$i]->weather_id)->first()->api_id);
                    $tempArray->push(number_format($forecast->forecastItems[$i]->temp,0)); 
                    if ($time->hour == 0) {
                        $forecastIcon = collect([
                            'icon' => MyHelper::getIconClass($dayArray->min()),
                            'day' => $actDay,
                            'minTemp' => $tempArray->min(),
                            'maxTemp' => $tempArray->max(),
                        ]);
                        $colForecast->push($forecastIcon);
                        break;
                    }
                }

                for ($i = 0; $i < $forecast->forecastItems->count(); $i++) {
                    $time = Carbon::parse($forecast->forecastItems[$i]->valid_from);
                    if ($time->hour > 18 && $inDay == true) {
                        $inDay = false;   
                        $dayArray->push(Weather::where('id', $forecast->forecastItems[$i]->weather_id)->first()->api_id);
                        $tempArray->push(number_format($forecast->forecastItems[$i]->temp,0)); 
                        $forecast = collect([
                            'icon' => MyHelper::getIconClass($dayArray->min()),
                            'day' => $weekMap[$time->dayOfWeek],
                            'minTemp' => $tempArray->min(),
                            'maxTemp' => $tempArray->max(),
                        ]);
                        $colForecast->push($forecast);
                    }

                    if ($time->hour > 5 && $inDay == true) {
                        $dayArray->push(Weather::where('id', $forecast->forecastItems[$i]->weather_id)->first()->api_id);
                        $tempArray->push(number_format($forecast->forecastItems[$i]->temp,0)); 
                    }

                    if ($time->hour == 0 && $inDay == false) {
                        $inDay = true;
                    }
                }
            }
            $node = collect([
                'userNode' => $userNode,
                'mainField' => $collMainField,
            ]);
            
            if (isset($weatherIcon)) {$node->put('mainWeatherIcon', $mainWeatherIcon);}
            if (isset($collSecField)) {$node->put('secField', $colSecField);}
            if (isset($colForecast)) {$node->put('forecasts', $colForecast);}
   
            $colNode->push($node);
        }

        //return response()->json($userNodeCollection,200,[],JSON_PRETTY_PRINT);
        $breadcrumbs = [
            ['link' => action('HomeController@index'), 'name' => "Home"],
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'bodyCustomClass' => 'menu-collapse', 'isFabButton' => true];

        return view('pages.home', ['pageConfigs' => $pageConfigs, 'Nodes' => $colNode], ['breadcrumbs' => $breadcrumbs]);
    }
}
