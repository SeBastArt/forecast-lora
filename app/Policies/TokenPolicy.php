<?php

namespace App\Policies;

use App\Helpers\RoleChecker;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TokenPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

     /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return (RoleChecker::check($user, 'ROLE_MANAGEMENT'))
            ? Response::allow()
            : Response::deny('You are not allowed to create tokens.');
    }

     /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function forceDelete(User $user)
    {
        return (RoleChecker::check($user, 'ROLE_MANAGEMENT'))
        ? Response::allow()
        : Response::deny('You are not allowed to delete tokens.');
    }
}
