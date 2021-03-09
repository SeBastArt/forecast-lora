<?php

namespace App\Services;

use App\Helpers\DecodeHelper;
use App\Models\Field;
use App\Models\Node;
use App\Models\Preset;
use App\Repositories\Contracts\FieldRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

CONST INIT_NAME = 'Dummy';
CONST INIT_UNIT = 'K';
CONST INIT_VISIBLE = '1';
CONST INIT_COLOR_PRIME = '#333';
CONST INIT_COLOR_SECOND = '#aaa';
CONST INIT_IS_DASHED = '0';
CONST INIT_IS_FILLED = '1';

class FieldService
{
    const TIMEFORMAT = 'H:i:s';
    private $fieldRepository;

    /**
     * Create a new service instance.
     * @param  \App\Repositories\Contracts\FieldRepository $fieldRepository
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function __construct(FieldRepository $fieldRepository)
    {
        $this->fieldRepository = $fieldRepository;
    }

    public function getMeta(Field $field){
        $fieldMeta = collect([
            'id' => $field->id,
            'title' => $field->name,
            'visible' => $field->visible,
            'node_id' => $field->nodes()->first()->id,
            'meta' => collect([
                'unit' => $field->unit,
                'fill' => $field->is_filled,
                'dash' => $field->is_dashed,
                'primary_color' => $field->primary_color,
                'secondary_color' => $field->secondary_color,
            ])
        ]);

        return $fieldMeta;
    }

    //crete field with init values if not set
    public function create(Request $request)
    {
        $fieldArray = $this->fillArray($request);
        return $this->fieldRepository->create($fieldArray);
    }

    //update given field with request params if set
    public function update(Field $field, Request $request)
    {
        if (isset($request->name)){ $field->name = $request->name; }
        if (isset($request->unit)){ $field->unit = $request->unit; }
        if (isset($request->primary_color)){ $field->primary_color = $request->primary_color; }
        if (isset($request->secondary_color)){ $field->secondary_color = $request->secondary_color; }
  
        //lower_limit
        $field->check_lower_limit = Arr::exists($request, 'check_lower_limit') ? '1' : '0';
        if (isset($request->lower_limit)){ $field->lower_limit = $request->lower_limit; }
        
        //upper_limit
        $field->check_upper_limit = Arr::exists($request, 'check_upper_limit') ? '1' : '0';
        if (isset($request->upper_limit)){ $field->upper_limit = $request->upper_limit; }

        $field->is_dashed = Arr::exists($request, 'dashed') ? '1' : '0';
        $field->is_filled = Arr::exists($request, 'filled') ? '1' : '0';
        $field->visible = Arr::exists($request, 'visible') ? '1' : '0';

        $this->fieldRepository->update($field->id, $field->toArray());
    }

    private function fillArray(Request $request){
        $fieldColl = collect();

        //name
        $var = (!isset($request->name)) ? INIT_NAME : $request->name;
        $fieldColl->put('name', $var);

        //unit
        $var = (!isset($request->unit)) ? INIT_UNIT : $request->unit;
        $fieldColl->put('unit', $var);

        //visible
        $var = Arr::exists($request, 'visible') ? '1' : INIT_VISIBLE;
        $fieldColl->put('visible', $var);

        //primary_color
        $var = (!isset($request->primary_color)) ? INIT_COLOR_PRIME : $request->primary_color;
        $fieldColl->put('primary_color', $var);

        //secondary_color
        $var = (!isset($request->secondary_color)) ? INIT_COLOR_SECOND : $request->secondary_color;
        $fieldColl->put('secondary_color', $var);

        //is_dashed
        $var = Arr::exists($request, 'dashed') ? '1' : INIT_IS_DASHED;
        $fieldColl->put('is_dashed', $var);

        //is_filled
        $var = Arr::exists($request, 'filled') ? '1' : INIT_IS_FILLED;
        $fieldColl->put('is_filled', $var);

        //exceeded
        $var = Arr::exists($request, 'filled') ? '1' : INIT_IS_FILLED;
        $fieldColl->put('is_filled', $var);

        //exceeded
        // $var = (!isset($request->error_level)) ? 0 : $request->error_level;
        // $fieldColl->put('error_level', $var);

        //node_id
        //$fieldColl->put('node_id', $node->id);

        return  $fieldColl->toArray();
    }

    public function Delete(Field $field){
        $this->fieldRepository->delete($field->id);
    }

}
