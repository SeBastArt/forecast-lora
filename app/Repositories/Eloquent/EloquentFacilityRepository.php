<?php

namespace App\Repositories\Eloquent;

use App\Models\Facility;
use App\Repositories\Contracts\FacilityRepository;

class EloquentFacilityRepository extends AbstractEloquentRepository implements FacilityRepository
{
    /**
     * Create new eloquent Facility repository instance.
     *
     * @param \App\Facility $Facility
     */
    public function __construct(Facility $Facility)
    {
        $this->model = $Facility;
    }
}