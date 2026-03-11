@extends('layouts.portal')

@section('title', 'Cadastro - Portal do Cliente')

@section('content')
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-4">

        <div class="card shadow">
          <div class="card-header">
            Criar Conta
          </div>

          <div class="card-body">

            <form method="POST" action="{{ route('portal.register') }}">
              @csrf

              <div class="mb-3">
                <label>Nome</label>
                <input type="text" name="name" class="form-control">
              </div>

              <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control">
              </div>

              <div class="mb-3">
                <label>Senha</label>
                <input type="password" name="password" class="form-control">
              </div>

              <div class="mb-3">
                <label>Confirmar Senha</label>
                <input type="password" name="password_confirmation" class="form-control">
              </div>

              <button class="btn btn-success w-100">
                Criar Conta
              </button>

            </form>

          </div>
        </div>

      </div>
    </div>
  </div>
@endsection
