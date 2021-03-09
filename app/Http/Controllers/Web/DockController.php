<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Node;
use App\Models\NodeData;
use App\Helpers\DecodeHelper;

class DockController extends Controller
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
        $breadcrumbs = [
            ['name' => "Home"], ['link' => action('Web\DockController@index'), 'name' => "Dock"],
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'isFabButton' => true];
        return view('pages.dock.index', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs]);
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
            'json' => 'required|min:100|max:1500',
        ]);

        $json = DecodeHelper::json_clean_decode($request->input('json'));

        //need to devide if TTN-Network or Swisscom
        //get the DevEUI  
        $devEUI = isset($json['DevEUI_uplink']) ? $json['DevEUI_uplink']['DevEUI'] : $json['hardware_serial'];

        //get the raw payload in hex 
        $payloadHex = isset($json['DevEUI_uplink']) ? $json['DevEUI_uplink']['payload_hex'] : bin2hex(base64_decode($json['payload_raw']));
        //get the Gateway part of incoming data
        $gatewaysJson = isset($json['DevEUI_uplink']) ?  $json['DevEUI_uplink']['Lrrs']['Lrr'] : $json['metadata']['gateways'];

        DecodeHelper::processInput($devEUI, $payloadHex, $gatewaysJson);
        return back()->with('status', 'Nodedata Created');
    }
}
