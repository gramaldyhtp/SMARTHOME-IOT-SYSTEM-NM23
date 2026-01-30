<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GardenController extends Controller
{
    public function index()
    {
        return view('garden.index');
    }
}
