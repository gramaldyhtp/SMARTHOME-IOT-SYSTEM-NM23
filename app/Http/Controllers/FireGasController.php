<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FireGasController extends Controller
{
    public function index()
    {
        return view('pages.firegas');
    }
}
