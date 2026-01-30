<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LightingController extends Controller
{
    public function index()
    {
        // Data kelompok 1 disini nanti
        return view('lighting.index');
    }
}
