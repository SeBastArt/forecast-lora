<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\NodeTypeResource;
use App\Http\Resources\FieldCollection;
use App\NodeType;

class NodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'guid' => $this->guid,
            'dev_eui' => $this->dev_eui,
            'user' => $this->user,
            'type' => new NodeTypeResource(NodeType::find(1)),
            'fields' => FieldCollection::Collection($this->fields),
            'data_url' => url("api/data/nodes/{$this->id}"),
          ];
    }
}
