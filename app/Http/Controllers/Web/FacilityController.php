<?php

namespace App\Http\Controllers\Web;

use App\Facades\Trace;
use App\Models\Company;
use App\Models\Facility;
use App\Http\Controllers\Controller;
use App\Services\FacilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class FacilityController extends Controller
{
    private $facilityService;

    /**
     * NodeRepository constructor.
     *
     * @param \App\Services\FacilityService $facilityService
     */
    public function __construct(FacilityService $facilityService)
    {
        $this->middleware('auth');
        $this->facilityService = $facilityService;
    }

    /**
     * Display a Dashboard of all Nodes.
     *
     * @param \App\Models\Facility $facility
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function dashboard(Facility $facility)
    {
        //Span Start
        Trace::StartSpan('app.facility-controller.dashboard');
        $response = Gate::inspect('view', $facility);
        if (!$response->allowed()) {
            $breadcrumbs = [
                ['link' => action('Web\FacilityController@dashboard', ['facility' => $facility->id]), 'name' => "Home"],
            ];
            //Pageheader set true for breadcrumbs
            $pageConfigs = ['pageHeader' => true, 'isFabButton' => true];

            return view('pages.facilities.dashboard', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs]);
        }
        $DataCollection = $this->facilityService->getDashboardData($facility);

        $breadcrumbs = [
            ['link' => action('Web\CompanyController@dashboard'), 'name' => "Companies Dashboard"],
            ['name' => $facility->name],
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'bodyCustomClass' => 'menu-collapse', 'isFabButton' => true];

        //Span End
        Trace::EndSpan();
        return view('pages.facilities.dashboard', ['pageConfigs' => $pageConfigs, 'facility' => $facility, 'nodes' => $DataCollection], ['breadcrumbs' => $breadcrumbs]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Company  $company
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function index(Company $company)
    {
        //For Admin
        //$facilities = DB::table('facilities')->get();

        //user allowed?
        $response = Gate::inspect('view', $company);
        if (!$response->allowed()) {
            //create errror message
            return redirect(
                action(
                    'Web\CompanyController@index'
                )
            )
                ->withErrors([$response->message()]);
        }

        //for Support
        $facilities = $company->facilities;

        //build up search table
        $searchCollection = collect([
            'table' => 'facilities',
            'data' => $this->facilityService->getDistinctResults(
                $facilities,
                collect([
                    'Name',
                    'Location',
                ])
            )
        ]);

        $breadcrumbs = [
            ['link' => action('Web\CompanyController@index'), 'name' => "Settings"],
            ['link' => action('Web\FacilityController@index', ['company' => $company->id]), 'name' => $company->name],
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'isFabButton' => true];

        return view('pages.facilities.index', ['pageConfigs' => $pageConfigs, 'searchCollection' => $searchCollection, 'company' => $company, 'facilities' => $facilities], ['breadcrumbs' => $breadcrumbs]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return int
     */
    public function create(): int
    {
        return 0;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Company $company
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Company $company, Request $request)
    {
        //user allowed?
        $response = Gate::inspect('create', Facility::class);
        if (!$response->allowed()) {
            //create error message
            return redirect(
                action(
                    'Web\FacilityController@index',
                    ['company' => $company->id]
                )
            )
                ->withErrors([$response->message()]);
        }

        //Validation
        $request->validate([
            'name' => 'required|min:4|max:255',
            'location' => 'required|min:4|max:100',
        ]);

        $model = $this->facilityService->createFacility($company, collect($request->all()));

        Session::flash('message', "Facility \"" . $model->name . "\" created");
        return Redirect::back();
    }

    /**
     * Display the specified resource.
     *
     * @param Facility $facility
     * @return int
     */
    public function show(Facility $facility): int
    {
        return 0;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Facility  $facility
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function edit(Facility $facility)
    {
        //user allowed?
        $response = Gate::inspect('update', $facility);
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

        $breadcrumbs = [
            ['link' => action('Web\CompanyController@dashboard'), 'name' => "Home"],
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'isFabButton' => true];

        return view('pages.facilities.edit', ['pageConfigs' => $pageConfigs, 'facility' => $facility], ['breadcrumbs' => $breadcrumbs]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Facility $facility
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, Facility $facility)
    {
        //user allowed?
        $response = Gate::inspect('update', $facility);
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

        $request->validate([
            'name' => 'required|min:4|max:255',
            'location' => 'required',

        ]);

        $this->facilityService->Update($request, $facility);
        Session::flash('message', 'Facility Updated');

        return redirect()->action(
            [FacilityController::class, 'index'],
            ['company' => $facility->company->id]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Facility $facility
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Facility $facility)
    {
        //user allowed?
        $response = Gate::inspect('delete', $facility);
        if (!$response->allowed()) {
            //create error message
            return redirect(
                action(
                    'Web\FacilityController@index',
                    ['company' => $facility->company->id]
                )
            )
                ->withErrors([$response->message()]);
        }

        $this->facilityService->delete($facility);
        return back();
    }
}
