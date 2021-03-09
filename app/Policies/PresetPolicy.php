<?php

namespace App\Policies;

use App\Helpers\RoleChecker;
use App\Models\Preset;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class PresetPolicy
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
        return (RoleChecker::check($user, 'ROLE_ACCOUNT_MANAGER'))
        ? Response::allow()
        : Response::deny('You are not allowed to view presets.');
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAll(User $user)
    {
        return (RoleChecker::check($user, 'ROLE_MANAGEMENT'))
        ? Response::allow()
        : Response::deny('You are not allowed to view presets.');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Preset  $preset
     * @return mixed
     */
    public function view(User $user, Preset $preset)
    {
        return ((RoleChecker::check($user, 'ROLE_ACCOUNT_MANAGER') && $preset->user->id == Auth::user()->id) || RoleChecker::check($user, 'ROLE_MANAGEMENT'))
        ? Response::allow()
        : Response::deny('You are not allowed to view this presets.');
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
        : Response::deny('You are not allowed to create presets.');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Preset  $preset
     * @return mixed
     */
    public function update(User $user, Preset $preset)
    {
        return ((RoleChecker::check($user, 'ROLE_ACCOUNT_MANAGER') && $preset->user->id == Auth::user()->id) || RoleChecker::check($user, 'ROLE_MANAGEMENT'))
        ? Response::allow()
        : Response::deny('You are not allowed to edit this presets.');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Preset  $preset
     * @return mixed
     */
    public function delete(User $user, Preset $preset)
    {
        return ((RoleChecker::check($user, 'ROLE_ACCOUNT_MANAGER') && $preset->user->id == Auth::user()->id) || RoleChecker::check($user, 'ROLE_MANAGEMENT'))
        ? Response::allow()
        : Response::deny('You are not allowed to delete this presets.');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Preset  $preset
     * @return mixed
     */
    public function restore(User $user, Preset $preset)
    {
        return ((RoleChecker::check($user, 'ROLE_ACCOUNT_MANAGER') && $preset->user->id == Auth::user()->id) || RoleChecker::check($user, 'ROLE_MANAGEMENT'))
        ? Response::allow()
        : Response::deny('You are not allowed to restore this presets.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Preset  $preset
     * @return mixed
     */
    public function forceDelete(User $user, Preset $preset)
    {
        return ((RoleChecker::check($user, 'ROLE_ACCOUNT_MANAGER') && $preset->user->id == Auth::user()->id) || RoleChecker::check($user, 'ROLE_MANAGEMENT'))
        ? Response::allow()
        : Response::deny('You are not allowed to delete this presets.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Preset  $preset
     * @return mixed
     */
    public function spread(User $user, Preset $preset)
    {
        return ((RoleChecker::check($user, 'ROLE_ACCOUNT_MANAGER') && $preset->user->id == Auth::user()->id) || RoleChecker::check($user, 'ROLE_MANAGEMENT'))
        ? Response::allow()
        : Response::deny('You are not allowed to spread this presets.');
    }
}
