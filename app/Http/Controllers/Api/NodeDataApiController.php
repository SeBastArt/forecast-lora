<?php

namespace App\Http\Controllers\API;

use App\Forecast;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use App\Node;
use Carbon\Carbon;
use \Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NodeDataApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('api');
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
    * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function data(Request $request)
    {
        if (!$request->has('nodeId')) {
            return response()->json([
                'error' => 'nodeId required',
            ], 500, [], JSON_PRETTY_PRINT);
        }
        $node = Node::where('id', $request->query('nodeId'))->first();
        if (!isset($node)) {
            return response()->json([
                'error' => 'node not found',
            ], 404, [], JSON_PRETTY_PRINT);
        }
        if (Auth::user()->id !== $node->user_id) {
            return response()->json([
                'error' => 'not allowed',
            ], 405, [], JSON_PRETTY_PRINT);
        }

        $nodeData = collect();
        foreach ($node->fields->sortBy('position') as $field) {
            $dataCollection = collect();
            foreach ($field->data->where('created_at', '>', Carbon::now()->subMinutes(1440)) as $data) {
                $dataCollection->push(
                    collect([
                        'x' => $data->created_at->format('c'),
                        'y' => $data->value,
                    ])
                );
            };

            if (Str::contains($field->name, ['Temperatur', 'temperature'])) {
                $city = $node->city()->first();
                if (isset($city)) {
                    $forecast = Forecast::where('city_id', $city->id)->first();
                    if (isset($forecast)) {
                        foreach ($forecast->forecastItems->where('valid_from', '<', Carbon::now()->addMinutes(1440)) as $forecastItem) {
                            $dataCollection->push(
                                collect([
                                    'x' => (Carbon::createFromFormat('Y-m-d H:i:s', $forecastItem->valid_from))->format('c'),
                                    'y' => floatval(number_format((float)$forecastItem->temp, 1, '.', '')), //format to one digit
                                ])
                            );
                        };
                    }
                }
            }
            $nodeData->push($dataCollection);
        }
        return response()->json($nodeData, 200, [], JSON_PRETTY_PRINT);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Resources\Json\ResourceCollection
    */
    public function meta(Request $request){
        if (!$request->has('nodeId')) {
            return response()->json([
                'error' => 'nodeId required',
            ], 500, [], JSON_PRETTY_PRINT);
        }
        $node = Node::where('id', $request->query('nodeId'))->first();
        if (!isset($node)) {
            return response()->json([
                'error' => 'node not found',
            ], 404, [], JSON_PRETTY_PRINT);
        }
        if (Auth::user()->id !== $node->user_id) {
            return response()->json([
                'error' => 'not allowed',
            ], 405, [], JSON_PRETTY_PRINT);
        }

        $fieldsCollection = collect();
        foreach ($node->fields->sortBy('position') as $field) {
            $fieldItem = collect([
                'title' => $field->name,
                'unit' => $field->unit,
                'fill' => $field->isfilled,
                'primarycolor' => $field->primarycolor,
                'secondarycolor' => $field->secondarycolor,
                'min' => number_format($field->data->where('created_at', '>', Carbon::now()->subMinutes(1440))->min('value'), 1, '.', ''), //format to one digit,
                'max' => number_format($field->data->where('created_at', '>', Carbon::now()->subMinutes(1440))->max('value'), 1, '.', ''), //format to one digit,  
            ]);
            $fieldsCollection->push($fieldItem);
        }
        $nodeCollection = collect([
            'id' => $node->id,
            'name' => $node->name,
            'deveui' => $node->dev_eui,
        ]);

        $response = collect([
            'fields' => $fieldsCollection,
            'node' => $nodeCollection,
        ]);

        return response()->json($response, 200, [], JSON_PRETTY_PRINT);
    }

}
