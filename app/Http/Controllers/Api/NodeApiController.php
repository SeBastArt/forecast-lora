<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\NodeCollection;
use App\Http\Resources\NodeResource;
use \Illuminate\Support\Facades\Auth;
use App\Node;

class NodeApiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function index()
    {
        return new NodeCollection(Node::paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
   
        $node = Node::create([
            'title' => $request->title,
            'dev_eui' => '',
            'node_type_id' => 1,
            'user_id' => Auth::user()->id
        ]);
        $node->save();
        return $node;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function show(Node $node)
    {
        if (Auth::user()->id == $node->user_id){
            return new NodeResource($node);
        } else {
            abort(403, 'Unauthorized action.');
        }
        return null;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
