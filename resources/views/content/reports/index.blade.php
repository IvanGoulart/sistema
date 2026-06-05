@extends('layouts/contentNavbarLayout')

@section('title', 'Relatório de Agendamentos')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1">Relatório de Agendamentos</h4>
        <p class="text-muted mb-0">Analise os agendamentos por período, profissional e serviço</p>
    </div>
</div>

<livewire:reports.agenda-report />

@endsection
