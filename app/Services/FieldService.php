<?php

namespace App\Services;

use App\Field;
use App\Node;
use App\Repositories\Contracts\FieldRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

CONST INIT_NAME = 'Dummy';
CONST INIT_UNIT = 'K';
CONST INIT_VISIBLE = '1';
CONST INIT_COLOR_PRIME = '#333';
CONST INIT_COLOR_SECOND = '#aaa';
CONST INIT_ISDASHED = '0';
CONST INIT_ISFILLED = '1';

class FieldService
{
    const TIMEFORMAT = 'H:i:s';
    private $repository;
    private $forecastService;
    /**
     * Create a new service instance.
     * @param  \App\Repositories\Contracts\FieldRepository $repository
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function __construct(FieldRepository $repository, ForecastService $forecastService)
    {
        $this->repository = $repository;
        $this->forecastService = $forecastService;
    }

    public function getData(Field $field){
        $dataColl = collect();
        foreach ($field->data->where('created_at', '>', Carbon::now()->subMinutes(1440)) as $data) {
            $dataColl->push(
                collect([
                    'x' => $data->created_at->format('c'),
                    'y' => $data->value,
                ])
            );
        };

        if (Str::contains($field->name, ['Temperatur', 'temperature'])) {
            $city = $field->node->city()->first();
                if (isset($city)){
                $forecastColl = $this->forecastService->getTempForecast($city);
                $dataColl = $dataColl->concat($forecastColl);
            }
        }
        return $dataColl;
    }

    public function getMeta(Field $field){
        $fieldMeta = collect([
            'id' => $field->id,
            'title' => $field->name,
            'node_id' => $field->node->id,
            'meta' => collect([
                'unit' => $field->unit,
                'fill' => $field->isfilled,
                'dash' => $field->isdashed,
                'primarycolor' => $field->primarycolor,
                'secondarycolor' => $field->secondarycolor,
            ]),
            'value' => collect([
                'min' => number_format($field->data->where('created_at', '>', Carbon::now()->subMinutes(1440))->min('value'), 1, '.', ''), //format to one digit,
                'max' => number_format($field->data->where('created_at', '>', Carbon::now()->subMinutes(1440))->max('value'), 1, '.', ''), //format to one digit,  
                'last'  =>  collect([
                    'value' => $field->data->last()->value,
                    'timestamp' => $field->data->last()->created_at->format(self::TIMEFORMAT)
                ])
            ]),
        ]);
        return $fieldMeta;
    }

    //crete field with init values if not set
    public function create(Node $node, Request $request)
    {
        $fieldArray = $this->fillArray($node, $request);
        $this->repository->create($fieldArray);
    }

    //update given field with request params if set
    public function update(Field $field, Request $request)
    {
        if (isset($request->name)){ $field->name = $request->name; }
        if (isset($request->unit)){ $field->unit = $request->unit; }
        if (isset($request->primarycolor)){ $field->primarycolor = $request->primarycolor; }
        if (isset($request->secondarycolor)){ $field->secondarycolor = $request->secondarycolor; }
  
        $field->isdashed = Arr::exists($request, 'dashed') ? '1' : '0';
        $field->isfilled = Arr::exists($request, 'filled') ? '1' : '0';
        $field->visible = Arr::exists($request, 'visible') ? '1' : '0';

        $this->repository->update($field->id, $field->toArray());
    }

    private function fillArray(Node $node, Request $request){
        $fieldColl = collect();

        //name
        $var = (!isset($request->name)) ? INIT_NAME : $request->name;
        $fieldColl->put('name', $var);

        //unit
        $var = (!isset($request->unit)) ? INIT_UNIT : $request->unit;
        $fieldColl->put('unit', $var);

        //position
        $fieldColl->put('position', $node->fields->count() + 1);

        //visible
        $var = Arr::exists($request, 'visible') ? '1' : INIT_VISIBLE;
        $fieldColl->put('visible', $var);

        //primarycolor
        $var = (!isset($request->primarycolor)) ? INIT_COLOR_PRIME : $request->primarycolor;
        $fieldColl->put('primarycolor', $var);

        //secondarycolor
        $var = (!isset($request->secondarycolor)) ? INIT_COLOR_SECOND : $request->secondarycolor;
        $fieldColl->put('secondarycolor', $var);

        //isdashed
        $var = Arr::exists($request, 'dashed') ? '1' : INIT_ISDASHED;
        $fieldColl->put('isdashed', $var);

        //isfilled
        $var = Arr::exists($request, 'filled') ? '1' : INIT_ISFILLED;
        $fieldColl->put('isfilled', $var);

        //node_id
        $fieldColl->put('node_id', $node->id);
        
        return  $fieldColl->toArray();
    }
}
