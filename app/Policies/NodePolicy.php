<?php

namespace App\Policies;

use App\Helpers\RoleChecker;
use App\Models\Node;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class NodePolicy
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
            : Response::deny('You are not allowed to view nodes.');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Node  $node
     * @return mixed
     */
    public function view(User $user, Node $node)
    {
        return (
            (RoleChecker::check($user, 'ROLE_SUPPORT') && $node->facility->company->users->pluck('id')->contains($user->id))
            || RoleChecker::check($user, 'ROLE_MANAGEMENT'))
            ? Response::allow()
            : Response::deny('You are not allowed to view nodes.');
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
            : Response::deny('You are not allowed to create nodes.');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Node  $node
     * @return mixed
     */
    public function update(User $user, Node $node)
    {
        return (
            (RoleChecker::check($user, 'ROLE_ACCOUNT_MANAGER') && $node->facility->company->users->pluck('id')->contains($user->id))
            || RoleChecker::check($user, 'ROLE_MANAGEMENT'))
            ? Response::allow()
            : Response::deny('You are not allowed to edit nodes.');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Node  $node
     * @return \Illuminate\Auth\Access\Response
     */
    public function delete(User $user, Node $node)
    {
        return (
            (RoleChecker::check($user, 'ROLE_ACCOUNT_MANAGER') && $node->facility->company->users->pluck('id')->contains($user->id))
            || RoleChecker::check($user, 'ROLE_MANAGEMENT'))
            ? Response::allow()
            : Response::deny('You are not allowed to delete nodes.');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Node  $node
     * @return mixed
     */
    public function restore(User $user, Node $node)
    {
        return (
            (RoleChecker::check($user, 'ROLE_ACCOUNT_MANAGER') && $node->facility->company->users->pluck('id')->contains($user->id))
            || RoleChecker::check($user, 'ROLE_MANAGEMENT'))
            ? Response::allow()
            : Response::deny('You are not allowed to restore nodes.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Node  $node
     * @return mixed
     */
    public function forceDelete(User $user, Node $node)
    {
        return (
            (RoleChecker::check($user, 'ROLE_ACCOUNT_MANAGER') && $node->facility->company->users->pluck('id')->contains($user->id))
            || RoleChecker::check($user, 'ROLE_MANAGEMENT'))
            ? Response::allow()
            : Response::deny('You are not allowed to delete nodes.');
    }
}
