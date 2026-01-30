<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CurtainController extends Controller
{
    public function index()
    {
        return view('curtain.index');
    }
}
