<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['tenant_id', 'name', 'description', 'price'];

    public function employees()
    {
        return $this->belongsToMany(User::class, 'user_services');
    }
}
