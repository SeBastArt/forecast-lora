<?php

namespace App\Repositories\Eloquent;

use App\Node;
use App\Repositories\Contracts\NodeRepository;

class EloquentNodeRepository extends AbstractEloquentRepository implements NodeRepository
{
    /**
     * Create new eloquent node repository instance.
     *
     * @param \App\Node $node
     */
    public function __construct(Node $node)
    {
        $this->model = $node;
    }
}