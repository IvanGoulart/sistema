@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/dashboards-analytics.js')}}"></script>
@endsection

@php
$active = [
0 => ['status' => 'Inativo', 'cor' => 'danger'],
1 => ['status' => 'Ativo', 'cor' => 'success'],
2 => ['status' => 'Pendente', 'cor' => 'warning']
];
@endphp
@section('content')
<div class="row gy-4">

	<!-- Congratulations card -->
	<div class="col-md-12 col-lg-4">
		<div class="card">
			<div class="card-body">
				<h4 class="card-title mb-1">Total de Clientes 🎉</h4>
				<h4 class="text-primary mb-1">{{ count($users) }}</h4>
			</div>
			<img src="{{asset('assets/img/icons/misc/triangle-light.png')}}"
				class="scaleX-n1-rtl position-absolute bottom-0 end-0" width="166" alt="triangle background">
			<img src="{{asset('assets/img/illustrations/trophy.png')}}"
				class="scaleX-n1-rtl position-absolute bottom-0 end-0 me-4 mb-4 pb-2" width="40" alt="view sales">
		</div>
	</div>
	<!--/ Congratulations card -->

	<!-- Transactions -->

	<!--
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header">
        <div class="d-flex align-items-center justify-content-between">
          <h5 class="card-title m-0 me-2">Transactions</h5>
          <div class="dropdown">
            <button class="btn p-0" type="button" id="transactionID" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="mdi mdi-dots-vertical mdi-24px"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="transactionID">
              <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
              <a class="dropdown-item" href="javascript:void(0);">Share</a>
              <a class="dropdown-item" href="javascript:void(0);">Update</a>
            </div>
          </div>
        </div>
        <p class="mt-3"><span class="fw-medium">Total 48.5% growth</span> 😎 this month</p>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3 col-6">
            <div class="d-flex align-items-center">
              <div class="avatar">
                <div class="avatar-initial bg-primary rounded shadow">
                  <i class="mdi mdi-trending-up mdi-24px"></i>
                </div>
              </div>
              <div class="ms-3">
                <div class="small mb-1">Sales</div>
                <h5 class="mb-0">245k</h5>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-6">
            <div class="d-flex align-items-center">
              <div class="avatar">
                <div class="avatar-initial bg-success rounded shadow">
                  <i class="mdi mdi-account-outline mdi-24px"></i>
                </div>
              </div>
              <div class="ms-3">
                <div class="small mb-1">Customers</div>
                <h5 class="mb-0">12.5k</h5>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-6">
            <div class="d-flex align-items-center">
              <div class="avatar">
                <div class="avatar-initial bg-warning rounded shadow">
                  <i class="mdi mdi-cellphone-link mdi-24px"></i>
                </div>
              </div>
              <div class="ms-3">
                <div class="small mb-1">Product</div>
                <h5 class="mb-0">1.54k</h5>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-6">
            <div class="d-flex align-items-center">
              <div class="avatar">
                <div class="avatar-initial bg-info rounded shadow">
                  <i class="mdi mdi-currency-usd mdi-24px"></i>
                </div>
              </div>
              <div class="ms-3">
                <div class="small mb-1">Revenue</div>
                <h5 class="mb-0">$88k</h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  -->
	<!--/ Transactions -->

	<!-- Data Tables -->
	<div class="col-12">
		<div class="card">
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

			<div class="table-responsive">
				<table class="table">
					<thead class="table-light">
						<tr>
							<th class="text-truncate">Usuario</th>
							<th class="text-truncate">Email</th>
							<th class="text-truncate">Permissão</th>
							<th class="text-truncate">Status</th>
							<th class="text-truncate">Ação</th>
						</tr>
					</thead>
					<tbody>

          @if (isset($users) && count($users) > 0)
            @foreach ($users as $user)
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-3">
                      <img src="{{asset('assets/img/avatars/1.png')}}" alt="Avatar" class="rounded-circle">
                    </div>
                    <div>
                      <h6 class="mb-0 text-truncate">{{ $user->name }}</h6>
                      <small class="text-truncate">@amiccoo</small>
                    </div>
                  </div>
                </td>
                <td class="text-truncate">{{ $user->email }}</td>
                <td class="text-truncate"><i class="mdi mdi-laptop mdi-24px text-danger me-1"></i>
                  {{ $user->userPermission?->permission?->name ?? 'Permission name does not exist.' }}
                </td>
                <td><span class="badge bg-label-{{$active[$user->active]['cor']}} rounded-pill">{{$active[$user->active]['status']}}</span>
                </td>
                <td>
                  <div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical"></i></button>
                    <div class="dropdown-menu" style="">
                      <a class="dropdown-item waves-effect" href="{{ route('user-edit', ['id' => $user->id]) }}"><i class="mdi mdi-pencil-outline me-1"></i> Editar</a>
                      <a class="dropdown-item waves-effect" href="{{ route('user-delete', ['id' => $user->id]) }}"><i class="mdi mdi-account-alert-outline"></i> Inativar</a>
                      <a class="dropdown-item waves-effect" href="{{ route('user-active', ['id' => $user->id]) }}"><i class="mdi mdi-account-outline"></i> Ativar</a>
                    </div>
                  </div>
                </td>
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="5" class="text-center">Nenhum usuário encontrado.</td>
            </tr>
          @endif
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<!--/ Data Tables -->
</div>
@endsection