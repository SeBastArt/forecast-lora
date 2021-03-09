<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Node;
use App\Models\User;
use App\Repositories\Contracts\CompanyRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
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

    public function createCompany(User $user, Collection $companyData)
    {
        $newCompany = $this->companyRepository->create($companyData->toArray());
        $newCompany->users()->attach($user->id);
        return $newCompany;
    }

    public function getAllUniqueComanies(Collection $valueArray)
    {
        $resultCollection = collect();
        $companies = Auth::user()->companies()->get();
        //is user is MANAGEMENT, show all companies
        if (Gate::inspect('viewAll', Company::class)->allowed()) {
            $companies = Company::all();
        }
        foreach ($valueArray as $key => $value) {
            if (Str::lower($value) == 'owner')
            {
                $user_names = collect();
                foreach ($companies as $key => $company) {
                    foreach ($company->users_name() as $key => $user_name) {
                        $user_names->push($user_name);
                    }
                }
                $filtered_usernames =  $user_names->unique();
                $resultCollection->put($value, $filtered_usernames); 
                continue;
            }
            $resultCollection->put($value, $companies->pluck(Str::lower($value))->unique());
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