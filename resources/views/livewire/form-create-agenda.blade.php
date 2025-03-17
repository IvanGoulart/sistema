<div class="container">
	<div class="mb-3">
		<label for="service" class="form-label">Selecione um serviço:</label>
		<select id="service" class="form-select" wire:model="selectedService" wire:change="updateUsers">
			<option value="">Selecione um serviço</option>
			@foreach($services as $service)
			<option value="{{ $service->id }}">{{ $service->nome }}</option>
			@endforeach
		</select>
	</div>
	<div class="mb-3">
		<label for="user" class="form-label">Selecione um profissional:</label>
		<select id="user" class="form-select" wire:model="selectedUser">
			@if($users && count($users) > 0)
			<option value="">Selecione</option>
			@foreach($users as $user)
			<option value="{{ $user->id }}">{{ $user->name }}</option>
			@endforeach
			@else
			<option value="">Nenhum usuário relacionado ao serviço selecionado.</option>
			@endif
		</select>
	</div>
	<div class="mb-3">
		<button type="button" class="btn btn-primary" wire:click="save">Salvar</button>
	</div>
</div>
