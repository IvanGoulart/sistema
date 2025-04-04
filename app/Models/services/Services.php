<?php

namespace App\Models\services;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Services extends Model
{
  use HasFactory;

  // Relacionamento com User (via UserService)
  public function users()
  {
    return $this->belongsToMany(User::class, 'user_services');
  }
}
