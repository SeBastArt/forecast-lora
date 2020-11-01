<?php

namespace App\Policies;

use App\Node;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class NodePolicy
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
        return $user->hasRole('ROLE_SUPPORT');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Node  $node
     * @return mixed
     */
    public function view(User $user, Node $node)
    {
        return $user->hasRole('ROLE_SUPPORT') && $node->user_id = $user->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return (bool) $user->hasRole('ROLE_ADMIN')
        ? Response::allow()
        : Response::deny('You do not own this node.');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Node  $node
     * @return mixed
     */
    public function update(User $user, Node $node)
    {
        return $user->hasRole('ROLE_SUPPORT') && $node->user_id = $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Node  $node
     * @return mixed
     */
    public function delete(User $user, Node $node)
    {
        return $user->hasRole('ROLE_SUPPORT') && $node->user_id = $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\Node  $node
     * @return mixed
     */
    public function restore(User $user, Node $node)
    {
        return $user->hasRole('ROLE_SUPPORT') && $node->user_id = $user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Node  $node
     * @return mixed
     */
    public function forceDelete(User $user, Node $node)
    {
        return $user->hasRole('ROLE_ADMIN');
    }
}
