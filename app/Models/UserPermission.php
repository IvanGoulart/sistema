<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Permission;

class UserPermission extends Model
{
  use HasFactory;
  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'code_permission',
    'user_id',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }
  /**
   * Define a relationship with the Permission model.
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function permission()
  {
    return $this->belongsTo(Permission::class, 'code_permission', 'id');
  }
}
