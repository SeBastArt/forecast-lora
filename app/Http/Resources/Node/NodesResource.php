<?php

namespace App\Http\Resources\Node;

use Illuminate\Http\Resources\Json\JsonResource;

class NodesResource extends JsonResource
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
            'url' => url("api/nodes/{$this->id}")
          ];
    }
}
