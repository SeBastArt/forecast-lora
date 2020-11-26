<?php

namespace App\Services;

use App\Facility;
use App\Node;
use App\Repositories\Contracts\FacilityRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FacilityService
{
    private $facilityRepository;

    /**
     * Create a new service instance.
     * @param  \App\Repositories\Contracts\FacilityRepository $repository
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function __construct(FacilityRepository $facilityRepository)
    {
        $this->facilityRepository = $facilityRepository;
    }

    public function createFacility(int $companyId, Collection $facilityData)
    {
        $facilityData->put('company_id', $companyId);
        return $this->facilityRepository->create($facilityData->toArray());
    }

    public function getDistinctResults(Collection $facilities, Collection $valueArray)
    {
        $resultCollection = collect();
        foreach ($valueArray as $key => $value) {
            $resultCollection->put($value, $facilities->pluck(Str::lower($value))->unique());
        }
        return $resultCollection;
    }

    public function Update(Request $request, Facility $facility)
    {
        $facility->name = $request->name;
        $facility->location = $request->location;
        $this->facilityRepository->update( $facility->id, $facility->toArray());
    }


    public function Delete(Facility $facility){
        $this->facilityRepository->delete($facility->id);  
    }
}