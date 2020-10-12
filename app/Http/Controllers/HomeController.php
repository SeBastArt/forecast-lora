<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\NodeResource;
use Illuminate\Http\Request;
use App\Node;
use App\NodeData;
use App\FieldData;
use App\Helpers\DecodeHelper;
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
            if ($primaryField->data->count() > 0){
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
            $node = collect([
                'Node' => $userNode,
                'primaryField' => $primaryFieldCollection,
                'secondaryField' => $secondaryFieldCollection
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
