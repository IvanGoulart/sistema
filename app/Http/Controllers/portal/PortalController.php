<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

    public function meusAgendamentos()
    {
        $clientId = auth()->id();
        $today    = Carbon::today();

        $base = DB::table('schedules')
            ->join('users as emp', 'schedules.employee_id', '=', 'emp.id')
            ->join('services', 'schedules.service_id', '=', 'services.id')
            ->where('schedules.client_id', $clientId)
            ->select(
                'schedules.id',
                'schedules.day',
                'schedules.hour',
                'schedules.cancel',
                'services.name as service_name',
                'emp.name as employee_name'
            );

        $upcoming = (clone $base)
            ->where('schedules.cancel', false)
            ->whereDate('schedules.day', '>=', $today)
            ->orderBy('schedules.day')
            ->orderBy('schedules.hour')
            ->get();

        $past = (clone $base)
            ->where(function ($q) use ($today) {
                $q->whereDate('schedules.day', '<', $today)
                  ->orWhere('schedules.cancel', true);
            })
            ->orderByDesc('schedules.day')
            ->orderByDesc('schedules.hour')
            ->get();

        return view('portal.meus-agendamentos', compact('upcoming', 'past'));
    }

    public function cancelar($id)
    {
        DB::table('schedules')
            ->where('id', $id)
            ->where('client_id', auth()->id())
            ->update(['cancel' => true]);

        return redirect()->route('portal.meus-agendamentos')
            ->with('message', 'Agendamento cancelado com sucesso.');
    }
}
