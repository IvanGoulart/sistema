@extends('layouts.portal')

@section('content')
  <div class="container mt-4">

    <h3 class="mb-3">Meus Agendamentos</h3>

    <table class="table table-striped">
      <thead>
      <tr>
        <th>Serviço</th>
        <th>Profissional</th>
        <th>Data</th>
        <th>Hora</th>
        <th>Ação</th>
      </tr>
      </thead>
      <tbody>

      @foreach($schedules as $schedule)

        <tr>
          <td>{{ $schedule->service->name }}</td>
          <td>{{ $schedule->employee->name }}</td>
          <td>{{ $schedule->day }}</td>
          <td>{{ $schedule->hour }}</td>

          <td>
            <button
              class="btn btn-danger btn-sm"
              wire:click="cancelSchedule({{ $schedule->id }})"
            >
              Cancelar
            </button>
          </td>

        </tr>

      @endforeach

      </tbody>
    </table>

  </div>
@endsection
