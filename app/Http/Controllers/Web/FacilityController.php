<?php

namespace App\Http\Controllers\Web;

use App\Company;
use App\Facility;
use App\Http\Controllers\Controller;
use App\Services\FacilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class FacilityController extends Controller
{
    private $facilitySerice;

    /**
     * NodeRepository constructor.
     *
     * @param $repository
     */
    public function __construct(FacilityService $facilitySerice)
    {
        $this->middleware('auth');
        $this->facilitySerice = $facilitySerice;
    }


    /**
     * Display a listing of the resource.
     *
     * @param  Company  $company
     * @return \Illuminate\Http\Response
     */
    public function index(Company $company)
    {
        //For Admin
        //$facilities = DB::table('facilities')->get();

        //for Support
        $facilities = $company->facilities;
        $searchCollection = collect([
            'table' => 'facilities',
            'data' => $searchCollection = $this->facilitySerice->getDistinctResults(
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
    public function store(Company $company, Request $request)
    {
          //Validation 
          $request->validate([
            'name' => 'required|min:5|max:255',
            'location' => 'required|min:5|max:100',
        ]);

        $model = $this->facilitySerice->createFacility($company->id, collect($request->all()));
       
        Session::flash('message', "Facility \"".$model->name."\" created");
        return Redirect::back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Facility $facility)
    {
        $breadcrumbs = [
            ['link' => action('Web\NodeController@dashboard'), 'name' => "Home"],
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'isFabButton' => true];

        return view('pages.facilities.show', ['pageConfigs' => $pageConfigs, 'facility' => $facility], ['breadcrumbs' => $breadcrumbs]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Facility $facility)
    {
        $request->validate([
            'name' => 'required|min:5|max:255',
            'location' => 'required',

        ]);

        $this->facilitySerice->Update($request, $facility);
        Session::flash('message', 'Facility Updated');

        return redirect()->action(
            [FacilityController::class, 'index'], ['company' => $facility->company->id]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Facility $facility
     * @return \Illuminate\Http\Response
     */
    public function destroy(Facility $facility)
    {
        $this->facilitySerice->delete($facility);
        return back();
    }
}
