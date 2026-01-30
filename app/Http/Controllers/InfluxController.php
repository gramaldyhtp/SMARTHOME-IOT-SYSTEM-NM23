<?php

namespace App\Http\Controllers;

use InfluxDB2\Client;

class InfluxController extends Controller
{
    public function getDeviceData($device)
    {
        $client = app('influxdb');
        $queryApi = $client->createQueryApi();

        $query = '
            from(bucket: "iot_data")
                |> range(start: -6h)
                |> filter(fn: (r) => r.device == "' . $device . '")
        ';

        $tables = $queryApi->query($query);

        $result = [];

        foreach ($tables as $table) {
            foreach ($table->records as $record) {
                $result[] = [
                    'time' => $record->getTime(),
                    'value' => $record->getValue(),
                    'measurement' => $record->getMeasurement(),
                    'device' => $record->values["device"]
                ];
            }
        }

        return response()->json($result);
    }
}
