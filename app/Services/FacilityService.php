<?php

namespace App\Services;

use App\Helpers\Trace;
use App\Models\City;
use App\Models\Company;
use App\Models\Facility;
use App\Repositories\Contracts\FacilityRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class FacilityService
{
    private $facilityRepository;
    private $nodeService;
    private $forecastService;

    /**
     * Create a new service instance.
     * @param FacilityRepository $facilityRepository
     * @param NodeService $nodeService
     * @param ForecastService $forecastService
     */
    public function __construct(FacilityRepository $facilityRepository, NodeService $nodeService, ForecastService $forecastService)
    {
        $this->facilityRepository = $facilityRepository;
        $this->nodeService = $nodeService;
        $this->forecastService = $forecastService;
    }

    public function getDashboardData(Facility $facility)
    {
        Trace::StartSpan('app.facility-service.dashboardData');
        Trace::setTag('facility', $facility);

        $DataCollection = collect();
        foreach ($facility->nodes as $node) {
                $NodeData = collect([
                    'userNode' => $node,
                ]);

                if ($node->show_forecast == true) {
                    $city = City::where('name', $node->facility->company->city)->first();
                    if (isset($city)){

                        //Trace
                        $mainWeatherIcon = $this->forecastService->getMainWeatherIcon($city);
                        $cityForecastColl = $this->forecastService->getWeatherForecast($city);

                        if (isset($mainWeatherIcon)) {$NodeData->put('mainWeatherIcon', $mainWeatherIcon);}
                        if (isset($cityForecastColl)) {$NodeData->put('cityForecast', $cityForecastColl);}
                        $start = Carbon::now()->subHours(24);
                        $end = Carbon::now();
                        $allData = $this->nodeService->getRawData($node, $start, $end);

                        if ($allData->isEmpty())
                        {
                            $meta = collect([
                                'min' => '-',
                                'max' => '-',
                                'now' => '-',
                                'unit' => $node->fields->first()->unit,
                                'lastUpdate' => '-',
                            ]);
                        } else
                        {
                            $mainData = $allData[0];
                            $lastUpdate = $allData[count($allData)-1]->last();
                            $meta = collect([
                                'min' => $mainData->min(),
                                'max' => $mainData->max(),
                                'now' => $mainData->last(),
                                'unit' => $node->fields->first()->unit,
                                'lastUpdate' => $lastUpdate->format('H:i:s')
                            ]);
                        }
                        $NodeData->put('meta', $meta);
                    }
                }
                $DataCollection->push($NodeData);
        }
        Trace::EndSpan();
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
        foreach ($valueArray as $value) {
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
