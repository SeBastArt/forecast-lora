<?php

namespace App\Http\Controllers\Web;

use App\Helpers\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AlertAddress;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private $userService;

    /**
     * NodeRepository constructor.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->middleware('auth');
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //user allowed?
        $response = Gate::inspect('viewAny', User::class);
        if (!$response->allowed()) {
            //create error message
            return redirect(
                action(
                    'Web\CompanyController@dashboard'
                )
            )
                ->withErrors([$response->message()]);
        }

        $users = User::all();
        $breadcrumbs = [
            ['name' => "All Users"]
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'isFabButton' => true];

        return view('pages.users.index', ['pageConfigs' => $pageConfigs, 'users' => $users], ['breadcrumbs' => $breadcrumbs]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return Response
     */
    public function show(User $user)
    {
        //user allowed?
        $response = Gate::inspect('view', $user);
        if (!$response->allowed()) {
            //create error message
            return redirect('/logout')
                ->withErrors([$response->message()]);
        }

        $tokens = $user->tokens()->get();

        $companies = $user->companies->all();
        $alertAddresses = $user->alertAddresses()->get();

        $breadcrumbs = [
            ['link' => action('Web\UserController@index'), 'name' => "All Users"], ['name' => $user->name ]
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'isFabButton' => true];

        return view('pages.users.show',
            [
                'pageConfigs' => $pageConfigs,
                'companies' => $companies,
                'tokens' => $tokens,
                'user' => $user,
                'alertAddresses' => $alertAddresses,
            ],
            [
                'breadcrumbs' => $breadcrumbs
            ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return Response
     */
    public function edit(User $user)
    {
        //user allowed?
        $response = Gate::inspect('view', $user);
        if (!$response->allowed()) {
            //create error message
            return redirect(
                action(
                    'Web\UserController@show',
                    ['user' => $user->id]
                )
            )
                ->withErrors([$response->message()]);
        }

        $companies = $this->userService->getCompanyArray($user);
        $facilities = $this->userService->getFacilityArray($user);

        //get formatted roles
        //if you can edit, then you need all roles
        $userRoles = collect();
        foreach ($user->getRoles() as $key => $userRole) {
            $userRoles->push(UserRole::getRoleList()[$userRole]); //nessessary for more than one role
        }

        $response = Gate::inspect('update', $user);
        if ($response->allowed()) {
            $userRoles = UserRole::getRoleList();
        }

        $breadcrumbs = [
            ['link' => action('Web\UserController@index'), 'name' => "All Users"],
            ['link' => action('Web\UserController@show', ['user' => $user->id]), 'name' => $user->name],
            ['name' => "Edit"]
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'isFabButton' => true];
        return view('pages.users.edit',
            [
                'pageConfigs' => $pageConfigs,
                'userRoles' => $userRoles,
                'user' => $user,
                'companies' => $companies,
                'facilities' => $facilities,
            ],
            [
                'breadcrumbs' => $breadcrumbs
            ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function update(Request $request, User $user)
    {
        //Validation
        $request->validate([
            'username' => 'sometimes|required|string|min:5|max:50',
            'name' => 'sometimes|required|string|min:5|max:50',
            'email' => 'sometimes|required|email',
            'roles' => 'sometimes|required|array',
            'status' => 'sometimes|required|numeric|between:0,1',
            'companies' => 'sometimes|required|array',
            'dashboard_view' => 'sometimes|required|min:0',

            'language' => 'sometimes|required|between:1,4',
            'phone' => 'sometimes|required|string|min:5|max:50',
            'address' => 'sometimes|required|string|min:5|max:50',
            'country' => 'sometimes|required|string|min:5|max:15',
        ]);

        //user allowed?
        $response = Gate::inspect('view', $user);
        if (!$response->allowed()) {
            //create error message
            return redirect(
                action(
                    'Web\UserController@show',
                    ['user' => $user->id]
                )
            )
                ->withErrors([$response->message()]);
        }

        $this->userService->update($request, $user);

        Session::flash('message', 'User Updated');
        return redirect()->action(
            [UserController::class, 'edit'],
            ['user' => $user->id]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return Response
     */
    public function destroy(User $user)
    {
        //user allowed?
        $response = Gate::inspect('forceDelete', $user);
        if (!$response->allowed()) {
            //create error message
            return redirect(action('Web\UserController@index'))
                ->withErrors([$response->message()]);
        }

        $this->userService->delete($user);
        return redirect()->action(
            [UserController::class, 'index']
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @param AlertAddress $alertAddress
     *
     * @return Response
     */
    public function destroyAlertAddress(User $user, AlertAddress $alertAddress)
    {
        $response = Gate::inspect('updateMeta', $user);
        if (!$response->allowed()) {
            //create error message
            return redirect(
                action(
                    'Web\UserController@edit',
                    ['user' => $user->id]
                )
            )
                ->withErrors([$response->message()]);
        }

        $alertAddress->delete();

        Session::flash('message', 'AlertAddress deleted');
        return redirect()->action(
            [UserController::class, 'edit'],
            ['user' => $user->id]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param AlertAddress $alertAddress
     * @param User $user
     * @return Response
     */
    public function addAlertAddress(Request $request, User $user)
    {
        $response = Gate::inspect('updateMeta', $user);
        if (!$response->allowed()) {
            //create error message
            return redirect()->action(
                [UserController::class, 'show'],
                ['user' => $user->id]
            )
                ->withErrors([$response->message()]);
        }

        //Validation
        $request->validate([
            'email' => ['required', 'email',
                Rule::unique('alert_addresses')->where(function ($query) use ($user) {
                    return $query->where('user_id', $user->id);
                })
            ]
        ]);

        $alertAddress = $user->alertAddresses()->create([
            'email' =>  $request->email,
        ]);

        Session::flash('message', 'AlertAddress added');
        return redirect()->action(
            [UserController::class, 'show'],
            ['user' => $user->id]
        );
    }

}
