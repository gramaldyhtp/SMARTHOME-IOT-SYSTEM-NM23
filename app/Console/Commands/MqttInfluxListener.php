<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use InfluxDB2\Client as InfluxClient;

class MqttInfluxListener extends Command
{
    /**
     * Nama artisan command
     */
    protected $signature = 'mqtt:influx';

    /**
     * Deskripsi command
     */
    protected $description = 'Listen to MQTT topics from IoT devices and store data into InfluxDB';

    /**
     * Handler
     */
    public function handle()
    {
        $server   = 'localhost';
        $port     = 1883;
        $clientId = 'laravel_gateway';

        // MQTT Connection Settings
        $connectionSettings = (new ConnectionSettings)
            ->setKeepAliveInterval(60);

        $mqtt = new MqttClient($server, $port, $clientId);

        $this->info("?? Connecting to MQTT Broker...");

        $mqtt->connect($connectionSettings, true);

        $this->info("? Connected. Listening on topic: home/+/data");

        // Subscribe
        $mqtt->subscribe('home/+/data', function (string $topic, string $message) {

            $this->info("?? Received [$topic] : $message");

            $data = json_decode($message, true);

            // Connect to InfluxDB
            $influx = new InfluxClient([
                'url'   => env('INFLUX_URL'),
                'token' => env('INFLUX_TOKEN'),
                'bucket'=> env('INFLUX_BUCKET'),
                'org'   => env('INFLUX_ORG'),
            ]);

            $writeApi = $influx->createWriteApi();

            $writeApi->write([
                'name' => 'iot_data',
                'tags' => [
                    'device' => $data['device'] ?? 'unknown',
                ],
                'fields' => [
                    'value'  => $data['value']  ?? 0,
                    'status' => $data['status'] ?? 'unknown',
                ],
                'time' => now()->toIso8601String(),
            ]);

            $this->info("? Data written to InfluxDB");

        }, 0);

        // Loop forever
        $mqtt->loop(true);

        return 0;
    }
}

