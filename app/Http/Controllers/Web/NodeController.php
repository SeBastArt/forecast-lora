<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Node;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Repositories\Contracts\NodeRepository;
use App\Services\NodeService;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class NodeController extends Controller
{
    /**
     * The node repository instance.
     *
     * @var \App\Repositories\Contracts\NodeRepository
     */
    private $repository;
    private $nodeService = null;

    /**
     * NodeRepository constructor.
     *
     * @param $repository
     */
    public function __construct(NodeRepository $repository, NodeService $nodeService)
    {
        $this->middleware('auth');
        $this->repository = $repository;
        $this->nodeService = $nodeService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (Auth::user()->cannot('viewAny', Node::class)) { return redirect('/'); }
        $userNodes = collect(Auth::user()->nodes);
        $MyNodes = collect();

        foreach ($userNodes as $userNode) {
            $primFieldInfo = $this->nodeService->getPrimFieldInfo($userNode);
            $secFieldInfo = $this->nodeService->getSecFieldInfo($userNode);
            $myNode = collect([
                'Node' => $userNode,
            ]);
            if (isset($primFieldInfo)) {$myNode->put('primField', $primFieldInfo);}
            if (isset($primFieldInfo)) {$myNode->put('secField', $secFieldInfo);}

            $MyNodes->push($myNode);
        }

        $breadcrumbs = [
            ['link' => action('HomeController@index'), 'name' => "Home"],
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'bodyCustomClass' => 'menu-collapse', 'isFabButton' => true];

        return view('pages.nodes.index', ['pageConfigs' => $pageConfigs, 'myNodes' => $MyNodes], ['breadcrumbs' => $breadcrumbs]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create()
    {
        if (Auth::user()->cannot('create', Node::class)) { return redirect('/'); }
        $nodes = collect(Auth::user()->nodes);

        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"], ['link' => action('Web\NodeController@index'), 'name' => "Nodes"],
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'isFabButton' => true];
        return view('pages.nodes.create', ['pageConfigs' => $pageConfigs, 'Nodes' => $nodes], ['breadcrumbs' => $breadcrumbs]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        //user allowed?
        $response = Gate::inspect('create', Node::class);
        if(!$response->allowed()){
            //create errror message
            return Redirect::back()->withErrors([$response->message()]);
        }

        //Validation 
        $request->validate([
            'name' => 'required|min:5|max:255',
            'dev_eui' => 'required',
            'node_type_id' => 'gt:0'
        ]);

        //Add some fields for repository
        $request->request->add(['user_id'=>Auth::user()->id]); 
        $request->request->add(['city_id'=>0]);
        $this->repository->create($request->all());

        //return to site with success message
        Session::flash('message', "Node \"".$request->name."\" created");
        return Redirect::back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Node  $node
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show(Node $node)
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
            ['link' => action('Web\NodeController@index'), 'name' => "Nodes"],
            ['link' => action('Web\NodeController@show', ['node' => $node->id]), 'name' => $node->name . " Node"],
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Node  $node
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Node $node)
    {
        //user allowed?
        $response = Gate::inspect('update', $node);
        if(!$response->allowed()){
            //create errror message
            return Redirect::back()->withErrors([$response->message()]);
        }

        $request->validate([
            'name' => 'required|min:5|max:255',
            'dev_eui' => 'required',
            'nodetype' => 'gt:0'
        ]);

        $node->name = $request->name;
        $node->dev_eui = $request->dev_eui;
        $node->node_type_id = $request->nodetype;
        $node->save();
        Session::flash('message', 'Node Updated');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Node  $node
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Node $node)
    {
        $response = Gate::inspect('delete', $node);
        if(!$response->allowed()){
            return response()->json($response->message(), 401, [], JSON_PRETTY_PRINT);
        }

        $this->repository->delete($node->id);
        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Node  $node
     * @return \Illuminate\Http\RedirectResponse
     */
    public function position(Request $request, Node $node)
    {
        if ($request->startPos > 0 && $request->newPos <= $node->fields->count()) {
            $node->fields->where('position', $request->startPos)->first()->update(['position' => $request->newPos]);
        }
    }
}
