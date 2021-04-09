<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\NodeResource;
use Illuminate\Http\Request;
use App\Node;
use App\NodeData;
use App\FieldData;
use App\Models\Forecast;
use App\Helpers\Alert;
use App\Helpers\DecodeHelper;
use App\Helpers\MyHelper;
use App\Jobs\ProcessMails;
use App\Mail\AlertTest;
use App\Services\ForecastService;
use App\User;
use App\Weather;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    private $forecastService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ForecastService $forecastService)
    {
        $this->middleware('auth:web');
        $this->forecastService = $forecastService; 
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        //$colUserNode = collect(Auth::user()->nodes);
        $colNode = collect();
        $colUserNode = collect();
        $company = Auth::user()->companies->first();
        foreach (Auth::user()->companies as $key => $company) {
            foreach ($company->facilities as $key => $facility) {
                foreach ($facility->nodes as $key => $node) {
                    $colUserNode->push($node);
                }
            }
        }
        //$facility = $company->facilities->first();
       // $colUserNode = collect($facility->nodes);
        

        foreach ($colUserNode as $userNode) {
            if($userNode->fields->count() == 0){ continue; }
            $mainWeatherIcon = null;
            $collSecField = null;
            $cityForecastColl = null;
            $collMainField = null;

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
                    $collSecField = collect([
                        'unit' => $secField->unit,
                        'min' => number_format($secField->data->where('created_at', '>', Carbon::now()->subMinutes(1440))->min('value'), 1, '.', ''), //format to one digit,
                        'max' => number_format($secField->data->where('created_at', '>', Carbon::now()->subMinutes(1440))->max('value'), 1, '.', ''), //format to one digit,
                        'last' => $secField->data->last()->value,
                    ]);
                }
            }

            if ($userNode->city_id > 0) {
                $city = $userNode->city()->first();
                $mainWeatherIcon = $this->forecastService->getMainWeatherIcon($city);
                $cityForecastColl = $this->forecastService->getWeatherForecast($city);
            }
            $node = collect([
                'userNode' => $userNode,
                'mainField' => $collMainField,
            ]);

            if (isset($mainWeatherIcon)) {$node->put('mainWeatherIcon', $mainWeatherIcon);}
            if (isset($collSecField)) {$node->put('secField', $collSecField);}
            if (isset($cityForecastColl)) {$node->put('cityForecast', $cityForecastColl);}
   
            $colNode->push($node);
        }

        //return response()->json($userNodeCollection,200,[],JSON_PRETTY_PRINT);
        $breadcrumbs = [
            ['link' => action('Web\NodeController@dashboard'), 'name' => "Home"],
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => false, 'bodyCustomClass' => 'menu-collapse', 'isFabButton' => true];

        return view('pages.home', ['pageConfigs' => $pageConfigs, 'Nodes' => $colNode], ['breadcrumbs' => $breadcrumbs]);
    }
}
