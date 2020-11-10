<?php

namespace App\Http\Controllers\Api;

use App\Forecast;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use App\Node;
use App\Services\ForecastService;
use App\Services\NodeService;
use Carbon\Carbon;
use \Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NodeDataApiController extends Controller
{
    const TIMEFORMAT = 'H:i:s';
    private $nodeService;
    private $forecastService;

    public function __construct(NodeService $nodeService, ForecastService $forecastService)
    {
        $this->nodeService = $nodeService;
        $this->forecastService = $forecastService;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\ResourceColl
     */
    public function data(Node $node, Request $request)
    {
        $nodeData = $this->nodeService->getData($node);
        return response()->json($nodeData, 200, [], JSON_PRETTY_PRINT);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Resources\Json\ResourceColl
    */
    public function meta(Node $node, Request $request){
        $nodeMeta = $this->nodeService->getMeta($node);
        return response()->json($nodeMeta, 200, [], JSON_PRETTY_PRINT);
    }
}
