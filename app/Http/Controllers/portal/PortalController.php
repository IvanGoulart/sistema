<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;

class PortalController extends Controller
{
    public function home()
    {
        return redirect()->route('portal.agendar');
    }

    public function agendar()
    {
        return view('portal.agendar');
    }
}
