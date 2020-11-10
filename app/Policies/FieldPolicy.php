<?php

namespace App\Policies;

use App\Field;
use App\Helpers\RoleChecker;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class FieldPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return RoleChecker::check($user, 'ROLE_SUPPORT');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Field  $Field
     * @return mixed
     */
    public function view(User $user, Field $Field)
    {
        return RoleChecker::check($user, 'ROLE_SUPPORT') && $Field->user_id = $user->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return (RoleChecker::check($user, 'ROLE_ADMIN'))
        ? Response::allow()
        : Response::deny('You are not allowed to create Fields.');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Field  $Field
     * @return mixed
     */
    public function update(User $user, Field $Field)
    {
        return RoleChecker::check($user, 'ROLE_SUPPORT') && $Field->user_id = $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Field  $Field
     * @return \Illuminate\Auth\Access\Response
     */
    public function delete(User $user, Field $Field)
    {
        return (RoleChecker::check($user, 'ROLE_ADMIN') && $Field->user_id = $user->id)
            ? Response::allow()
            : Response::deny('You don\'t have permission for delte this Field.');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\Field  $Field
     * @return mixed
     */
    public function restore(User $user, Field $Field)
    {
        return RoleChecker::check($user, 'ROLE_SUPPORT') && $Field->user_id = $user->id ;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Field  $Field
     * @return mixed
     */
    public function forceDelete(User $user, Field $Field)
    {
        return $user->hasRole('ROLE_ADMIN');
    }
}
