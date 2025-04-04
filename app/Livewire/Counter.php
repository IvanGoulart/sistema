<?php

namespace App\Livewire;

use Livewire\Component;

class Counter extends Component

{

  public $count = 0;
  public $selectedCategory = 0;

  public function increment()
  {
    $this->count++;
    //$this->updated($this->teste);
  }

  // Hook chamado após a atualização da propriedade 'count'
  public function updated($value)
  {
    dd($this->selectedCategory);
  }

  public function render()

  {

    return view('livewire.counter');
  }
}
