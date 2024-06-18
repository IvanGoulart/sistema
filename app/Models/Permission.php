<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserPermission;

class Permission extends Model
{
  use HasFactory;

  protected $fillable = [
    'name'
  ];


  /**
   * Define a relationship with the UserPermission model.
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function userPermissions()
  {
    return $this->hasMany(UserPermission::class);
  }
}
