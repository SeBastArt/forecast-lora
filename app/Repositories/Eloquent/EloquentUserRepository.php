<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepository;

class EloquentUserRepository extends AbstractEloquentRepository implements UserRepository
{
    /**
     * Create new eloquent User repository instance.
     *
     * @param \App\User $user
     */
    public function __construct(User $user)
    {
        $this->model = $user;
    }
}