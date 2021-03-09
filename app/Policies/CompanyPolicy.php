<?php

namespace App\Policies;

use App\Models\Company;
use App\Helpers\RoleChecker;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
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
            : Response::deny('You are not allowed to view companies.');
    }

    /**
     * Determine whether the user can view all companies.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAll(User $user)
    {
        return RoleChecker::check($user, 'ROLE_MANAGEMENT');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Company  $company
     * @return mixed
     */
    public function view(User $user, Company $company)
    {
        return (
            (RoleChecker::check($user, 'ROLE_SUPPORT')
                && $company->users()->get()->pluck('id')->contains($user->id))
            || RoleChecker::check($user, 'ROLE_MANAGEMENT'))
            ? Response::allow()
            : Response::deny('You are not allowed to see this companies.');
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
            : Response::deny('You are not allowed to create companies.');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Company  $company
     * @return mixed
     */
    public function update(User $user, Company $company)
    {
        return (
            (RoleChecker::check($user, 'ROLE_ACCOUNT_MANAGER')
                && $company->users()->get()->pluck('id')->contains($user->id))
            || RoleChecker::check($user, 'ROLE_MANAGEMENT'))
            ? Response::allow()
            : Response::deny('You are not allowed to edit companies.');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Company  $company
     * @return mixed
     */
    public function delete(User $user, Company $company)
    {
        return (
            (RoleChecker::check($user, 'ROLE_ACCOUNT_MANAGER')
                && $company->users()->get()->pluck('id')->contains($user->id))
            || RoleChecker::check($user, 'ROLE_MANAGEMENT'))
            ? Response::allow()
            : Response::deny('You are not allowed to delete companies.');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Company  $company
     * @return mixed
     */
    public function restore(User $user, Company $company)
    {
        return (
            (RoleChecker::check($user, 'ROLE_ACCOUNT_MANAGER')
                && $company->users()->get()->pluck('id')->contains($user->id))
            || RoleChecker::check($user, 'ROLE_MANAGEMENT'))
            ? Response::allow()
            : Response::deny('You are not allowed to restore companies.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Company  $company
     * @return mixed
     */
    public function forceDelete(User $user, Company $company)
    {
        return (
            (RoleChecker::check($user, 'ROLE_ACCOUNT_MANAGER')
                && $company->users()->get()->pluck('id')->contains($user->id))
            || RoleChecker::check($user, 'ROLE_MANAGEMENT'))
            ? Response::allow()
            : Response::deny('You are not allowed to delte companies.');
    }
}
