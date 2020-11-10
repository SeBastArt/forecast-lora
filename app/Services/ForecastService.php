<?php

namespace App\Services;

use App\City;
use App\Forecast;
use App\Node;
use App\Repositories\Contracts\ForecastRepository;
use App\Repositories\Contracts\WeatherRepository;
use App\Weather;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

const WEEKMAP = [
    0 => 'So',
    1 => 'Mo',
    2 => 'Di',
    3 => 'Mi',
    4 => 'Do',
    5 => 'Fr',
    6 => 'Sa',
];

class ForecastService
{


    const TIMEFORMAT = 'H:i:s';
    private $forecastRepository;
    private $weatherRepository;

    /**
     * Create a new service instance.
     * @param  \App\Repositories\Contracts\ForecastRepository $repository
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function __construct(ForecastRepository $forecastRepository, WeatherRepository $weatherRepository)
    {
        $this->forecastRepository = $forecastRepository;
        $this->weatherRepository = $weatherRepository;
    }

    public function getTempForecast(City $city)
    {
        $dataColl = collect();
        if (isset($city)) {
            $forecast = $this->forecastRepository->findByCityId($city->id);
            if (isset($forecast)) {
                foreach ($forecast->forecastItems->where('valid_from', '<', Carbon::now()->addMinutes(1440)) as $forecastItem) {
                    $dataColl->push(
                        collect([
                            'x' => (Carbon::createFromFormat('Y-m-d H:i:s', $forecastItem->valid_from))->format('c'),
                            'y' => floatval(number_format((float)$forecastItem->temp, 1, '.', '')), //format to one digit
                        ])
                    );
                };
            }
        }
        return $dataColl;
    }

    public function getMainWeatherIcon(City $city){
        //if there is no icon let the sun shine
        $weatherIcon = $this->getIconClass(800);
        $forecast = $this->forecastRepository->findByCityId($city->id);
        if (isset($forecast)) {
            $forecastitem = $this->forecastRepository->getFirstForecastItem($forecast->id);
            $weatherItem = $this->weatherRepository->find($forecastitem->weather_id);
            $weatherIcon = $this->getIconClass($weatherItem->api_id);
        }
        return $weatherIcon;
    }

    public function getWeatherForecast(City $city)
    {
        $forecast = $this->forecastRepository->findByCityId($city->id);
        $forecastItems = $this->forecastRepository->getSortedForecastItems($forecast->id);        
        $colForecast = collect();
        $iconIdColl = collect();
        $tempArray = collect();
        $inDay = false;

        $actDay = WEEKMAP[Carbon::parse($forecast->forecastItems[0]->valid_from)->dayOfWeek];

        //aktueller Tag
        for ($i = 0; $i < count($forecastItems); $i++) {
            $weatherItem = $this->weatherRepository->find($forecastItems[$i]->weather_id);
            $time = Carbon::parse($forecastItems[$i]->valid_from);
            $iconIdColl->push($weatherItem->api_id);
            $tempArray->push(number_format($forecastItems[$i]->temp, 0));

            if ($time->hour == 0) {
                $icon = $this->GetBadestWeatherIcon($iconIdColl);
                $forecastIcon = collect([
                    'icon' => $icon,
                    'day' => $actDay,
                    'minTemp' => $tempArray->min(),
                    'maxTemp' => $tempArray->max(),
                ]);
                $colForecast->push($forecastIcon);
                break;
            }
        }

        //Wochenforecast
        for ($i = 0; $i < count($forecastItems); $i++) {
            $time = Carbon::parse($forecastItems[$i]->valid_from);
            if ($time->hour > 18 && $inDay == true) {
                $inDay = false;
                $weatherItem = $this->weatherRepository->find($forecastItems[$i]->weather_id);

                $iconIdColl->push($weatherItem->api_id);
                $tempArray->push(number_format($forecastItems[$i]->temp, 0));
                $icon = $this->GetBadestWeatherIcon($iconIdColl);

                $userForecast = collect([
                    'icon' => $icon,
                    'day' => WEEKMAP[$time->dayOfWeek],
                    'minTemp' => $tempArray->min(),
                    'maxTemp' => $tempArray->max(),
                ]);
                $colForecast->push($userForecast);
            }

            if ($time->hour > 5 && $inDay == true) {
                $iconIdColl->push(Weather::where('id', $forecastItems[$i]->weather_id)->first()->api_id);
                $tempArray->push(number_format($forecastItems[$i]->temp, 0));
            }

            if ($time->hour == 0 && $inDay == false) {
                $tempArray = collect();
                $iconIdColl = collect();
                $inDay = true;
            }
        }
        $forecastCity = collect([
            'id' => $city->id,
            'name' => $city->name,
            'country' => $city->country,
            'lat' => $city->lat,
            'lon' => $city->lon,
        ]) ;

        $forecastColl  = collect([
            'city' => $forecastCity,
            'forecast' => $colForecast
        ]);
        
        return $forecastColl;
    }

    private function GetBadestWeatherIcon(Collection $weatherArray){
        //if no forecastitem given, then let the sun shine
        if ($weatherArray->count() == 0) { 
            return $this->getIconClass(800); 
        }
        //get main Category 800 -> 8, 512 -> 5,....
        $divided = $weatherArray->map(function ($item, $key) {
            return $item / 100;
        });
        $mainCategory = round($divided->min()) * 100;

        //get max value in category
        $subCategories = collect();
        foreach ($weatherArray as $key => $value) {
            if($value >= $mainCategory && $value < $mainCategory + 100){
                $subCategories->push($value);
            }
        }
        return $this->getIconClass($subCategories->max());
    }

    /**
     * @param int $_iconNumber 
     * @return string 
     */
    private function getIconClass(int $icon): string
    {
        $resultIcon = 'mdi-weather-cloudy';

        if ($icon == 200) {
            return 'mdi-weather-lightning-rainy';
        }
        if ($icon == 201) {
            return 'mdi-weather-lightning-rainy';
        }
        if ($icon == 202) {
            return 'mdi-weather-lightning-rainy';
        }
        if ($icon == 210) {
            return 'mdi-weather-lightning-rainy';
        }
        if ($icon == 211) {
            return 'mdi-weather-lightning-rainy';
        }
        if ($icon == 212) {
            return 'mdi-weather-lightning-rainy';
        }
        if ($icon == 221) {
            return 'mdi-weather-lightning-rainy';
        }
        if ($icon == 230) {
            return 'mdi-weather-lightning-rainy';
        }
        if ($icon == 231) {
            return 'mdi-weather-lightning-rainy';
        }
        if ($icon == 232) {
            return 'mdi-weather-lightning-rainy';
        }

        if ($icon == 300) {
            return 'mdi-weather-pouring';
        }
        if ($icon == 301) {
            return 'mdi-weather-pouring';
        }
        if ($icon == 302) {
            return 'mdi-weather-pouring';
        }
        if ($icon == 310) {
            return 'mdi-weather-pouring';
        }
        if ($icon == 311) {
            return 'mdi-weather-pouring';
        }
        if ($icon == 312) {
            return 'mdi-weather-pouring';
        }
        if ($icon == 313) {
            return 'mdi-weather-pouring';
        }
        if ($icon == 314) {
            return 'mdi-weather-pouring';
        }
        if ($icon == 321) {
            return 'mdi-weather-pouring';
        }

        if ($icon == 500) {
            return 'mdi-weather-pouring';
        }
        if ($icon == 501) {
            return 'mdi-weather-rainy';
        }
        if ($icon == 502) {
            return 'mdi-weather-rainy';
        }
        if ($icon == 503) {
            return 'mdi-weather-rainy';
        }
        if ($icon == 504) {
            return 'mdi-weather-rainy';
        }
        if ($icon == 511) {
            return 'mdi-weather-snowy';
        }
        if ($icon == 520) {
            return 'mdi-weather-pouring';
        }
        if ($icon == 521) {
            return 'mdi-weather-pouring';
        }
        if ($icon == 522) {
            return 'mdi-weather-pouring';
        }
        if ($icon == 531) {
            return 'mdi-weather-pouring';
        }

        if ($icon == 600) {
            return 'mdi-weather-snowy';
        }
        if ($icon == 601) {
            return 'mdi-weather-snowy';
        }
        if ($icon == 602) {
            return 'mdi-weather-snowy';
        }
        if ($icon == 611) {
            return 'mdi-weather-snowy';
        }
        if ($icon == 612) {
            return 'mdi-weather-snowy';
        }
        if ($icon == 613) {
            return 'mdi-weather-snowy';
        }
        if ($icon == 615) {
            return 'mdi-weather-snowy';
        }
        if ($icon == 616) {
            return 'mdi-weather-snowy';
        }
        if ($icon == 620) {
            return 'mdi-weather-snowy';
        }
        if ($icon == 621) {
            return 'mdi-weather-snowy';
        }
        if ($icon == 622) {
            return 'mdi-weather-snowy';
        }

        if ($icon == 701) {
            return 'mdi-weather-fog';
        }
        if ($icon == 711) {
            return 'mdi-weather-fog';
        }
        if ($icon == 721) {
            return 'mdi-weather-fog';
        }
        if ($icon == 731) {
            return 'mdi-weather-fog';
        }
        if ($icon == 741) {
            return 'mdi-weather-fog';
        }
        if ($icon == 751) {
            return 'mdi-weather-fog';
        }
        if ($icon == 761) {
            return 'mdi-weather-fog';
        }
        if ($icon == 762) {
            return 'mdi-weather-fog';
        }
        if ($icon == 771) {
            return 'mdi-weather-fog';
        }
        if ($icon == 781) {
            return 'mdi-weather-fog';
        }

        if ($icon == 800) {
            return 'mdi-weather-sunny';
        }

        if ($icon == 801) {
            return 'mdi-weather-partlycloudy';
        }
        if ($icon == 802) {
            return 'mdi-weather-partlycloudy';
        }
        if ($icon == 803) {
            return 'mdi-weather-cloudy';
        }
        if ($icon == 804) {
            return 'mdi-weather-cloudy';
        }

        return 'mdi-weather-sunny';
    }
}
