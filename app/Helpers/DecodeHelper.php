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
};
?>