<?php

namespace App\Models\services;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\services\Services;


class UserService extends Model
{
  use HasFactory;

  // Relacionamento inverso com User
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  // Relacionamento inverso com Service
  public function service()
  {
    return $this->belongsTo(Services::class);
  }
}
