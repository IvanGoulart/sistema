<?php

namespace App\Http\Controllers\services;

use App\Http\Controllers\Controller;

class ServicesController extends Controller
{
    public function index()
    {
        return view('content.services.index');
    }
}
