<div class="container mt-4">

  <div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0">Cadastrar Tenant</h5>
    </div>

    <div class="card-body">

      @if (session()->has('success'))
        <div class="alert alert-success">
          {{ session('success') }}
        </div>
      @endif

      <div class="mb-3">
        <label for="name" class="form-label fw-bold">Nome da empresa</label>
        <input
          type="text"
          id="name"
          class="form-control"
          wire:model.live="name"
          placeholder="Digite o nome da empresa"
        >
        @error('name')
        <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label for="slug" class="form-label fw-bold">Slug</label>
        <input
          type="text"
          id="slug"
          class="form-control"
          wire:model.live="slug"
          placeholder="ex: clinica-vida"
        >
        @error('slug')
        <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label for="email" class="form-label fw-bold">E-mail</label>
        <input
          type="email"
          id="email"
          class="form-control"
          wire:model.live="email"
          placeholder="contato@empresa.com"
        >
        @error('email')
        <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label for="phone" class="form-label fw-bold">Telefone</label>
        <input
          type="text"
          id="phone"
          class="form-control"
          wire:model.live="phone"
          placeholder="(00) 00000-0000"
        >
        @error('phone')
        <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-check form-switch mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          id="is_active"
          wire:model.live="is_active"
        >
        <label class="form-check-label fw-bold" for="is_active">
          Tenant ativo
        </label>
      </div>

      <div class="d-grid">
        <button
          type="button"
          class="btn btn-primary btn-lg"
          wire:click="save"
        >
          Salvar Tenant
        </button>
      </div>

    </div>
  </div>

  <div class="card shadow-sm mt-4">
    <div class="card-header bg-light">
      <h6 class="mb-0">Tenants cadastrados</h6>
    </div>

    <div class="card-body">

      @if($tenants->isEmpty())
        <div class="alert alert-warning mb-0">Nenhum tenant cadastrado.</div>
      @else
        <div class="table-responsive">
          <table class="table table-striped align-middle mb-0">
            <thead>
            <tr>
              <th>Nome</th>
              <th>Slug</th>
              <th>E-mail</th>
              <th>Telefone</th>
              <th>Status</th>
            </tr>
            </thead>
            <tbody>
            @foreach($tenants as $tenant)
              <tr>
                <td>{{ $tenant->name }}</td>
                <td>{{ $tenant->slug }}</td>
                <td>{{ $tenant->email ?? '-' }}</td>
                <td>{{ $tenant->phone ?? '-' }}</td>
                <td>
                  @if($tenant->is_active)
                    <span class="badge bg-success">Ativo</span>
                  @else
                    <span class="badge bg-secondary">Inativo</span>
                  @endif
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      @endif

    </div>
  </div>

</div>
