<div>
  <h1>Contador: {{ $count }}</h1>
  <button wire:click="increment">Incrementar</button>

  @if (session()->has('message'))
  <div style="color: green;">{{ session('message') }}</div>
  @endif

  <div>
    <label for="category">Selecione uma categoria:</label>
    <select id="category" wire:model="selectedCategory" wire:change="updatedSelectedCategory($event.target.value)">
      <option value="">Todas as Categorias</option>
      <option value="teste1">teste 1</option>
      <option value="teste2">teste 2</option>
    </select>
  </div>

</div>