@extends('layouts.portal')

@section('title', 'Login - Portal do Cliente')

@section('content')
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-4">

        <div class="card shadow">
          <div class="card-header">
            Login do Portal
          </div>

          <div class="card-body">

            @if($errors->any())
              <div class="alert alert-danger">
                {{ $errors->first() }}
              </div>
            @endif

            <form method="POST" action="{{ route('portal.login.post') }}">
              @csrf

              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control">
              </div>

              <div class="mb-3">
                <label class="form-label">Senha</label>
                <input type="password" name="password" class="form-control">
              </div>

              <button class="btn btn-primary w-100">
                Entrar
              </button>

            </form>

          </div>
        </div>

      </div>
    </div>
  </div>
@endsection
