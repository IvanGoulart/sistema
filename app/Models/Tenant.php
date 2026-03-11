<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{

  protected $fillable = ['name', 'slug', 'email', 'phone', 'is_active'];
  public function users()
  {
    return $this->belongsToMany(User::class)
      ->withPivot(['role', 'is_active'])
      ->withTimestamps();
  }
}

