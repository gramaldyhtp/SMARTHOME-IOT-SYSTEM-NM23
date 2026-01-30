<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApplianceController extends Controller
{
    public function index()
    {
        return view('appliance.index');
    }
}
