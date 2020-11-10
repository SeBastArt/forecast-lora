<?php

namespace App\Services;

use App\Field;
use App\Node;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Services\FieldService;
use Illuminate\Support\Str;

class NodeService
{
    const TIMEFORMAT = 'H:i:s';
 
    private $fieldService;
    /**
     * Create a new service instance.
     * @param  \App\Repositories\Contracts\FieldRepository $repository
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function __construct(FieldService $fieldService)
    {
        $this->fieldService = $fieldService;
    }
 
 
    /**
     * Create a json collection with primary field informations.
     *
     * @param  \App\Node  $node
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function getPrimFieldInfo(Node $node){
        If ($node->fields->count() == 0) {
            return null;
        }
   
        $primField = $node->fields->sortBy('position')->first();
        if (!isset($primField)) { 
            return null; 
        }
        return $this->getCollection($primField);
    }


    /**
     * Create a json collection with primary field informations.
     *
     * @param  \App\Node  $node
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function getSecFieldInfo(Node $node){

        If ($node->fields->count() <= 1) {
            return null;
        }

        $secField = $node->fields->sortBy('position')->skip(1)->first();
        if (!isset($primaryField)) { 
            return null;     
        }
        return $this->getCollection($secField);
    }

    public function getData(Node $node){

        $nodeDataColl = collect([
            'id' => $node->id,
            'name' => $node->name,
            'fields' => collect()
        ]);
        foreach ($node->fields->sortBy('position') as $field) {
            $fieldData = $this->fieldService->getData($field);
            $fieldDataColl = collect([
                'id' => $field->id,
                'title' => $field->title,
                'data' => $fieldData
            ]);
            $nodeDataColl['fields']->push($fieldDataColl);
        }
        return $nodeDataColl;
    }

    public function getMeta(Node $node){
        $fieldsColl = collect();
        foreach ($node->fields->sortBy('position') as $field) {
            $fieldMeta = $this->fieldService->getMeta($field);
            $fieldsColl->push($fieldMeta);
        }
        $nodeColl = collect([
            'id' => $node->id,
            'name' => $node->name,
            'deveui' => $node->dev_eui,
            'user_id' => Auth::user()->id,
            'fields' => $fieldsColl,
        ]);

        return $nodeColl;
    }

    private function getCollection(Field $field){
        
        if ($field->data->count() == 0) {
            return null;
        }

        $result = collect([
            'unit' => $field->unit,
            'min' => number_format($field->data->where('created_at', '>', Carbon::now()->subMinutes(1440))->min('value'), 1, '.', ''), //format to one digit,
            'max' => number_format($field->data->where('created_at', '>', Carbon::now()->subMinutes(1440))->max('value'), 1, '.', ''), //format to one digit,
            'last'  =>  collect([
                'value' => $field->data->last()->value,
                'timestamp' => $field->data->last()->created_at->format(self::TIMEFORMAT)
            ])
        ]);
        return $result;
    }
}