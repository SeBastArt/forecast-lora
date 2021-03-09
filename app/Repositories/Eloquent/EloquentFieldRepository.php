<?php

namespace App\Repositories\Eloquent;

use App\Models\Field;
use App\Repositories\Contracts\FieldRepository;

class EloquentFieldRepository extends AbstractEloquentRepository implements FieldRepository
{
    /**
     * Create new eloquent Field repository instance.
     *
     * @param \App\Field $Field
     */
    public function __construct(Field $Field)
    {
        $this->model = $Field;
    }
}