<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use InfluxDB2\Client;

class InfluxServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('influxdb', function () {
            return new Client([
                "url" => env('INFLUX_URL'),
                "token" => env('INFLUX_TOKEN'),
                "bucket" => env('INFLUX_BUCKET'),
                "org" => env('INFLUX_ORG'),
                "precision" => \InfluxDB2\Model\WritePrecision::S,
            ]);
        });
    }
}

