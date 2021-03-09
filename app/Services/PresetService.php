<?php

namespace App\Services;

use App\Models\Preset;
use App\Models\Node;
use App\Repositories\Contracts\PresetRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PresetService
{
    private $presetRepository;
    private $nodeService;

    /**
     * Create a new service instance.
     * @param  \App\Repositories\Contracts\PresetRepository $repository
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function __construct(PresetRepository $presetRepository, NodeService $nodeService)
    {
        $this->presetRepository = $presetRepository;
        $this->nodeService = $nodeService;
    }
    
    public function createPreset(Collection $presetData)
    {
        $presetData['user_id'] = Auth::user()->id;
        return $this->presetRepository->create($presetData->toArray());
    }

    public function getDistinctResults(Collection $facilities, Collection $valueArray)
    {
        $resultCollection = collect();
        foreach ($valueArray as $key => $value) {
            $resultCollection->put($value, $facilities->pluck(Str::lower($value))->unique());
        }
        return $resultCollection;
    }

    public function Update(Request $request, Preset $preset)
    {
        $preset->name = $request->name;
        $preset->description = $request->description;
        $this->presetRepository->update( $preset->id, $preset->toArray());
    }


    public function Delete(Preset $preset){
        $this->presetRepository->delete($preset->id);  
    }

    public function Spread(Preset $preset){
        $nodes = Node::where('preset_id', $preset->id)->get();
        foreach ($nodes as $key => $node) {
            $this->nodeService->DeleteFields($node);  
        }
        foreach ($preset->fields()->get() as $key => $field) {
           
            foreach ($nodes as $key => $node) {
              
                $newField = $field->replicate();
                $newField->save();
                $node->fields()->attach($newField);
            }
        }
    }
}