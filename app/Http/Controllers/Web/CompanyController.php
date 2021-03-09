<?php

namespace App\Http\Controllers\Web;

use App\Models\Company;
use App\Http\Controllers\Controller;
use App\Services\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class CompanyController extends Controller
{
    private $companySerice;

    /**
     * NodeRepository constructor.
     *
     * @param CompanyService $companyService
     */
    public function __construct(CompanyService $companyService)
    {
        $this->middleware('auth');
        $this->companySerice = $companyService;
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        if (Auth::user()->dashboard_view > 0)
        {
            return redirect(
                action(
                    [FacilityController::class, 'dashboard'],
                    ['facility' => Auth::user()->dashboard_view]
                )
            );
        }
        //user allowed?
        $response = Gate::inspect('viewAny', Company::class);
        if (!$response->allowed()) {
            //create errror message
            return redirect('/logout')
                ->withErrors([$response->message()]);
        }

        //is user is MANAGEMENT, show all companies
        $companies = Auth::user()->companies;
        //is user is MANAGEMENT, show all companies
        if (Gate::inspect('viewAll', Company::class)->allowed()) {
            $companies = Company::all();
        }

        foreach ($companies as $key => $company) {
            foreach ($company->facilities as $key => $facility) {
                $facility->getErrorLevel();
            }
        }

        $breadcrumbs = [
            ['name' => ""]
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'isFabButton' => true];

        return view('pages.companies.dashboard', ['pageConfigs' => $pageConfigs, 'companies' => $companies], ['breadcrumbs' => $breadcrumbs]);
    }

    /**
     * Display a listing of the resource.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //user allowed?
        $response = Gate::inspect('viewAny', Company::class);
        if (!$response->allowed()) {
            //create errror message
            return redirect('/')->withErrors([$response->message()]);
        }

        $companies = Auth::user()->companies;
        //is user is MANAGEMENT, show all companies
        if (Gate::inspect('viewAll', Company::class)->allowed()) {
            $companies = Company::all();
        }

        //build up search table 
        $searchCollection = collect([
            'table' => 'companies',
            'data' => $this->companySerice->getAllUniqueComanies(
                collect([
                    'City',
                    'Country',
                    'Owner'
                ])
            )
        ]);

        $breadcrumbs = [
            ['link' => action('Web\CompanyController@index'), 'name' => "Settings"],
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'isFabButton' => true];

        return view('pages.companies.index', ['pageConfigs' => $pageConfigs, 'searchCollection' => $searchCollection, 'companies' => $companies], ['breadcrumbs' => $breadcrumbs]);
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
        $response = Gate::inspect('create', Company::class);
        if (!$response->allowed()) {
            //create errror message 
            return redirect(action('Web\CompanyController@index'))
                ->withErrors([$response->message()]);
        }

        //Validation 
        $request->validate([
            'name' => 'required|min:3|max:20',
            'city' => 'required|min:3|max:20',
            'country' => 'required|min:3|max:20',
        ]);

        $model = $this->companySerice->createCompany(Auth::user(), collect($request->all()));

        Session::flash('message', "Node \"" . $model->name . "\" created");
        return Redirect::back();
    }

    /**
     * Display the specified resource.
     *
     * @param  Company  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Company  $company
     * @return \Illuminate\Http\Response
     */
    public function edit(Company $company)
    {
        //user allowed?
        $response = Gate::inspect('update', $company);
        if (!$response->allowed()) {
            //create errror message
            return redirect(action('Web\CompanyController@index'))
                ->withErrors([$response->message()]);
        }

        $breadcrumbs = [
            ['link' => action('Web\CompanyController@dashboard'), 'name' => "Home"],
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'isFabButton' => true];

        return view('pages.companies.edit', ['pageConfigs' => $pageConfigs, 'company' => $company], ['breadcrumbs' => $breadcrumbs]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Company $company)
    {
        //user allowed?
        $response = Gate::inspect('update', $company);
        if (!$response->allowed()) {
            //create errror message
            return redirect(action('Web\CompanyController@index'))
                ->withErrors([$response->message()]);
        }

        $request->validate([
            'name' => 'required|min:3|max:20',
            'city' => 'required|min:3|max:20',
            'country' => 'required|min:3|max:20',
        ]);

        $this->companySerice->Update($request, $company);
        Session::flash('message', 'Company Updated');

        return redirect()->action(
            [CompanyController::class, 'index']
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Company $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        //user allowed?
        $response = Gate::inspect('forceDelete', $company);
        if (!$response->allowed()) {
            //create errror message
            return redirect(action('Web\CompanyController@index'))
                ->withErrors([$response->message()]);
        }

        $this->companySerice->delete($company);
        return response()->noContent();
    }
}
