<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Preset;
use App\Services\PresetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class PresetController extends Controller
{

    private $presetService;

    /**
     * NodeRepository constructor.
     *
     * @param $repository
     */
    public function __construct(PresetService $presetService)
    {
        //$this->middleware('auth:sanctum');
        $this->presetService = $presetService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //user allowed?
        $response = Gate::inspect('viewAny', Preset::class);
        if (!$response->allowed()) {
            //create errror message
            return redirect('/')->withErrors([$response->message()]);
        }

        // //for Account_Manager
        // $presets = Preset::where('user_id', Auth::user()->id)->get();
        // //is user is MANAGEMENT, show all companies
        // if (Gate::inspect('viewAll', Company::class)->allowed()) {
        //     $presets = Preset::all();
        // }

        //todo: this is only a test
        $presets = Preset::all();
        
        $breadcrumbs = [
            ['link' => action('Web\CompanyController@index'), 'name' => "Presets"]
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'isFabButton' => true];

        return view('pages.presets.index', ['pageConfigs' => $pageConfigs, 'presets' => $presets], ['breadcrumbs' => $breadcrumbs]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //user allowed?
        $response = Gate::inspect('create', Preset::class);
        if (!$response->allowed()) {
            //create errror message
            return redirect(action('Web\PresetController@index'))
                ->withErrors([$response->message()]);
        }

        //Validation 
        $request->validate([
            'name' => 'required|min:5|max:255',
            'description' => 'required|min:5|max:100',
        ]);

        $preset = $this->presetService->createPreset(collect($request->all()));

        Session::flash('message', "Preset \"" . $preset->name . "\" created");
        return Redirect::back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Preset  $preset
     * @return \Illuminate\Http\Response
     */
    public function show(Preset $preset)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Preset  $preset
     * @return \Illuminate\Http\Response
     */
    public function edit(Preset $preset)
    {
        //user allowed?
        $response = Gate::inspect('update', $preset);
        if (!$response->allowed()) {
            //edit errror message
            return redirect(action('Web\PresetController@index'))
                ->withErrors([$response->message()]);
        }

        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'isFabButton' => true];

        $breadcrumbs = [
            ['link' => action('Web\PresetController@index'), 'name' => "Presets"],
            ['name' => $preset->name],
        ];

        return view(
            'pages.presets.show',
            [
                'pageConfigs' => $pageConfigs,
                'preset' => $preset
            ],
            ['breadcrumbs' => $breadcrumbs]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Preset  $preset
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Preset $preset)
    {
        //user allowed?
        $response = Gate::inspect('update', $preset);
        if (!$response->allowed()) {
            //edit errror message
            return redirect(action('Web\PresetController@index'))
                ->withErrors([$response->message()]);
        }

        $request->validate([
            'name' => 'required|min:5|max:255',
            'description' => 'required',
        ]);

        $this->presetService->Update($request, $preset);
        Session::flash('message', 'Preset Updated');

        return redirect()->action(
            [PresetController::class, 'index']
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Preset  $preset
     * @return \Illuminate\Http\Response
     */
    public function destroy(Preset $preset)
    {
        //user allowed?
        $response = Gate::inspect('forceDelete', $preset);
        if (!$response->allowed()) {
            //delete errror message
            return redirect(action('Web\PresetController@index'))
                ->withErrors([$response->message()]);
        }

        $this->presetService->delete($preset);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Preset  $preset
     * @return \Illuminate\Http\Response
     */
    public function spread(Preset $preset)
    {
        //todo: transfer to api controller

        //user allowed?
        $response = Gate::inspect('spread', $preset);
        if (!$response->allowed()) {
            //spread errror message
            return redirect(action('Web\PresetController@index'))
                ->withErrors([$response->message()]);
        }

        $this->presetService->Spread($preset);
        return back();
    }
}
