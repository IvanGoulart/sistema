<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class RegisterBasic extends Controller
{
  public function index()
  {

    return view('content.authentications.auth-register-basic', ['permissions' => Permission::all()]);
  }
}
