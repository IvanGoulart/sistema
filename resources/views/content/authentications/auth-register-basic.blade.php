@extends('layouts/blankLayout')

@section('title', 'Register Basic - Pages')

@section('page-style')
<!-- Page -->
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-auth.css')}}">
@endsection


@section('content')
<div class="position-relative">
	<div class="authentication-wrapper authentication-basic container-p-y">
		<div class="authentication-inner py-4">

			<!-- Register Card -->
			<div class="card p-2">
				@if ($errors->any())
				<div class="alert alert-danger">
					<ul>
						@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
				@endif
				@if(session('success'))
				<div class="alert alert-success">
					{{ session('success') }}
				</div>
				@endif

				<!-- Logo -->
				<div class="app-brand justify-content-center mt-5">
					<a href="{{url('/')}}" class="app-brand-link gap-2">
						<span class="app-brand-logo demo">@include('_partials.macros',["height"=>20])</span>
						<span
							class="app-brand-text demo text-heading fw-semibold">{{ config('variables.templateName') }}</span>
					</a>
				</div>
				<!-- /Logo -->
				<div class="card-body mt-2">
					<h4 class="mb-2">{{ isset($user) ? 'Editar Usuário' : 'Criar Usuário' }} 🚀</h4>
					@if (isset($user))
					<form method="POST" action="{{ route('user-update', $user->id) }}">
						@method('PUT')
						@else
						<form method="POST" action="{{ route('user-create') }}">
							@endif
							@csrf
							<div class="form-floating form-floating-outline mb-3">
								<input type="text" class="form-control" id="username" name="username"
									value="{{ old('username', isset($user) ? $user->name : '') }}"
									placeholder="Enter your username" autofocus>
								<label for="username">Nome</label>
							</div>
							<div class="form-floating form-floating-outline mb-3">
								<input type="" class="form-control" id="email" name="email"
									value="{{ old('email', isset($user) ? $user->email : '') }}"
									placeholder="Enter your email">
								<label for="email">Email</label>
							</div>

							<div class="form-floating form-floating-outline mb-4">
								<select class="form-select" id="exampleFormControlSelect1"
									aria-label="Default select example">
									<option selected="">Permissões</option>
									@foreach($permissions as $permission)
									<option value="{{$permission->code}}">{{$permission->name}}</option>
									@endforeach
								</select>
								<label for="exampleFormControlSelect1">Permissão</label>
							</div>

							<div class="mb-3 form-password-toggle">
								<div class="input-group input-group-merge">
									<div class="form-floating form-floating-outline">
										<input type="password" id="password" class="form-control" name="password"
											value=""
											placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
											aria-describedby="password" />
										<label for="password">Senha</label>
									</div>
									<span class="input-group-text cursor-pointer"><i
											class="mdi mdi-eye-off-outline"></i></span>
								</div>
							</div>
							<button class="btn btn-primary d-grid w-100">
								Cadastrar
							</button>
						</form>
						<a href="{{url('/users/list')}}" class="app-brand-link gap-2">
							<span class="app-brand-logo demo">
								<< Voltar</span>
						</a>

				</div>
			</div>
			<!-- Register Card -->
			<img src="{{asset('assets/img/illustrations/tree-3.png')}}" alt="auth-tree"
				class="authentication-image-object-left d-none d-lg-block">
			<img src="{{asset('assets/img/illustrations/auth-basic-mask-light.png')}}"
				class="authentication-image d-none d-lg-block" alt="triangle-bg">
			<img src="{{asset('assets/img/illustrations/tree.png')}}" alt="auth-tree"
				class="authentication-image-object-right d-none d-lg-block">
		</div>
	</div>
</div>
@endsection