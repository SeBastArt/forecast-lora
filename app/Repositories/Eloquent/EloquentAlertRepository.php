<?php

namespace App\Repositories\Eloquent;

use App\Models\Alert;
use App\Repositories\Contracts\AlertRepository;

class EloquentAlertRepository extends AbstractEloquentRepository implements AlertRepository
{
    /**
     * Create new eloquent Alert repository instance.
     *
     * @param \App\Alert $Alert
     */
    public function __construct(Alert $Alert)
    {
        $this->model = $Alert;
    }
}