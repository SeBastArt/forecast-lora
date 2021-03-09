<?php

namespace App\Repositories\Eloquent;

use App\Models\Company;
use App\Repositories\Contracts\CompanyRepository;

class EloquentCompanyRepository extends AbstractEloquentRepository implements CompanyRepository
{
    /**
     * Create new eloquent Company repository instance.
     *
     * @param \App\Company $Company
     */
    public function __construct(Company $Company)
    {
        $this->model = $Company;
    }
}