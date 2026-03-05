<?php
@extends('layouts.app') {{-- ajuste para o seu layout --}}

@section('content')
  <div class="container mt-4">
    <h3 class="mb-3">Agendar atendimento</h3>

    @if (session()->has('message'))
      <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <livewire:form-create-agenda />
  </div>

@endsection
