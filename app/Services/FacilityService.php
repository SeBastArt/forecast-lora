<?php

namespace App\Services;

use App\Models\City;
use App\Models\Company;
use App\Models\Facility;
use App\Models\Node;
use App\Repositories\Contracts\FacilityRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FacilityService
{
    private $facilityRepository;
    private $nodeService;
    private $forecastService;

    /**
     * Create a new service instance.
     * @param  \App\Repositories\Contracts\FacilityRepository $repository
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function __construct(FacilityRepository $facilityRepository, NodeService $nodeService, ForecastService $forecastService)
    {
        $this->facilityRepository = $facilityRepository;
        $this->nodeService = $nodeService;
        $this->forecastService = $forecastService;
    }

    public function getDashboardData(Facility $facility)
    {
        $DataCollection = collect();
        foreach ($facility->nodes as $key => $node) {
                $NodeData = collect([
                    'userNode' => $node,
                ]);

                if ($node->show_forecast == true) {
                    $city = City::where('name', $node->facility->company->city)->first();
                    if (isset($city)){
                        $mainWeatherIcon = $this->forecastService->getMainWeatherIcon($city);
                        $cityForecastColl = $this->forecastService->getWeatherForecast($city);
                        if (isset($mainWeatherIcon)) {$NodeData->put('mainWeatherIcon', $mainWeatherIcon);}
                        if (isset($cityForecastColl)) {$NodeData->put('cityForecast', $cityForecastColl);}
                    }
                }
                $DataCollection->push($NodeData);
        }
        return $DataCollection;
    }

    public function createFacility(Company $company, Collection $facilityData)
    {
        $facilityData->put('company_id', $company->id);
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
        $this->facilityRepository->update($facility->id, $facility->toArray());
    }


    public function Delete(Facility $facility)
    {
        $this->facilityRepository->delete($facility->id);
    }
}
