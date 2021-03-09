<?php

namespace App\Policies;

use App\Helpers\RoleChecker;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class FacilityPolicy
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
        return (RoleChecker::check($user, 'ROLE_SUPPORT'))
            ? Response::allow()
            : Response::deny('You are not allowed to view facilities.');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Facility  $facility
     * @return mixed
     */
    public function view(User $user, Facility $facility)
    {
        return (
            (RoleChecker::check($user, 'ROLE_SUPPORT')
                && $facility->company->users()->get()->pluck('id')->contains($user->id))
            || RoleChecker::check($user, 'ROLE_MANAGEMENT'))
            ? Response::allow()
            : Response::deny('You are not allowed to edit facilities.');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return (RoleChecker::check($user, 'ROLE_ACCOUNT_MANAGER'))
            ? Response::allow()
            : Response::deny('You are not allowed to create facilities.');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Facility  $facility
     * @return mixed
     */
    public function update(User $user, Facility $facility)
    {
        return (
            (RoleChecker::check($user, 'ROLE_ACCOUNT_MANAGER')
                && $facility->company->users()->get()->pluck('id')->contains($user->id))
            || RoleChecker::check($user, 'ROLE_MANAGEMENT'))
            ? Response::allow()
            : Response::deny('You are not allowed to edit facilities.');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Facility  $facility
     * @return mixed
     */
    public function delete(User $user, Facility $facility)
    {
        return (
            (RoleChecker::check($user, 'ROLE_ACCOUNT_MANAGER')
                && $facility->company->users()->get()->pluck('id')->contains($user->id))
            || RoleChecker::check($user, 'ROLE_MANAGEMENT'))
            ? Response::allow()
            : Response::deny('You are not allowed to delete facilities.');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Facility  $facility
     * @return mixed
     */
    public function restore(User $user, Facility $facility)
    {
        return (
            (RoleChecker::check($user, 'ROLE_ACCOUNT_MANAGER')
                && $facility->company->users()->get()->pluck('id')->contains($user->id))
            || RoleChecker::check($user, 'ROLE_MANAGEMENT'))
            ? Response::allow()
            : Response::deny('You are not allowed to restore facilities.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Facility  $facility
     * @return mixed
     */
    public function forceDelete(User $user, Facility $facility)
    {
        return (
            (RoleChecker::check($user, 'ROLE_ACCOUNT_MANAGER')
                && $facility->company->users()->get()->pluck('id')->contains($user->id))
            || RoleChecker::check($user, 'ROLE_MANAGEMENT'))
            ? Response::allow()
            : Response::deny('You are not allowed to delete facilities.');
    }
}
