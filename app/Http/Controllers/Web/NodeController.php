<?php

namespace App\Http\Controllers\Web;

use App\Field;
use App\Forecast;
use App\Helpers\MyHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Node;
use Illuminate\Http\Request;
use App\NodeType;
use App\Weather;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Providers\NodePolicy;

class NodeController extends Controller
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
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $userNodes = collect(Auth::user()->nodes);

        $userNodeCollection = collect();

        foreach ($userNodes as $userNode) {
            $primaryField = $userNode->fields->sortBy('position')->first();
            $primaryFieldCollection = null;
            if ($primaryField->data->count() > 0) {
                $primaryFieldCollection = collect([
                    'unit' => $primaryField->unit,
                    'min' => number_format($primaryField->data->where('created_at', '>', Carbon::now()->subMinutes(1440))->min('value'), 1, '.', ''), //format to one digit,
                    'max' => number_format($primaryField->data->where('created_at', '>', Carbon::now()->subMinutes(1440))->max('value'), 1, '.', ''), //format to one digit,
                    'last'  =>  collect([
                        'value' => $primaryField->data->last()->value,
                        'timestamp' => $primaryField->data->last()->created_at->format('H:i:s')
                    ])
                ]);
            }

            $secondaryField = $userNode->fields->count() > 1 ? $userNode->fields->sortBy('position')->skip(1)->first() : $userNode->fields->sortBy('position')->first();
            $secondaryFieldCollection = null;
            if ($secondaryField->data->count() > 0) {
                $secondaryFieldCollection = collect([
                    'unit' => $secondaryField->unit,
                    'min' => number_format($secondaryField->data->where('created_at', '>', Carbon::now()->subMinutes(1440))->min('value'), 1, '.', ''), //format to one digit,
                    'max' => number_format($secondaryField->data->where('created_at', '>', Carbon::now()->subMinutes(1440))->max('value'), 1, '.', ''), //format to one digit,
                    'last' => $secondaryField->data->last()->value,
                ]);
            }

            $node = collect([
                'Node' => $userNode,
                'primaryField' => $primaryFieldCollection,
                'secondaryField' => $secondaryFieldCollection,
            ]);

            $userNodeCollection->push($node);
        }
        //return response()->json($userNodeCollection,200,[],JSON_PRETTY_PRINT);
        $breadcrumbs = [
            ['link' => action('HomeController@index'), 'name' => "Home"],
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'bodyCustomClass' => 'menu-collapse', 'isFabButton' => true];

        return view('pages.nodes.index', ['pageConfigs' => $pageConfigs, 'userNodeCollection' => $userNodeCollection], ['breadcrumbs' => $breadcrumbs]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create()
    {
        $user = Auth::user();

        if ($user->cannot('create', Node::class)) { return back(); }
        
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
        $request->validate([
            'name' => 'required|min:5|max:255',
            'dev_eui' => 'required',
            'nodetype' => 'gt:0'
        ]);

        $node = Node::create([
            'name' => $request->name,
            'dev_eui' => $request->dev_eui,
            'node_type_id' => $request->nodetype,
            'user_id' => Auth::user()->id,
            'city_id' => 0,
        ]);
        return back()->with('status', 'Node Created');

        //return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Node  $node
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show(Node $node)
    {
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"],
            ['link' => action('Web\NodeController@index'), 'name' => "Nodes"],
            ['link' => action('Web\NodeController@show', ['node' => $node->id]), 'name' => $node->name . " Node"],
        ];
        $primaryFieldCollection = null;
        $secondaryFieldCollection = null;

        if ($node->fields->count() > 0) {
            $primaryField = $node->fields->sortBy('position')->first();
            if ($primaryField->data->count() > 0) {
                $primaryFieldCollection = collect([
                    'unit' => $primaryField->unit,
                    'min' => number_format($primaryField->data->where('created_at', '>', Carbon::now()->subMinutes(1440))->min('value'), 1, '.', ''), //format to one digit,
                    'max' => number_format($primaryField->data->where('created_at', '>', Carbon::now()->subMinutes(1440))->max('value'), 1, '.', ''), //format to one digit,
                    'last'  =>  collect([
                        'value' => $primaryField->data->last()->value,
                        'timestamp' => $primaryField->data->last()->created_at->format('H:i:s')
                    ])
                ]);
            }

            $secondaryField = $node->fields->count() > 1 ? $node->fields->sortBy('position')->skip(1)->first() : $node->fields->sortBy('position')->first();
            if ($secondaryField->data->count() > 0) {
                $secondaryFieldCollection = collect([
                    'unit' => $secondaryField->unit,
                    'min' => number_format($secondaryField->data->where('created_at', '>', Carbon::now()->subMinutes(1440))->min('value'), 1, '.', ''), //format to one digit,
                    'max' => number_format($secondaryField->data->where('created_at', '>', Carbon::now()->subMinutes(1440))->max('value'), 1, '.', ''), //format to one digit,
                    'last' => $secondaryField->data->last()->value,
                ]);
            }
        }

        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'isFabButton' => true];
        return view(
            'pages.nodes.show',
            [
                'pageConfigs' => $pageConfigs,
                'primaryField' => $primaryFieldCollection,
                'secondaryField' => $secondaryFieldCollection,
                'Node' => $node
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
        $request->validate([
            'name' => 'required|min:5|max:255',
            'dev_eui' => 'required',
            'nodetype' => 'gt:0'
        ]);

        $node->name = $request->name;
        $node->dev_eui = $request->dev_eui;
        $node->node_type_id = $request->nodetype;
        $node->save();

        return back()->with('status', 'Node Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Node  $node
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Node $node)
    {
        $node->delete();
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
