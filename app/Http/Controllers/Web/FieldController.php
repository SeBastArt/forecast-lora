<?php

namespace App\Http\Controllers\Web;

use App\Models\Field;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Node;
use App\Models\Preset;
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Node  $node
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeNode(Request $request, Node $node)
    {
        //user allowed?
        $response = Gate::inspect('create', Field::class);
        if (!$response->allowed()) {
            //create errror message
            return redirect(
                action(
                    'Web\NodeController@index',
                    ['facility' => $node->facility->id]
                )
            )
                ->withErrors([$response->message()]);
        }

        //todo node is missing
        $request->validate([
            'name' => 'required|min:3|max:255',
            'unit' => 'required',
        ]);

        //$this->fieldService->create($node, $request);
        $node->fields()->attach($this->fieldService->create($request));
        return back()->with('status', 'Field Created');
    }

    public function storePreset(Request $request, Preset $preset)
    {
        //user allowed?
        $response = Gate::inspect('update', $preset);
        if (!$response->allowed()) {
            //create errror message
            return redirect(
                action(
                    'Web\PresetController@index'
                )
            )
                ->withErrors([$response->message()]);
        }

        //todo node is missing
        $request->validate([
            'name' => 'required|min:3|max:255',
            'unit' => 'required',
        ]);

        //$this->fieldService->create($node, $request);
        $preset->fields()->attach($this->fieldService->create($request));
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
        //user allowed?
        $response = Gate::inspect('update', $field);
        if (!$response->allowed()) {
            //create errror message
            if($field->presets()->first() !== null){   
                return redirect(action('Web\PresetController@index'));
            }
            return redirect(
                action(
                    'Web\NodeController@index',
                    ['facility' => $field->nodes()->first()->facility->id]
                )
            )
                ->withErrors([$response->message()]);
        }
        $request->validate([
            'name' => 'required|min:3|max:255',
            'unit' => 'required',
            'primary_color' => 'required',
            'secondary_color' => 'required',
            'check_upper_limit' => 'sometimes',
            'upper_limit' => 'exclude_if:check_upper_limit,null|required|numeric',
            'check_lower_limit' => 'sometimes',
            'lower_limit' => 'exclude_if:check_lower_limit,null|required|numeric',
        ]);

        $this->fieldService->update($field, $request);
        return back()->with('status', 'Field ' . $field->name . ' Updated');
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param  \App\Field  $field
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Field $field)
    {
        //user allowed?
        $response = Gate::inspect('delete', $field);
        if (!$response->allowed()) {
            //create errror message
            if($field->presets()->first() !== null){   
                return redirect(action('Web\PresetController@index'));
            }
            return redirect(
                action(
                    'Web\NodeController@index',
                    ['facility' => $field->nodes()->first()->facility->id]
                )
            )
                ->withErrors([$response->message()]);
        }

        $this->fieldRepository->delete($field->id);
        return back();
    }
}
