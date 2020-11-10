<?php

namespace App\Http\Resources\Node;

use App\Http\Resources\Field\FieldResource;
use App\Http\Resources\Field\FieldsResource;
use App\Http\Resources\FieldCollection;
use App\Http\Resources\Node\NodeTypeResource;
use App\NodeType;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'fields' => FieldsResource::Collection($this->fields),
            'data_url' => url("api/data/nodes/{$this->id}"),
        ];
    }
}
