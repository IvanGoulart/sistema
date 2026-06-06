<?php

namespace App\Http\Controllers;

use App\Mail\NewLeadMail;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class LandingController extends Controller
{
    public function index()
    {
        return view('landing');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'whatsapp'      => 'required|string|max:20',
            'business_type' => 'required|string|max:100',
        ], [
            'name.required'          => 'Informe seu nome.',
            'whatsapp.required'      => 'Informe seu WhatsApp.',
            'business_type.required' => 'Selecione o tipo de negócio.',
        ]);

        $lead = Lead::create($data);

        Mail::to(config('mail.from.address'))->send(new NewLeadMail($lead));

        return redirect()->back()
            ->with('lead_success', true)
            ->withFragment('interesse');
    }
}
