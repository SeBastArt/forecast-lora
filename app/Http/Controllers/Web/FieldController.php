<?php

namespace App\Http\Controllers\Web;

use App\Field;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Node;
use App\Repositories\Contracts\FieldRepository;
use App\Services\FieldService;
use App\Services\NodeService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redirect;

class FieldController extends Controller
{
    private $fieldRepository;
    private $fieldService = null;
    private $nodeService = null;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(NodeService $nodeService, FieldRepository $fieldRepository, FieldService $fieldService)
    {
        $this->middleware('auth');
        $this->fieldService = $fieldService;
        $this->fieldRepository = $fieldRepository;
        $this->nodeService = $nodeService;
    }

    /**
     * Display a listing of the resource.
     * @param  \App\Node  $node
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Node $node)
    {
        $response = Gate::inspect('view', $node);
        if(!$response->allowed()){
            return Redirect::back()->withErrors([$response->message()]);
        }

        $primFieldInfo = $this->nodeService->getPrimFieldInfo($node);
        $secFieldInfo = $this->nodeService->getSecFieldInfo($node);
        $myFields = collect([
        ]);
        if (isset($primFieldInfo)) {$myFields->put('primField', $primFieldInfo);}
        if (isset($primFieldInfo)) {$myFields->put('secField', $secFieldInfo);}

        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'isFabButton' => true];

        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"],
            ['link' => action('Web\CompanyController@index'), 'name' => "Companies"],
            ['link' => action('Web\FacilityController@index', ['company' => $node->facility->company->id]), 'name' => $node->facility->company->name." Company"],
            ['link' => action('Web\NodeController@index', ['facility' => $node->facility->id]), 'name' => $node->facility->name." Facility"],
        ];

        return view(
            'pages.nodes.show',
            [
                'pageConfigs' => $pageConfigs,
                'Node' => $node,
                'Fields' => $myFields
            ],
            ['breadcrumbs' => $breadcrumbs]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Node  $node
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3|max:255',
            'unit' => 'required',
            'node_id' => 'required|gt:0'
        ]);
        
        $node = Node::find($request->node_id);
                
        $this->fieldService->create($node, $request);
        return back()->with('status', 'Field Created');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Field  $field
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Field $field)
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
     * @param  \App\Field  $field
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Field $field)
    {
        $this->repository->delete($field->id);
        return back();
    }
}
