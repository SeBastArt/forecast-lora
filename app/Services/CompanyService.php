<?php

namespace App\Services;

use App\Company;
use App\Node;
use App\Repositories\Contracts\CompanyRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CompanyService
{
    private $companyRepository;

    /**
     * Create a new service instance.
     * @param  \App\Repositories\Contracts\CompanyRepository $repository
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function createCompany(Collection $companyData)
    {
        $companyData->put('user_id', Auth::user()->id); 
        return $this->companyRepository->create($companyData->toArray());
    }

    public function getUserDistinctResults(Collection $valueArray)
    {
        $resultCollection = collect();
        $table = Auth::user()->companies()->get();
        foreach ($valueArray as $key => $value) {
            if (Str::lower($value) == 'owner')
            {
                $resultCollection->put($value, collect('You'));
                continue;
            }
            $resultCollection->put($value, $table->pluck(Str::lower($value))->unique());
        }
        return $resultCollection;
    }

    public function getAdminDistinctResults(Collection $valueArray)
    {
        $resultCollection = collect();
        $table = DB::table('companies')->get();
        foreach ($valueArray as $key => $value) {
            if (Str::lower($value) == 'owner')
            {
                $resultCollection->put($value, DB::table('users')->get()->pluck('name')->unique());
                continue;
            }
            $resultCollection->put($value, $table->pluck(Str::lower($value))->unique());
        }
        return $resultCollection;
    }

    public function Update(Request $request, Company $company)
    {
        $company->name = $request->name;
        $company->city = $request->city;
        $company->country = $request->country;
        $this->companyRepository->update( $company->id, $company->toArray());
    }


    public function Delete(Company $company){
        $this->companyRepository->delete($company->id);  
    }
}