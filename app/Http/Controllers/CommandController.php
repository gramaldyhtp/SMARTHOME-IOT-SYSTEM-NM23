<?php

namespace App\Http\Controllers;

use PhpMqtt\Client\MqttClient;

class CommandController extends Controller
{
    public function sendCommand()
    {
        $device  = request('device');
        $command = request('command');

        $mqtt = new MqttClient(env('MQTT_HOST'), env('MQTT_PORT'), 'laravel_cmd');

        $mqtt->connect();

        $topic = "home/$device/cmd";

        $mqtt->publish($topic, json_encode([
            "device"  => $device,
            "command" => $command,
        ]), 0);

        $mqtt->disconnect();

        return response()->json([
            "status" => "success",
            "sent_to" => $topic
        ]);
    }
}
