<?php

namespace App\Repositories\Eloquent;

use App\Models\Preset;
use App\Repositories\Contracts\PresetRepository;

class EloquentPresetRepository extends AbstractEloquentRepository implements PresetRepository
{
    /**
     * Create new eloquent preset repository instance.
     *
     * @param \App\Preset $preset
     */
    public function __construct(Preset $preset)
    {
        $this->model = $preset;
    }
}