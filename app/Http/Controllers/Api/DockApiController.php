<?php

namespace App\Http\Controllers\Api;

use App\Helpers\DecodeHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DockApiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('api');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function dock(Request $request): JsonResponse
    {
        //need to devide if TTN-Network or Swisscom
        //get the DevEUI
        $devEUI = ($request->input('DevEUI_uplink') !== null) ? $request->input('DevEUI_uplink.DevEUI') : $request->input('hardware_serial');
        ///return response()->json([ "message" => $devEUI], 200);
        //get the raw payload in hex
        $payloadHex = ($request->input('DevEUI_uplink') !== null) ? $request->input('DevEUI_uplink.payload_hex') : bin2hex(base64_decode($request->input('payload_raw')));
        //get the Gateway part of incoming data
        $gatewayArray = ($request->input('DevEUI_uplink') !== null) ? $request->input('DevEUI_uplink.Lrrs.Lrr') : $request->input('metadata.gateways');

        DecodeHelper::processInput($devEUI, $payloadHex, $gatewayArray);
        return response()->json(["message" => "incoming data processed."], 201);
    }
}



//const JSON_STR_DECENT = '{"app_id":"ia","dev_id":"decentlab","hardware_serial":"0004A30B001FDC0B","port":1,"counter":237,"payload_raw":"AgjKAAOBZQvO","payload_fields":{"Version":2,"deviceID":2250,"sensor0":3.022,"sensor1":22.3125,"sensorCount":2},"metadata":{"time":"2018-12-03T20:39:04.748804857Z","frequency":867.1,"modulation":"LORA","data_rate":"SF7BW125","coding_rate":"4/5","gateways":[{"gtw_id":"eui-c0ee40ffff294870","gtw_trusted":true,"timestamp":1067417243,"time":"","channel":3,"rssi":-44,"snr":8.5,"rf_chain":0,"latitude":51.072018,"longitude":13.768493,"altitude":153,"location_source":"registry"}]},"downlink_url":"https://integrations.thethingsnetwork.org/ttn-eu/api/v2/down/ia/black_mesa?key=ttn-account-v2.HPgeEGfHNCIm3GMzY5shGebD_XNafL18ljnFaGNzZSk"}';

//const JSON_STR_ZANE = '{"app_id":"ia","dev_id":"decentlab","hardware_serial":"0004A30B001FDC0B","port":1,"counter":237,"payload_raw":"EEEz","payload_fields":{"Version":2,"deviceID":2250,"sensor0":3.022,"sensor1":22.3125,"sensorCount":2},"metadata":{"time":"2018-12-03T20:39:04.748804857Z","frequency":867.1,"modulation":"LORA","data_rate":"SF7BW125","coding_rate":"4/5","gateways":[{"gtw_id":"eui-c0ee40ffff294870","gtw_trusted":true,"timestamp":1067417243,"time":"","channel":3,"rssi":-44,"snr":8.5,"rf_chain":0,"latitude":51.072018,"longitude":13.768493,"altitude":153,"location_source":"registry"}]},"downlink_url":"https://integrations.thethingsnetwork.org/ttn-eu/api/v2/down/ia/black_mesa?key=ttn-account-v2.HPgeEGfHNCIm3GMzY5shGebD_XNafL18ljnFaGNzZSk"}';

//const JSON_2gtw_decent = '{"app_id":"ia","dev_id":"decentlab","hardware_serial":"0025CA0A0000E78B","port":1,"counter":237,"payload_raw":"AgjKAAOBZQvO","payload_fields":{"Version":2,"deviceID":2250,"sensor0":3.022,"sensor1":22.3125,"sensorCount":2},"metadata":{"time":"2018-12-03T20:39:04.748804857Z","frequency":867.1,"modulation":"LORA","data_rate":"SF7BW125","coding_rate":"4/5","gateways":[{"gtw_id":"eui-c0ee40ffff294861","gtw_trusted":true,"timestamp":1067417243,"time":"","channel":3,"rssi":-44,"snr":8.5,"rf_chain":0,"latitude":51.072018,"longitude":13.768493,"altitude":153,"location_source":"registry"},{"gtw_id":"eui-testident","gtw_trusted":true,"timestamp":1067417243,"time":"","channel":3,"rssi":-78,"snr":6.5,"rf_chain":0,"latitude":54.072018,"longitude":12.768493,"altitude":123,"location_source":"registry"}]},"latitude":51.26964,"longitude":14.096932,"altitude":139,"downlink_url":"https://integrations.thethingsnetwork.org/ttn-eu/api/v2/down/ia/black_mesa?key=ttn-account-v2.HPgeEGfHNCIm3GMzY5shGebD_XNafL18ljnFaGNzZSk"}';

//const JSON_2gtw_decent = '{"app_id":"ia","dev_id":"decentlab","hardware_serial":"0004A30B001FDC0A","port":1,"counter":237,"payload_raw":"AgjKAAOBZQvO","payload_fields":{"Version":2,"deviceID":2250,"sensor0":3.022,"sensor1":22.3125,"sensorCount":2},"metadata":{"time":"2018-12-03T20:39:04.748804857Z","frequency":867.1,"modulation":"LORA","data_rate":"SF7BW125","coding_rate":"4/5","gateways":[{"gtw_id":"eui-c0ee40ffff294870","gtw_trusted":true,"timestamp":1067417243,"time":"","channel":3,"rssi":-44,"snr":8.5,"rf_chain":0,"latitude":51.072018,"longitude":13.768493,"altitude":153,"location_source":"registry"},{"gtw_id":"eui-testident","gtw_trusted":true,"timestamp":1067417243,"time":"","channel":3,"rssi":-78,"snr":6.5,"rf_chain":0,"latitude":54.072018,"longitude":12.768493,"altitude":123,"location_source":"registry"}]},"longitude":14.096932,"downlink_url":"https://integrations.thethingsnetwork.org/ttn-eu/api/v2/down/ia/black_mesa?key=ttn-account-v2.HPgeEGfHNCIm3GMzY5shGebD_XNafL18ljnFaGNzZSk"}';

//const JSON_SwissCom_dragino_Cayenne = '{"DevEUI_uplink": {"Time": "2018-12-27T21:25:07.496+01:00","DevEUI": "A84041000181854B","FPort": 2,"FCntUp": 173,"ADRbit": 1,"MType": 2,"FCntDn": 168,"payload_hex": "01020125026700aa03000004020005050000","mic_hex": "a8f45435","Lrcid": "00000401","LrrRSSI": -91.000000,"LrrSNR": 8.000000,"SpFact": 12,"SubBand": "G0","Channel": "LC5","DevLrrCnt": 10,"Lrrid": "080E04D1","Late": 0,"LrrLAT": 47.351612,"LrrLON": 8.490310,"Lrrs": {"Lrr": [{"Lrrid": "080E04D1","Chain": 0,"LrrRSSI": -91.000000,"LrrSNR": 8.000000,"LrrESP": -91.638924},{"Lrrid": "29000085","Chain": 0,"LrrRSSI": -103.000000,"LrrSNR": 5.500000,"LrrESP": -104.078331},{"Lrrid": "29000128","Chain": 0,"LrrRSSI": -118.000000,"LrrSNR": -5.500000,"LrrESP": -124.578331}]},"CustomerID": "100016276","CustomerData": {"alr":{"pro":"LORA/Generic","ver":"1"}},"ModelCfg": "0","InstantPER": 0.000000,"MeanPER": 0.000003,"DevAddr": "08132F47","TxPower": 16.000000,"NbTrans": 1}}';

//const JSON_STR_DRAGINO = '{"app_id":"ia","dev_id":"decentlab","hardware_serial":"A84041000181852E","port":1,"counter":237,"payload_raw":"C50BBgAAJwA=","payload_fields":{"Version":2,"deviceID":2250,"sensor0":3.022,"sensor1":22.3125,"sensorCount":2},"metadata":{"time":"2018-12-03T20:39:04.748804857Z","frequency":867.1,"modulation":"LORA","data_rate":"SF7BW125","coding_rate":"4/5","gateways":[{"gtw_id":"eui-c0ee40ffff294666","gtw_trusted":true,"timestamp":1067417243,"time":"","channel":3,"rssi":-44,"snr":8.5,"rf_chain":0,"latitude":51.072018,"longitude":13.768493,"altitude":153,"location_source":"registry"}]},"downlink_url":"https://integrations.thethingsnetwork.org/ttn-eu/api/v2/down/ia/black_mesa?key=ttn-account-v2.HPgeEGfHNCIm3GMzY5shGebD_XNafL18ljnFaGNzZSk"}';
