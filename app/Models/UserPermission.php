<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Permission;

class UserPermission extends Model
{
  use HasFactory;

  protected $table = 'user_permissions';

  protected $fillable = ['tenant_id', 'user_id', 'code_permission'];

  public function user()
  {
    return $this->belongsTo(User::class, 'user_id', 'id');
  }

  public function permission()
  {
    return $this->belongsTo(Permission::class, 'code_permission', 'id');
  }
}
