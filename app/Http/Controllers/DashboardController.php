<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Nanti ambil data InfluxDB / MQTT disini
        return view('dashboard.index');
    }
}
