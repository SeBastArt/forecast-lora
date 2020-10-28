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

        $userNodes = collect(Auth::user()->nodes);

        $userNodeCollection = collect();

        foreach ($userNodes as $userNode) {
            $primaryField = $userNode->fields->sortBy('position')->first();
            $primaryFieldCollection = null;
            if ($primaryField->data->count() > 0) {
                $primaryFieldCollection = collect([
                    'unit' => $primaryField->unit,
                    'min' => number_format($primaryField->data->where('created_at', '>', Carbon::now()->subMinutes(1440))->min('value'), 1, '.', ''), //format to one digit,
                    'max' => number_format($primaryField->data->where('created_at', '>', Carbon::now()->subMinutes(1440))->max('value'), 1, '.', ''), //format to one digit,
                    'last'  =>  collect([
                        'value' => $primaryField->data->last()->value,
                        'timestamp' => $primaryField->data->last()->created_at->format('H:i:s')
                    ])
                ]);
            }

            $secondaryField = $userNode->fields->count() > 1 ? $userNode->fields->sortBy('position')->skip(1)->first() : $userNode->fields->sortBy('position')->first();
            $secondaryFieldCollection = null;
            if ($secondaryField->data->count() > 0) {
                $secondaryFieldCollection = collect([
                    'unit' => $secondaryField->unit,
                    'min' => number_format($secondaryField->data->where('created_at', '>', Carbon::now()->subMinutes(1440))->min('value'), 1, '.', ''), //format to one digit,
                    'max' => number_format($secondaryField->data->where('created_at', '>', Carbon::now()->subMinutes(1440))->max('value'), 1, '.', ''), //format to one digit,
                    'last' => $secondaryField->data->last()->value,
                ]);
            }

            $weatherIconClass = "";
            if ($userNode->city()->first() !== null) {
                $forecast = Forecast::where('city_id', $userNode->city()->first()->id)->first();
                if (isset($forecast)) {
                    $forecastitem = $forecast->forecastItems->first();
                    $weatherIconClass = MyHelper::getIconClass(Weather::where('id', $forecast->forecastItems[0]->weather_id)->first()->api_id);
                }
            }

            $forecastIcons = collect();
            $dayArray = collect();
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
                if ($time->hour == 0) {
                    $forecastIcon = collect([
                        'icon' => MyHelper::getIconClass($dayArray->min()),
                        'day' => $actDay
                    ]);
                    $forecastIcons->push($forecastIcon);
                    break;
                }
            }

            for ($i = 0; $i < $forecast->forecastItems->count(); $i++) {
                $time = Carbon::parse($forecast->forecastItems[$i]->valid_from);
                if ($time->hour > 18 && $inDay == true) {
                    $inDay = false;   
                    $dayArray->push(Weather::where('id', $forecast->forecastItems[$i]->weather_id)->first()->api_id);
                    $forecastIcon = collect([
                        'icon' => MyHelper::getIconClass($dayArray->min()),
                        'day' => $weekMap[$time->dayOfWeek]
                    ]);
                    $forecastIcons->push($forecastIcon);
                }

                if ($time->hour > 5 && $inDay == true) {
                    $dayArray->push(Weather::where('id', $forecast->forecastItems[$i]->weather_id)->first()->api_id);
                }

                if ($time->hour == 0 && $inDay == false) {
                    $inDay = true;
                }
            }

            $node = collect([
                'Node' => $userNode,
                'primaryField' => $primaryFieldCollection,
                'secondaryField' => $secondaryFieldCollection,
                'weatherIconClass' => $weatherIconClass,
                'forecasts' => $forecastIcons
            ]);


            $userNodeCollection->push($node);
        }
        //return response()->json($userNodeCollection,200,[],JSON_PRETTY_PRINT);
        $breadcrumbs = [
            ['link' => action('HomeController@index'), 'name' => "Home"],
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'bodyCustomClass' => 'menu-collapse', 'isFabButton' => true];

        return view('pages.home', ['pageConfigs' => $pageConfigs, 'userNodeCollection' => $userNodeCollection], ['breadcrumbs' => $breadcrumbs]);
    }
}
