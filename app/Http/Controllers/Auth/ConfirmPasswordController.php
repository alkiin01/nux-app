<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
// use Illuminate\Foundation\Auth\ConfirmsPasswords;
use Illuminate\Http\Request;
use App\Models\User; 


class ConfirmPasswordController extends Controller
{ 
    protected $redirectTo = RouteServiceProvider::HOME;


 
}
