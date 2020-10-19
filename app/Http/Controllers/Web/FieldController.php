<?php

namespace App\Http\Controllers\Web;

use App\Field;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Node;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

class FieldController extends Controller
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
     * @param  \App\Node  $node
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Node $node)
    {
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"],  
            ['link' => action('Web\NodeController@index'), 'name' => "Nodes"], 
            ['link' => action('Web\NodeController@show', ['node' => $node->id]), 'name' => $node->name." Node"],  
            ['link' => action('Web\FieldController@index', ['node' => $node->id]), 'name' => "Fields"]
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'isFabButton' => true];
        return view('pages.fields.index', ['pageConfigs' => $pageConfigs, 'Node' => $node, 'Fields' => $node->fields()->get()], ['breadcrumbs' => $breadcrumbs]);
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
     * @param  \App\Node  $node
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Node $node, Request $request)
    {
        $request->validate([
            'name' => 'required|min:3|max:255',
            'unit' => 'required',
        ]);
       
        $field = Field::create([
            'name' => $request->name,
            'unit' => $request->unit,
            'position' => $node->fields->count() + 1, //position starts at 1
            'visible' => true,
            'primarycolor' => '#000',
            'secondarycolor' => '#ccc',
            'isdashed' => false,
            'isfilled' => false,
            'node_id' => $node->id
        ]);
        $field->save();
        return back()->with('status', 'Field Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Node  $node
     * @param  \App\Field  $field
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show(Node $node, Field $field)
    {
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"], 
            ['link' => action('Web\NodeController@index'), 'name' => "Nodes"], 
            ['link' => action('Web\NodeController@show', ['node' => $node->id]), 'name' => $node->name." Node"],
            ['link' => action('Web\FieldController@index', ['node' => $node->id]), 'name' => "Fields"],
            ['link' => action('Web\FieldController@show', ['node' => $node->id, 'field' => $field->id]), 'name' => $field->name." Field"],
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'isFabButton' => true];
        return view('pages.fields.show', ['pageConfigs' => $pageConfigs, 'Node'=> $node, 'Field'=> $field], ['breadcrumbs' => $breadcrumbs]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Field  $field
     * @return \Illuminate\Http\Response
     */
    public function edit(Field $field)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Node  $node
     * @param  \App\Field  $field
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Node $node, Field $field)
    {

        $request->validate([
            'name' => 'required|min:3|max:255',
            'unit' => 'required',
            'primarycolor' => 'required',
            'secondarycolor' => 'required',
        ]);

        $field->name = $request->name;
        $field->unit = $request->unit;
        $field->primarycolor = $request->primarycolor;
        $field->secondarycolor = $request->secondarycolor;
        $field->isdashed = Arr::exists($request, 'dashed') ? '1' : '0';
        $field->isfilled = Arr::exists($request, 'filled') ? '1' : '0';
        $field->visible = Arr::exists($request, 'visible') ? '1' : '0';
        $field->save();
        return back()->with('status', 'Field '.$field->name.' Updated');
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param  \App\Node  $node
     * @param  \App\Field  $field
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Node $node, Field $field)
    {
        $field->delete();
        return back();
    }
}
