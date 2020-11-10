<?php

namespace App\Http\Controllers\Web;

use App\Field;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Node;
use App\Repositories\Contracts\FieldRepository;
use App\Services\FieldService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

class FieldController extends Controller
{
    private $repository;
    private $fieldService = null;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(FieldRepository $repository, FieldService $fieldService)
    {
        $this->middleware('auth');
        $this->fieldService = $fieldService;
        $this->repository = $repository;
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
    
        $this->fieldService->create($node, $request);
        return back()->with('status', 'Field Created');
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

        $this->fieldService->update($field, $request);
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
        $this->repository->delete($field->id);
        return back();
    }
}
