<?php

namespace App\Http\Controllers\Web;

use App\Models\Company;
use App\Models\Facility;
use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\File;
use Illuminate\Support\Facades\Auth;
use App\Models\Node;
use App\Models\Preset;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Services\NodeService;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ViewErrorBag;

class NodeController extends Controller
{
    /**
     * The node repository instance.
     *
     * @var \App\Services\NodeService
     */
    private $nodeService = null;

    /**
     * NodeRepository constructor.
     *
     * @param $nodeRepository
     * @param $nodeService
     */
    public function __construct(NodeService $nodeService)
    {
        $this->middleware('auth');
        $this->nodeService = $nodeService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \App\Facility  $facility
     * @return \Illuminate\Http\Response
     */
    public function index(Facility $facility)
    {
        //dd($facility->company->name);
        //user allowed?
        $response = Gate::inspect('viewAny', Node::class);
        if (!$response->allowed()) {
            //create errror message
            return redirect(
                action(
                    'Web\FacilityController@index',
                    ['company' => $facility->company->id]
                )
            )
                ->withErrors([$response->message()]);
        }

        $nodes = $facility->nodes;

        //for Account_Manager
        // $presets = Preset::where('user_id', Auth::user()->id)->get();
        // //is user is MANAGEMENT, show all companies
        // if (Gate::inspect('viewAll', Preset::class)->allowed()) {
        //     $presets = Preset::all();
        // }

        //todo: this is only a test
        $presets = Preset::all();

        $breadcrumbs = [
            ['link' => action('Web\CompanyController@index'), 'name' => "Settings"],
            ['link' => action('Web\FacilityController@index', ['company' => $facility->company->id]), 'name' => $facility->company->name],
            ['link' => action('Web\NodeController@index', ['facility' => $facility->id]), 'name' => $facility->name],
        ];

        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'isFabButton' => true];

        return view('pages.nodes.index', ['pageConfigs' => $pageConfigs, 'presets' => $presets, 'facility' => $facility, 'nodes' => $nodes], ['breadcrumbs' => $breadcrumbs]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create()
    {
        //
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
        if (!$response->allowed()) {
            //create errror message
            return redirect(
                action(
                    'Web\NodeController@index',
                    ['facility' => $facility->id]
                )
            )
                ->withErrors([$response->message()]);
        }
        //Validation 
        $request->validate([
            'name' => 'required|min:5|max:255',
            'dev_eui' => 'required',
            'node_type_id' => 'required|gt:0',
            'preset_id' => 'sometimes|gt:-1'
        ]);

        $model = $this->nodeService->createNode($facility, collect($request->all()));

        Session::flash('message', "Node \"" . $model->name . "\" created");
        return Redirect::back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Node  $node
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show(Node $node, Request $request)
    {
        //user allowed?
        $response = Gate::inspect('view', $node);
        if (!$response->allowed()) {
            //create errror message
            return redirect(
                action(
                    'Web\NodeController@index',
                    ['facility' => $node->facility->id]
                )
            )
                ->withErrors([$response->message()]);
        }
        
        //without Request the last 24h
        $start = Carbon::now()->subHours(24);
        $end = Carbon::now();

        //dd(Carbon::parse($request['timestamp'])->isoFormat('D.MM.YYYY HH:mm'));
        if (isset($request['timestamp'])) {
            $start = Carbon::parse($request['timestamp'])->startOfDay();
            $end = Carbon::parse($request['timestamp'])->endOfDay();
        }

        if (isset($request['startDate']) && isset($request['startTime'])) {
            $start = Carbon::parse(str_replace('/', '-', $request['startDate']) . ' ' . $request['startTime']);
        }

        if (isset($request['endDate']) && isset($request['endTime'])) {
            $end = Carbon::parse(str_replace('/', '-', $request['endDate']) . ' ' . $request['endTime']);
        }

        //if limit exceeded than change timestamp
        //$node->getErrorLevel() will do the rest in blade file
        $alert = null;
        $alertTimestamp = null;
        $alertField = '';
        $timestamp = null;
       
        if($end->greaterThan(Carbon::now()->subHours(12))){
            $timestamp = Carbon::now()->isoFormat('D.MM.YYYY HH:mm');
        }
        foreach ($node->fields()->get() as $fieldKey => $field) {
            if ($field->isExceeded() == true) {
                $alert = $field->alerts()->first();
                $alertField = $field->name;
                $alertTimestamp = Carbon::parse($alert->exceed_timestamp)->isoFormat('D.MM.YYYY HH:mm');
                if (isset($request['timestamp'])) {
                    $timestamp = Carbon::parse($alert->exceed_timestamp)->isoFormat('D.MM.YYYY HH:mm');
                }
                break;
            }
        }
        if($alertTimestamp != null){
            $alert = collect([
                'alertTimestamp' => $alertTimestamp,
                'field_name' =>  $alertField,
                'upper_limit' => $field->upper_limit,
                'lower_limit' => $field->lower_limit
            ]);
        }
        
        if (isset($request['timestamp'])) {
            $timestamp = Carbon::parse($request['timestamp'])->isoFormat('D.MM.YYYY HH:mm');
        }
        $time = collect([
            'startDate' => $start->isoFormat('D.MM.YYYY'),
            'startTime' => $start->isoFormat('HH:mm'),
            'endDate' => $end->isoFormat('D.MM.YYYY'),
            'endTime' => $end->isoFormat('HH:mm'),
            'timestamp' => $timestamp,
        ]);

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
                'node' => $node,
                'time' => $time,
                'alert' => $alert
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
        if (!$response->allowed()) {
            //create errror message
            return redirect(
                action(
                    [NodeController::class, 'index'],
                    ['facility' => $node->facility->id]
                )
            )
                ->withErrors([$response->message()]);
        }

        $request->validate([
            'name' => 'required|min:5|max:255',
            'dev_eui' => 'required',
            'nodetype' => 'gt:0',
            'preset_id' => 'sometimes|gt:-1'
        ]);

        $this->nodeService->Update($request, $node);
        Session::flash('message', 'Node Updated');

        return redirect()->action(
            [NodeController::class, 'show'],
            ['node' => $node->id]
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
        //user allowed?
        $response = Gate::inspect('delete', $node);
        if (!$response->allowed()) {
            //create errror message
            return redirect(
                action(
                    'Web\NodeController@index',
                    ['facility' => $node->facility->id]
                )
            )
                ->withErrors([$response->message()]);
        }

        $this->nodeService->Delete($node);
        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Node  $node
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deletepreset(Node $node)
    {
        //user allowed?
        $response = Gate::inspect('update', $node);
        if (!$response->allowed()) {
            //create errror message
            return redirect(
                action(
                    'Web\NodeController@index',
                    ['facility' => $node->facility->id]
                )
            )
                ->withErrors([$response->message()]);
        }

        $this->nodeService->DeletePreset($node);
        return back();
    }

    /**
     * Reset the alert on this node.
     *
     * @param  \App\Node  $node
     * @return \Illuminate\Http\RedirectResponse
     */
    public function alert_reset(Node $node)
    {
        //user allowed?
        $response = Gate::inspect('update', $node);
        if (!$response->allowed()) {
            //create errror message
            return redirect(
                action(
                    'Web\NodeController@index',
                    ['facility' => $node->facility->id]
                )
            )
                ->withErrors([$response->message()]);
        }

        $this->nodeService->ResetAlert($node);
        return redirect()->action(
            [NodeController::class, 'show'],
            ['node' => $node->id]
        );
    }


    public function fileUpload(Facility $facility, Request $request){
        //user allowed?
        $response = Gate::inspect('update', $facility);
        if (!$response->allowed()) {
            //create errror message
            return redirect(
                action(
                    'Web\NodeController@index',
                    ['facility' => $facility->id]
                )
            )
                ->withErrors([$response->message()]);
        }

        $request->validate([
        'file' => 'required|file|max:2048'
        ]);

        if( $facility->file != null)
        {
            Storage::delete($facility->file->file_path);
            $facility->file->delete();
        }

        $fileModel = new File;
        if($request->file()) {
            $fileName = $facility->id.'_'.$request->file->getClientOriginalName();
            //laravel bug: if you save in public, you have to add ist to path
            $filePath = 'public/'.$request->file('file')->storeAs('uploads', $fileName, 'public');

            //generate Model
            $fileModel->name = $fileName;
            $fileModel->file_path = $filePath;
            $fileModel->facility()->associate($facility);
            $fileModel->save();

            return back()
            ->with('success','File has been uploaded.')
            ->with('file', $fileName);
        }
   }

   public function fileDownload(Facility $facility, Request $request){

      //user allowed?
      $response = Gate::inspect('view', $facility);
      if (!$response->allowed()) {
          //create errror message
          return redirect(
              action(
                  'Web\NodeController@index',
                  ['facility' => $facility->id]
              )
          )
              ->withErrors([$response->message()]);
      }

        $file = Storage::path($facility->file->file_path);

        $headers = array(
                'Content-Type: application/pdf',
                );

        return response()->download($file, $facility->file->name, $headers);
   }

   public function fileRemove(Facility $facility, Request $request)
   {
        //user allowed?
        $response = Gate::inspect('update', $facility);
        if (!$response->allowed()) {
            //create errror message
            return redirect(
                action(
                    'Web\NodeController@index',
                    ['facility' => $facility->id]
                )
            )
                ->withErrors([$response->message()]);
        }

        $facility->file->delete();
        return response()->noContent();
   }
}
