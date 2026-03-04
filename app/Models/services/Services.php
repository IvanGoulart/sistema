<?php

namespace App\Models\services;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Services extends Model
{
  use HasFactory;

  public function users()
  {
    return $this->belongsToMany(
      User::class,
      'user_services',
      'service_id', // coluna da pivot que referencia services
      'user_id'     // coluna da pivot que referencia users
    );
  }
}
