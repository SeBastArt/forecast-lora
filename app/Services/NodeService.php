<?php

namespace App\Services;

use App\Helpers\DecodeHelper;
use App\Models\Facility;
use App\Models\Field;
use App\Models\Node;
use App\Models\Preset;
use App\Repositories\Contracts\NodeRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Services\FieldService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\VarDumper\Cloner\Data;

class NodeService
{
    const TIMEFORMAT = 'H:i:s';

    private $fieldService;
    private $nodeRepository;
    private $alertService;
    /**
     * Create a new service instance.
     * @param  \App\Repositories\Contracts\FieldRepository $repository
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function __construct(NodeRepository $nodeRepository, FieldService $fieldService, AlertService $alertService)
    {
        $this->fieldService = $fieldService;
        $this->nodeRepository = $nodeRepository;
        $this->alertService = $alertService;
    }

    public function getCSVColumnsName(Node $node){
        $columnsC = collect();
       
        foreach ($node->fields()->get() as $key => $field) {
            $columnsC->push($field->name);
        }
        $columnsC->push('Timestamp');
        $columns =  $columnsC->toArray();

        return $columns;
    }

    public function getCSVArray(Node $node, $start, $end){
        $nodeData = $this->getRawData($node, $start, $end);
        $columns = $this->getCSVColumnsName($node);
       
        $csvEcport = collect();
 
        for ($j=0; $j < $nodeData[$nodeData->count() - 1]->count(); $j++) { 
            $temp = collect();
            for ($i=0; $i < $nodeData->count(); $i++) { 
                $temp->put($columns[$i], $nodeData[$i][$j]);
               
                $csvEcport->push($temp);
            }
        }

        return $csvEcport;
    }

    public function getRawData(Node $node, $start, $end){
        $rawDataFields = collect();
        $dataFetch = $node->nodeData()->whereBetween('created_at', [$start, $end])->get();

        if ($dataFetch->count() == 0) {
            return $dataFetch;
        }
        
        //all fields of payload
        $rawDataCount = min($node->fields->count(), count(DecodeHelper::convertPayloadToArray($node->type->id, $dataFetch->first()['payload'])));
        for ($i = 0; $i < $rawDataCount; $i++) {
            $rawDataFields->push(collect());
        }
        //additional timestamp row
        $rawDataFields->push(collect());

        foreach ($dataFetch as $key => $nodeData) {
            $dataArray = DecodeHelper::convertPayloadToArray($node->type->id, $nodeData['payload']);
            for ($i = 0; $i < $rawDataCount; $i++) {
                $rawDataFields[$i]->push($dataArray[$i]);
            }
            $rawDataFields[$i]->push($nodeData->created_at);
        }
        return $rawDataFields;
    }

    public function getData(Node $node, $start, $end)
    {
        $resultData = collect([
            'id' => $node->id,
            'name' => $node->name,
            'fields' => collect()
        ]);
        foreach ($node->fields as $key => $field) {
            if($field->visible){
                $resultData['fields']->push(collect([
                    'id' => $field->id,
                    'title' => $field->name,
                    'data' => collect(),
                ]));
            }
        }

        $rawDataFields = $this->getRawData($node, $start, $end);
        if ($rawDataFields->count() == 0) {
            return $resultData;
        }
     
        /* 
        $rawDataFields: 
            0 = data
            1 = data
            ...
            last = timestamp
 */
        //fill data
        //speedup for count estimation
        $rawDataCount = $rawDataFields->count() - 1;
        $arrayCounter = 0;
        foreach ($node->fields as $fieldKey => $field) {
            if ($fieldKey < $rawDataCount && $field->visible) { //prevent overshoot if more fields are given
                foreach ($rawDataFields[$fieldKey] as $rawKey => $rawData) {
                    $resultData['fields'][$arrayCounter]['data']->push(collect([
                        'x' => $rawDataFields->last()[$rawKey]->format('c'), //timestamp
                        'y' => number_format($rawData, 1, '.', ''), //value
                    ]));
                }
                $arrayCounter++;
            }
        }

        //fill meta
        $arrayCounter = 0;
        foreach ($node->fields as $fieldKey => $field) {
            if ($fieldKey < $rawDataCount  && $field->visible) { //prevent overshoot if more fields are given
                if ($rawDataFields[$fieldKey]->count() == 0) {
                    $resultData['fields'][$arrayCounter]->put('meta', collect([
                        'unit' => $field->unit,
                        'min' => 0, //format to one digit,
                        'max' => 0, //format to one digit,
                        'last'  =>  collect([
                            'value' => 0,
                            'timestamp' => 0
                        ])
                    ]));
                } else {
                    $resultData['fields'][$arrayCounter]->put('meta', collect([
                        'unit' => $field->unit,
                        'min' => number_format($rawDataFields[$fieldKey]->min(), 1, '.', ''), //format to one digit,
                        'max' => number_format($rawDataFields[$fieldKey]->max(), 1, '.', ''), //format to one digit,
                        'last'  =>  collect([
                            'value' => number_format($rawDataFields[$fieldKey]->last(), 1, '.', ''), //format to one digit,,
                            'timestamp' => $rawDataFields[$rawDataCount]->last()->format(self::TIMEFORMAT)
                        ])
                    ]));
                }
                $arrayCounter++;
            }
        }

        return $resultData;
    }


    public function getMeta(Node $node)
    {
        $fieldsColl = collect();
        foreach ($node->fields->sortBy('created_at') as $field) {
            if($field->visible){
                $fieldMeta = $this->fieldService->getMeta($field);
                $fieldsColl->push($fieldMeta);
            }
        }

        $nodeColl = collect([
            'id' => $node->id,
            'name' => $node->name,
            'deveui' => $node->dev_eui,
            'user_id' => 1, //Auth::user()->id,
            'fields' => $fieldsColl,
        ]);

        return $nodeColl;
    }

    public function createNode(Facility $facility, Collection $requestData)
    {

        //Add some fields for repository
        $requestData->put('facility_id', $facility->id);
        //only lowercase for devEUI
        $requestData['dev_eui'] = strtoupper($requestData['dev_eui']);
        $node = $this->nodeRepository->create($requestData->toArray());
        if (isset($requestData['preset_id'])) {
            $requestData['preset_id'] = $requestData['preset_id'] < 1 ? null : $requestData['preset_id'];
        }

        if (isset($requestData['preset_id'])) {
            $preset = Preset::findorfail($requestData['preset_id']);
            foreach ($preset->fields()->get() as $key => $field) {
                $newField = $field->replicate();
                //save for new Id
                $newField->save();
                $node->fields()->attach($newField);
            }
            $node->preset_id = $requestData['preset_id'];
        }

        $recentNode = Node::where('dev_eui', $requestData['dev_eui'])->orderBy('created_at', 'asc')->first();
        if (isset($recentNode)) {
            $allRecentNodeData = $recentNode->nodeData()->get();
            foreach ($allRecentNodeData as $nodeDataKey => $recentNodeData) {
                $newNodeData = $recentNodeData->replicate();
                //$newNodeData->timestamp = false;
                $newNodeData->node_id = $node->id;
                $newNodeData->created_at = $recentNodeData->created_at;
                $newNodeData->updated_at = $recentNodeData->updated_at;
                $newNodeData->Save();
            }
        }
        return $node;
    }


    public function Update(Request $request, Node $node)
    {
        $node->name = $request->name;
        $node->dev_eui = strtoupper($request->dev_eui);
        $node->node_type_id = $request->nodetype;
        $node->show_forecast = Arr::exists($request, 'show_forecast') ? '1' : '0';

        //in general there is no presetID in Request
        if($request->preset_id != null &&  $request->preset_id > 1){
            $node->preset_id = $request->preset_id;
        }
        $this->nodeRepository->update($node->id, $node->toArray());
    }

    public function DeleteFields(Node $node)
    {
        foreach ($node->fields()->get() as $key => $field) {
            $this->fieldService->Delete($field);
        }
    }

    public function Delete(Node $node)
    {
        foreach ($node->fields()->get() as $key => $field) {
            $this->fieldService->Delete($field);
        }
        $this->nodeRepository->delete($node->id);
    }

    public function DeletePreset(Node $node)
    {
        $node->preset_id = null;
        $this->nodeRepository->update($node->id, $node->toArray());
    }

    public function ResetAlert(Node $node){
        foreach ($node->fields()->get() as $fieldKey => $field) {
            $alert = $field->alerts()->first();
            if ($alert != null){
                $this->alertService->Delete($alert);
                break;
            }
        }
    }
}
