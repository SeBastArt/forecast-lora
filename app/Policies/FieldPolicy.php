<?php

namespace App\Policies;

use App\Models\Field;
use App\Helpers\RoleChecker;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class FieldPolicy
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
        return Response::deny('You are not allowed to view fields.');  
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Field  $field
     * @return mixed
     */
    public function view(User $user, Field $field)
    {
        return Response::deny('You are not allowed to view fields.');  
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
            : Response::deny('You are not allowed to create Fields.');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Field  $field
     * @return mixed
     */
    public function update(User $user, Field $field)
    {
        if($field->presets()->first() !== null){   
            if (RoleChecker::check($user, 'ROLE_MANAGEMENT'))
            {
                return Response::allow();
            }
            if (RoleChecker::check($user, 'ROLE_ACCOUNT_MANAGER') && $field->presets()->first()->user_id = $user->id)
            {
                return Response::allow();
            }
            return Response::deny('You are not allowed to update Fields.');
        }
        
        return (RoleChecker::check($user, 'ROLE_ACCOUNT_MANAGER') && $field->nodes()->first()->facility->company->user_id = $user->id)
            ? Response::allow()
            : Response::deny('You are not allowed to update Fields.');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Field  $field
     * @return \Illuminate\Auth\Access\Response
     */
    public function delete(User $user, Field $field)
    {
        if($field->presets()->first() !== null){   
            if (RoleChecker::check($user, 'ROLE_MANAGEMENT'))
            {
                return Response::allow();
            }
            if (RoleChecker::check($user, 'ROLE_ACCOUNT_MANAGER') && $field->presets()->first()->user_id = $user->id)
            {
                return Response::allow();
            }
            return Response::deny('You are not allowed to update Fields.');
        }
        
        return (RoleChecker::check($user, 'ROLE_ACCOUNT_MANAGER') && $field->nodes()->first()->facility->company->user_id = $user->id)
            ? Response::allow()
            : Response::deny('You are not allowed to update Fields.');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Field  $field
     * @return mixed
     */
    public function restore(User $user, Field $field)
    {
        if($field->presets()->first() !== null){   
            if (RoleChecker::check($user, 'ROLE_MANAGEMENT'))
            {
                return Response::allow();
            }
            if (RoleChecker::check($user, 'ROLE_ACCOUNT_MANAGER') && $field->presets()->first()->user_id = $user->id)
            {
                return Response::allow();
            }
            return Response::deny('You are not allowed to update Fields.');
        }
        
        return (RoleChecker::check($user, 'ROLE_ACCOUNT_MANAGER') && $field->nodes()->first()->facility->company->user_id = $user->id)
            ? Response::allow()
            : Response::deny('You are not allowed to update Fields.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Field  $field
     * @return mixed
     */
    public function forceDelete(User $user, Field $field)
    {
        if($field->presets()->first() !== null){   
            if (RoleChecker::check($user, 'ROLE_MANAGEMENT'))
            {
                return Response::allow();
            }
            if (RoleChecker::check($user, 'ROLE_ACCOUNT_MANAGER') && $field->presets()->first()->user_id = $user->id)
            {
                return Response::allow();
            }
            return Response::deny('You are not allowed to update Fields.');
        }
        
        return (RoleChecker::check($user, 'ROLE_ACCOUNT_MANAGER') && $field->nodes()->first()->facility->company->user_id = $user->id)
            ? Response::allow()
            : Response::deny('You are not allowed to update Fields.');
    }
}
