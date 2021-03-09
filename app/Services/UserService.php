<?php

namespace App\Services;

use App\Helpers\UserRole;
use App\Models\Company;
use App\Models\User;
use App\Models\Node;
use App\Repositories\Contracts\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserService
{
    private $userRepository;

    /**
     * Create a new service instance.
     * @param  \App\Repositories\Contracts\UserRepository $repository
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getFacilityArray(User $user)
    {
        $facilities = collect();
        foreach ($user->companies as $key => $company) {
            foreach ($company->facilities as $key => $facility) {
                $facilities->push(collect([
                    'id' => $facility->id,
                    'name' => $facility->name,
                    'selected' => $facility->id == $user->dashboard_view
                ]));
            }
        }
        return $facilities;
    }

    public function getCompanyArray(User $user)
    {
        //if you can edit, then you need all companies
        $allCompanies = $user->companies;
        $myCompanies = $allCompanies;
        $response = Gate::inspect('update', Auth::user());
        if ($response->allowed()) {
            $allCompanies = Company::all();
        }

        //get formatted companies 
        $companies = collect();
        foreach ($allCompanies as $key => $allCompany) {
            $selected = $myCompanies->pluck('id')->contains($allCompany->id) ? true : false;
            $companies->push(collect([
                'id' => $allCompany->id,
                'name' => $allCompany->name,
                'selected' => $selected
            ]));
        }
        return $companies;
    }

    public function Create(array $data){
        $data['name'] = $data['username']; 
        $data['roles'] = Array(UserRole::ROLE_SUPPORT); 
        $data['status'] = 'active'; 
        $data['language'] = 1; 
        return User::create([
            'username' => $data['username'],
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'roles' => $data['roles'],
            'status' => $data['status'],
            'language' => $data['language'],
        ]);
    }

    public function Update(Request $request, User $user)
    {
        $response = Gate::inspect('view', $user);
        if ($response->allowed()) {
            $user->username = isset($request->username) ? $request->username : $user->username;
            $user->name = isset($request->name) ? $request->name : $user->name;
            $user->email = isset($request->email) ? $request->email : $user->email;

            $user->language = isset($request->language) ? $request->language : $user->language;
            $user->phone = isset($request->phone) ? $request->phone : $user->phone;
            $user->address = isset($request->address) ? $request->address : $user->address;
            $user->country = isset($request->country) ? $request->country : $user->country;
            $user->dashboard_view = isset($request->dashboard_view) ? $request->dashboard_view : $user->dashboard_view;

            //status
            if (isset($request->status)) {
                switch ($request->status) {
                    case '0':
                        $user->status = 'close';
                        break;
                    case '1':
                        $user->status = 'active';
                        break;
                    default:
                        $user->status = 'close';
                        break;
                }
            }
        }
        
        $response = Gate::inspect('update', $user);
        if ($response->allowed()) {
            $user->roles = isset($request->roles) ? $request->roles : $user->roles;

            if (isset($request->companies)) {
                $companies = Company::whereIn('id', $request->companies)->get();
                $user->companies()->sync($companies);
            }
        }

        $user->Save();
        return $user;
    }

    public function Delete(User $user){
        $this->userRepository->delete($user->id);  
    }
}