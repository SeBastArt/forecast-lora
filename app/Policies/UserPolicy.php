<?php

namespace App\Policies;

use App\Helpers\RoleChecker;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return (RoleChecker::check($user, 'ROLE_MANAGEMENT'))
        ? Response::allow()
        : Response::deny('You are not allowed to view userlist.');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function view(User $user, User $model)
    {
        return ((RoleChecker::check($user, 'ROLE_SUPPORT') && $model->id == $user->id) || RoleChecker::check($user, 'ROLE_MANAGEMENT')  )
        ? Response::allow()
        : Response::deny('You are not allowed to view user.');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return (RoleChecker::check($user, 'ROLE_MANAGEMENT'))
        ? Response::allow()
        : Response::deny('You are not allowed to edit this.');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function update(User $user, User $model)
    {
        return (RoleChecker::check($user, 'ROLE_MANAGEMENT')  )
        ? Response::allow()
        : Response::deny('You are not allowed to update user.');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function delete(User $user, User $model)
    {
        return (RoleChecker::check($user, 'ROLE_MANAGEMENT'))
        ? Response::allow()
        : Response::deny('You are not allowed to edit this.');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function restore(User $user, User $model)
    {
        return (RoleChecker::check($user, 'ROLE_MANAGEMENT'))
        ? Response::allow()
        : Response::deny('You are not allowed to edit this.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function forceDelete(User $user, User $model)
    {
        return (RoleChecker::check($user, 'ROLE_MANAGEMENT'))
        ? Response::allow()
        : Response::deny('You are not allowed to edit this.');
    }

    /**
     * Determine whether the user can update metas like Token and Alertaddresses.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function updateMeta(User $user, User $model)
    {
        return ((RoleChecker::check($user, 'ROLE_ACCOUNT_MANAGER') && $model->id == $user->id) || RoleChecker::check($user, 'ROLE_MANAGEMENT')  )
        ? Response::allow()
        : Response::deny('You are not allowed to view user.');
    }
}
