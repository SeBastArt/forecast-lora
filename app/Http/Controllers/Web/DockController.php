<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Node;
use App\NodeData;
use App\FieldData;
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
        $json = DecodeHelper::json_clean_decode($request->input('json'));
        
        //DevEUI_uplink means Swisscom
        //get regstred nodes
        //dd($json);
        $dev_eui = isset($json['DevEUI_uplink']) ? $json['DevEUI_uplink.DevEUI'] : $json['hardware_serial'];
        $nodes = Node::all()->where('dev_eui', '=', $dev_eui);
     
        //get the raw payload in hex 
        $payload_hex = isset($json['DevEUI_uplink']) ? $json['DevEUI_uplink']['payload_hex'] : bin2hex(base64_decode($json['payload_raw']));

        $gateways = isset($json['DevEUI_uplink']) ?  $json['DevEUI_uplink']['Lrrs']['Lrr'] : $json['metadata']['gateways']; 
  
        $max_rssi = DecodeHelper::get_max_rssi($gateways);
        $max_snr = DecodeHelper::get_max_snr($gateways);
    
        foreach ($nodes as $nodeKey => $nodeValue) {
            dd();
            $nodedata = NodeData::create([
                'snr' => $max_snr,
                'rssi' => $max_rssi,
                'payload' => $payload_hex,
                'node_id' => $nodeValue->id
            ]);
            switch ($nodeValue->type->name) {
                case 'cayenne':
                    $dataArray = DecodeHelper::cayenne_payload_to_json($payload_hex);
                    break;
                case 'dragino':
                    $dataArray = DecodeHelper::dragino_payload_to_json($payload_hex);
                    break;
                case 'decentlab':
                    $dataArray = DecodeHelper::decent_payload_to_json($payload_hex);
                    break;
                case 'zane':
                    $dataArray = DecodeHelper::decent_payload_to_json($payload_hex);
                    break;
                default:
                    $dataArray = [];
                    break;
            }
            $nodedata->longitude =  (isset($dataArray['longitude'])) ? $dataArray['longitude'] : null;
            $nodedata->latitude =  (isset($dataArray['latitude'])) ? $dataArray['latitude'] : null;
            $nodedata->save();
            foreach ($dataArray as $dataKey => $dataValue) {
                if ($dataKey < $nodeValue->fields->count()){
                    $fieldData = FieldData::create([
                        'node_data_id' => $nodedata->id,
                        'field_id' => $nodeValue->fields[$dataKey]->id,
                        'value' => $dataValue
                    ]);
                }
            }
            DecodeHelper::ProcessGateways($gateways, $nodedata->id);
        }
        return back()->with('status', 'Field Created');
    }
}
