<?php

namespace App\Http\Controllers\Web;

use App\Company;
use App\Facility;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Node;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Repositories\Contracts\NodeRepository;
use App\Services\NodeService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ViewErrorBag;
use App\Services\ForecastService;

class NodeController extends Controller
{
    /**
     * The node repository instance.
     *
     * @var \App\Repositories\Contracts\NodeRepository
     */
    private $nodeRepository;
    private $nodeService = null;
    private $forecastService;

    /**
     * NodeRepository constructor.
     *
     * @param $nodeRepository
     * @param $nodeService
     */
    public function __construct(NodeRepository $nodeRepository, NodeService $nodeService, ForecastService $forecastService)
    {
        $this->middleware('auth');
        $this->repository = $nodeRepository;
        $this->nodeService = $nodeService;
        $this->forecastService = $forecastService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \App\Facility  $facility
     * @return \Illuminate\Http\Response
     */
    public function index(Facility $facility)
    {
        $nodes = $facility->nodes;
        
        $breadcrumbs = [
            ['link' => action('Web\CompanyController@index'), 'name' => "Settings"],
            ['link' => action('Web\FacilityController@index', ['company' => $facility->company->id]), 'name' => $facility->company->name],
            ['link' => action('Web\NodeController@index', ['facility' => $facility->id]), 'name' => $facility->name],
        ];

        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'isFabButton' => true];

        return view('pages.nodes.index', ['pageConfigs' => $pageConfigs, 'facility' => $facility, 'nodes' => $nodes], ['breadcrumbs' => $breadcrumbs]);
    }

    public function dashboard()
    {
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
    
            return view('pages.nodes.dashboard', ['pageConfigs' => $pageConfigs, 'Nodes' => $colNode], ['breadcrumbs' => $breadcrumbs]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create()
    {
        if (Auth::user()->cannot('create', Node::class)) { return redirect('/'); }
        $allNodes = collect();
        $companies = Auth::user()->companies->load('facilities');
        foreach ($companies as $company) {
            $facilities = $company->facilities;
            foreach ($facilities as $facility) {
                $nodes = $facility->nodes;
                foreach ($nodes as $node) {
                    $allNodes->push($node);
                }
            }
        }

        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"], ['link' => action('Web\NodeController@index'), 'name' => "Nodes"],
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'isFabButton' => true];
        return view('pages.nodes.create', ['pageConfigs' => $pageConfigs, 'Nodes' => $allNodes], ['breadcrumbs' => $breadcrumbs]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Facility $facility, Request $request)
    {
        //user allowed?
        $response = Gate::inspect('create', Node::class);
        if(!$response->allowed()){
            //create errror message
            return Redirect::back()->withErrors([$response->message()]);
        }

        //Validation 
        $request->validate([
            'name' => 'required|min:5|max:255',
            'dev_eui' => 'required',
            'node_type_id' => 'gt:0',
        ]);

        $model = $this->nodeService->createNode($facility->id, collect($request->all()));
       
        Session::flash('message', "Node \"".$model->name."\" created");
        return Redirect::back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Node  $node
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show(Node $node)
    {
        $response = Gate::inspect('view', $node);
        if(!$response->allowed()){
            return Redirect::back()->withErrors([$response->message()]);
        }

        $primFieldInfo = $this->nodeService->getPrimFieldInfo($node);
        $secFieldInfo = $this->nodeService->getSecFieldInfo($node);
        $myFields = collect([
        ]);
        if (isset($primFieldInfo)) {$myFields->put('primField', $primFieldInfo);}
        if (isset($primFieldInfo)) {$myFields->put('secField', $secFieldInfo);}

        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'isFabButton' => true];

        $breadcrumbs = [
            ['link' => action('Web\CompanyController@index'), 'name' => "Settings"],
            ['link' => action('Web\FacilityController@index', ['company' => $node->facility->company->id]), 'name' => $node->facility->company->name],
            ['link' => action('Web\NodeController@index', ['facility' => $node->facility->id]), 'name' => $node->facility->name],
            ['link' => action('Web\NodeController@show', ['node' => $node->id]), 'name' => $node->name],
        ];

        return view(
            'pages.nodes.show',
            [
                'pageConfigs' => $pageConfigs,
                'Node' => $node,
                'Fields' => $myFields
            ],
            ['breadcrumbs' => $breadcrumbs]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Node  $node
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Node $node)
    {
        //user allowed?
        $response = Gate::inspect('update', $node);
        if(!$response->allowed()){
            //create errror message
            return Redirect::back()->withErrors([$response->message()]);
        }

        $request->validate([
            'name' => 'required|min:5|max:255',
            'dev_eui' => 'required',
            'nodetype' => 'gt:0'
        ]);

        $this->nodeService->Update($request, $node);
        Session::flash('message', 'Node Updated');

        return redirect()->action(
            [NodeController::class, 'index'], ['facility' => $node->facility->id]
        );

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Node  $node
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Node $node)
    {
        $response = Gate::inspect('delete', $node);
        if(!$response->allowed()){
            return response()->json($response->message(), 401, [], JSON_PRETTY_PRINT);
        }

        $this->nodeService->Delete($node);
        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Node  $node
     * @return \Illuminate\Http\RedirectResponse
     */
    public function position(Request $request, Node $node)
    {
        if ($request->startPos > 0 && $request->newPos <= $node->fields->count()) {
            $node->fields->where('position', $request->startPos)->first()->update(['position' => $request->newPos]);
        }
    }
}
