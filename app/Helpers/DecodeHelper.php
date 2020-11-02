<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;

use App\Gateway;
use App\GatewayData;

class DecodeHelper
{
    public static function json_clean_decode($json, $assoc = true, $depth = 512, $options = 0)
    {
        
        // search and remove comments like /* */ and //
        $json = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t]//.*)|(^//.*)#", '', $json);

        if (version_compare(phpversion(), '5.4.0', '>=')) {
            $json = json_decode($json, $assoc, $depth, $options);
        } elseif (version_compare(phpversion(), '5.3.0', '>=')) {
            $json = json_decode($json, $assoc, $depth);
        } else {
            $json = json_decode($json, $assoc);
        }
       
        return $json;
    }

    public static function decent_payload_to_json($payload_hex){
        $result_array = array();

        $payload_flags_hex = substr($payload_hex, 6, 4);
        $bitArray = array_reverse(str_split(base_convert($payload_flags_hex, 16, 2)));

        $sensorCount = 0;
        foreach ($bitArray as $key => $value) {
            if ($value) {
                $pos = 10 + $sensorCount * 4;
                $varInt = hexdec(substr($payload_hex, $pos, 4));
                if ($varInt > 30000) {
                    $var_float = ($varInt - 32768) / 16;
                    $result_array[$sensorCount] = $var_float;
                } else {
                    $var_float = $varInt / 1000;
                    $result_array[$sensorCount] = $var_float;
                }
                $sensorCount++;
            }
        }
        return $result_array;
    }

    public static function zane_payload_to_json($payload_hex)
    {
        $result_array = array();

        $sign = hexdec(substr($payload_hex, 0, 1));
        $T_ten = hexdec(substr($payload_hex, 1, 1));
        $T_ones = hexdec(substr($payload_hex, 2, 1));
        $T_tenth = hexdec(substr($payload_hex, 3, 1));
        $temp = ($T_ten * 10) + ($T_ones * 1) +  ($T_tenth / 10);

        $V_ones = hexdec(substr($payload_hex, 4, 1));
        $V_tenth = hexdec(substr($payload_hex, 5, 1));
        $Volt = ($V_ones * 1) +  ($V_tenth / 10);

        if ($sign > 0) { $temp = $temp * -1; }

        $result_array[0] = $temp;
        $result_array[1] = $Volt;

        return $result_array;
    }

    public static function dragino_payload_to_json($payload_hex){
        $result_array = array();

        $Volt = hexdec(substr($payload_hex, 0, 4)) / 1000;
        $temp = hexdec(substr($payload_hex, 4, 4));
        if ($temp > 60000){ $temp = $temp - 65536; } // minus werte
        $temp = $temp / 10;
        $result_array[0] = $temp;
        $result_array[1] = $Volt;

        return $result_array;
    }

    public static function cayenne_payload_to_json($payload_hex){
        $result_array = array();
        $jump = 1;
        $frame_port_length = 2;
        $data_type_length = 2;
        $j = 0;
        for ($i = 0; $i < strlen($payload_hex); $i = $i + $jump) {
            $frame_port = substr($payload_hex, $i, 2);
            $data_type = hexdec(substr($payload_hex, $i + 2, 2));
            $data_size = 0;
            switch ($data_type) {
                case 0:         //Digital Input
                case 1:         //Digital Output
                case 102:       //Presence Sensor
                case 104:       //Humidity Sensor
                    $data_size = 1;
                    break;
                case 2:         //Analog Input
                case 3:         //Analog Output
                case 101:       //Illuminance Sensor
                case 103:       //Temperature Sensor
                case 115:       //Barometer
                    $data_size = 2;
                    break;
                case 113:       //Accelerometer
                case 134:       //Gyrometer
                    $data_size = 6;
                    break;
                case 136:       //GPS Location
                    $data_size = 9;
                break;
                default:
                    $data_size = 2;
            }
            switch ($data_type) {
                case 1: 
                case 2:         //Analog Input
                case 3:         //Analog Output
                    $result_array [$j] = hexdec(substr($payload_hex, $i + 4, $data_size * 2));
                    $j++;
                    break;
                case 104:
                    $result_array [$j] = hexdec(substr($payload_hex, $i + 4, $data_size * 2)) * 0.5;
                    $j++;
                    break;
                case 103:       //Temperature Sensor
                case 115:       //Barometer
                    $temp_value = hexdec(substr($payload_hex, $i + 4, $data_size * 2));
                    $temp_value = ($temp_value > 1000) ? $temp_value - 65536.0 : $temp_value;
                    $result_array [$j] = $temp_value * 0.1;
                    $j++;
                    break;
                case 136:
                $lat = hexdecs(substr($payload_hex, $i + 4, 6)) * 0.0001;
                $long = hexdecs(substr($payload_hex, $i + 10, 6)) * 0.0001 ;
                $alt = hexdecs(substr($payload_hex, $i + 16, 6)) * 0.01 ;
                $result_array['longitude'] = $long;
                $result_array['latitude'] = $lat;
                break;
                default:
                    //
                    break;
            }
            
            $jump =  $frame_port_length + $data_type_length + ($data_size * 2);
        }
        return $result_array;
    }

    public static function get_max_rssi($requestGateways){

        $max_rssi = -PHP_FLOAT_MAX;
        foreach ($requestGateways as $gateway) {          
            $max_rssi = isset( $gateway['rssi'] ) ? max($gateway['rssi'], $max_rssi) : max($gateway['LrrRSSI'], $max_rssi);                     
        }
        return $max_rssi;
    }

    public static function get_max_snr($requestGateways){
        $max_snr = -PHP_FLOAT_MAX;
        foreach ($requestGateways as $gateway) {
            $max_snr = isset( $gateway['snr'] ) ? max($gateway['snr'], $max_snr) : max($gateway['LrrSNR'], $max_snr);            
        }
        return $max_snr;
    }

    public static function ProcessGateways($_requestGateways, $_nodeDataId){
        foreach ($_requestGateways as $gateway) {
            $dev_eui = isset( $gateway['Lrrid'] ) ? $gateway['Lrrid'] : $gateway['gtw_id'];
            $rssi = isset($gateway['rssi']) ? $gateway['rssi'] : $gateway['LrrRSSI'];            
            $snr = isset($gateway['snr']) ? $gateway['snr'] : $gateway['LrrSNR'];        
            $latitude = isset($gateway['latitude']) ? $gateway['latitude'] : null;
            $longitude = isset($gateway['longitude']) ? $gateway['longitude'] : null;

            $gateway = Gateway::all()->where('dev_eui', '=', $dev_eui)->first();
            if (!$gateway){
                $createGateway = Gateway::create([
                    'dev_eui' =>  $dev_eui,
                    'latitude' =>  $latitude,
                    'longitude' =>  $longitude,
                    'lastseen' => now()
                ]);
            $gateway_id =  $createGateway->id;
            } else { 
                $gateway->lastseen = now();
                $gateway->latitude = $latitude;
                $gateway->longitude = $longitude;
                $gateway->save();
                $gateway_id =  $gateway->id;
            }

            $gatewayData = GatewayData::create([
                'node_data_id' =>  $_nodeDataId,
                'gateway_id' =>  $gateway_id,
                'snr' =>  $snr,
                'rssi' => $rssi,
            ]);
        }
    }

    public static function convertPhpToJsMomentFormat(string $phpFormat): string
    {
        $replacements = [
            'A' => 'A',      // for the sake of escaping below
            'a' => 'a',      // for the sake of escaping below
            'B' => '',       // Swatch internet time (.beats), no equivalent
            'c' => 'YYYY-MM-DD[T]HH:mm:ssZ', // ISO 8601
            'D' => 'ddd',
            'd' => 'DD',
            'e' => 'zz',     // deprecated since version 1.6.0 of moment.js
            'F' => 'MMMM',
            'G' => 'H',
            'g' => 'h',
            'H' => 'HH',
            'h' => 'hh',
            'I' => '',       // Daylight Saving Time? => moment().isDST();
            'i' => 'mm',
            'j' => 'D',
            'L' => '',       // Leap year? => moment().isLeapYear();
            'l' => 'dddd',
            'M' => 'MMM',
            'm' => 'MM',
            'N' => 'E',
            'n' => 'M',
            'O' => 'ZZ',
            'o' => 'YYYY',
            'P' => 'Z',
            'r' => 'ddd, DD MMM YYYY HH:mm:ss ZZ', // RFC 2822
            'S' => 'o',
            's' => 'ss',
            'T' => 'z',      // deprecated since version 1.6.0 of moment.js
            't' => '',       // days in the month => moment().daysInMonth();
            'U' => 'X',
            'u' => 'SSSSSS', // microseconds
            'v' => 'SSS',    // milliseconds (from PHP 7.0.0)
            'W' => 'W',      // for the sake of escaping below
            'w' => 'e',
            'Y' => 'YYYY',
            'y' => 'YY',
            'Z' => '',       // time zone offset in minutes => moment().zone();
            'z' => 'DDD',
        ];

        // Converts escaped characters.
        foreach ($replacements as $from => $to) {
            $replacements['\\' . $from] = '[' . $from . ']';
        }

        return strtr($phpFormat, $replacements);
    }

};



//const JSON_STR_DECENT = '{"app_id":"ia","dev_id":"decentlab","hardware_serial":"0004A30B001FDC0A","port":1,"counter":237,"payload_raw":"AgjKAAOBZQvO","payload_fields":{"Version":2,"deviceID":2250,"sensor0":3.022,"sensor1":22.3125,"sensorCount":2},"metadata":{"time":"2018-12-03T20:39:04.748804857Z","frequency":867.1,"modulation":"LORA","data_rate":"SF7BW125","coding_rate":"4/5","gateways":[{"gtw_id":"eui-c0ee40ffff294870","gtw_trusted":true,"timestamp":1067417243,"time":"","channel":3,"rssi":-44,"snr":8.5,"rf_chain":0,"latitude":51.072018,"longitude":13.768493,"altitude":153,"location_source":"registry"}]},"downlink_url":"https://integrations.thethingsnetwork.org/ttn-eu/api/v2/down/ia/black_mesa?key=ttn-account-v2.HPgeEGfHNCIm3GMzY5shGebD_XNafL18ljnFaGNzZSk"}';

//const JSON_STR_ZANE = '{"app_id":"ia","dev_id":"decentlab","hardware_serial":"0004A30B001FDC0B","port":1,"counter":237,"payload_raw":"EEEz","payload_fields":{"Version":2,"deviceID":2250,"sensor0":3.022,"sensor1":22.3125,"sensorCount":2},"metadata":{"time":"2018-12-03T20:39:04.748804857Z","frequency":867.1,"modulation":"LORA","data_rate":"SF7BW125","coding_rate":"4/5","gateways":[{"gtw_id":"eui-c0ee40ffff294870","gtw_trusted":true,"timestamp":1067417243,"time":"","channel":3,"rssi":-44,"snr":8.5,"rf_chain":0,"latitude":51.072018,"longitude":13.768493,"altitude":153,"location_source":"registry"}]},"downlink_url":"https://integrations.thethingsnetwork.org/ttn-eu/api/v2/down/ia/black_mesa?key=ttn-account-v2.HPgeEGfHNCIm3GMzY5shGebD_XNafL18ljnFaGNzZSk"}';

//const JSON_2gtw_decent = '{"app_id":"ia","dev_id":"decentlab","hardware_serial":"0004A30B001FDC0A","port":1,"counter":237,"payload_raw":"AgjKAAOBZQvO","payload_fields":{"Version":2,"deviceID":2250,"sensor0":3.022,"sensor1":22.3125,"sensorCount":2},"metadata":{"time":"2018-12-03T20:39:04.748804857Z","frequency":867.1,"modulation":"LORA","data_rate":"SF7BW125","coding_rate":"4/5","gateways":[{"gtw_id":"eui-c0ee40ffff294861","gtw_trusted":true,"timestamp":1067417243,"time":"","channel":3,"rssi":-44,"snr":8.5,"rf_chain":0,"latitude":51.072018,"longitude":13.768493,"altitude":153,"location_source":"registry"},{"gtw_id":"eui-testident","gtw_trusted":true,"timestamp":1067417243,"time":"","channel":3,"rssi":-78,"snr":6.5,"rf_chain":0,"latitude":54.072018,"longitude":12.768493,"altitude":123,"location_source":"registry"}]},"latitude":51.26964,"longitude":14.096932,"altitude":139,"downlink_url":"https://integrations.thethingsnetwork.org/ttn-eu/api/v2/down/ia/black_mesa?key=ttn-account-v2.HPgeEGfHNCIm3GMzY5shGebD_XNafL18ljnFaGNzZSk"}';

//const JSON_2gtw_decent = '{"app_id":"ia","dev_id":"decentlab","hardware_serial":"0004A30B001FDC0A","port":1,"counter":237,"payload_raw":"AgjKAAOBZQvO","payload_fields":{"Version":2,"deviceID":2250,"sensor0":3.022,"sensor1":22.3125,"sensorCount":2},"metadata":{"time":"2018-12-03T20:39:04.748804857Z","frequency":867.1,"modulation":"LORA","data_rate":"SF7BW125","coding_rate":"4/5","gateways":[{"gtw_id":"eui-c0ee40ffff294870","gtw_trusted":true,"timestamp":1067417243,"time":"","channel":3,"rssi":-44,"snr":8.5,"rf_chain":0,"latitude":51.072018,"longitude":13.768493,"altitude":153,"location_source":"registry"},{"gtw_id":"eui-testident","gtw_trusted":true,"timestamp":1067417243,"time":"","channel":3,"rssi":-78,"snr":6.5,"rf_chain":0,"latitude":54.072018,"longitude":12.768493,"altitude":123,"location_source":"registry"}]},"longitude":14.096932,"downlink_url":"https://integrations.thethingsnetwork.org/ttn-eu/api/v2/down/ia/black_mesa?key=ttn-account-v2.HPgeEGfHNCIm3GMzY5shGebD_XNafL18ljnFaGNzZSk"}';

//const JSON_SwissCom_dragino_Cayenne = '{"DevEUI_uplink": {"Time": "2018-12-27T21:25:07.496+01:00","DevEUI": "A84041000181854B","FPort": 2,"FCntUp": 173,"ADRbit": 1,"MType": 2,"FCntDn": 168,"payload_hex": "01020125026700aa03000004020005050000","mic_hex": "a8f45435","Lrcid": "00000401","LrrRSSI": -91.000000,"LrrSNR": 8.000000,"SpFact": 12,"SubBand": "G0","Channel": "LC5","DevLrrCnt": 10,"Lrrid": "080E04D1","Late": 0,"LrrLAT": 47.351612,"LrrLON": 8.490310,"Lrrs": {"Lrr": [{"Lrrid": "080E04D1","Chain": 0,"LrrRSSI": -91.000000,"LrrSNR": 8.000000,"LrrESP": -91.638924},{"Lrrid": "29000085","Chain": 0,"LrrRSSI": -103.000000,"LrrSNR": 5.500000,"LrrESP": -104.078331},{"Lrrid": "29000128","Chain": 0,"LrrRSSI": -118.000000,"LrrSNR": -5.500000,"LrrESP": -124.578331}]},"CustomerID": "100016276","CustomerData": {"alr":{"pro":"LORA/Generic","ver":"1"}},"ModelCfg": "0","InstantPER": 0.000000,"MeanPER": 0.000003,"DevAddr": "08132F47","TxPower": 16.000000,"NbTrans": 1}}';

//const JSON_STR_DRAGINO = '{"app_id":"ia","dev_id":"decentlab","hardware_serial":"A84041000181852E","port":1,"counter":237,"payload_raw":"C50BBgAAJwA=","payload_fields":{"Version":2,"deviceID":2250,"sensor0":3.022,"sensor1":22.3125,"sensorCount":2},"metadata":{"time":"2018-12-03T20:39:04.748804857Z","frequency":867.1,"modulation":"LORA","data_rate":"SF7BW125","coding_rate":"4/5","gateways":[{"gtw_id":"eui-c0ee40ffff294666","gtw_trusted":true,"timestamp":1067417243,"time":"","channel":3,"rssi":-44,"snr":8.5,"rf_chain":0,"latitude":51.072018,"longitude":13.768493,"altitude":153,"location_source":"registry"}]},"downlink_url":"https://integrations.thethingsnetwork.org/ttn-eu/api/v2/down/ia/black_mesa?key=ttn-account-v2.HPgeEGfHNCIm3GMzY5shGebD_XNafL18ljnFaGNzZSk"}';

?>


