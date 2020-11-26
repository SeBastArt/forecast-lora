<?php

namespace App\Http\Controllers\Web;

use App\Company;
use App\Http\Controllers\Controller;
use App\Services\CompanyService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class CompanyController extends Controller
{
    private $companySerice;

    /**
     * NodeRepository constructor.
     *
     * @param $repository
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
        $user = Auth::user();
        $companies = $user->companies->all();
        
        $breadcrumbs = [
            ['link' => action('Web\CompanyController@dashboard'), 'name' => "Company Dashboard"]
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'isFabButton' => true];
    
        return view('pages.companies.dashboard', ['pageConfigs' => $pageConfigs, 'companies' => $companies, 'user' => $user], ['breadcrumbs' => $breadcrumbs]);
    }

    /**
     * Display a listing of the resource.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        
        //$companies = collect(Auth::user()->companies());
        $companies = $user->companies()->get();

        $searchCollection = collect([
            'table' => 'companies',
            'data' => $this->companySerice->getUserDistinctResults(
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
        //Validation 
        $request->validate([
            'name' => 'required|min:5|max:255',
            'city' => 'required|min:5|max:100',
            'country' => 'required|min:5|max:100',
        ]);

        $model = $this->companySerice->createCompany(collect($request->all()));
       
        Session::flash('message', "Node \"".$model->name."\" created");
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
        $breadcrumbs = [
            ['link' => action('Web\NodeController@dashboard'), 'name' => "Home"],
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'isFabButton' => true];

        return view('pages.companies.show', ['pageConfigs' => $pageConfigs, 'company' => $company], ['breadcrumbs' => $breadcrumbs]);
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
    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|min:5|max:255',
            'city' => 'required',
            'country' => 'required',
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
        $this->companySerice->delete($company);
        return back();
    }
}
