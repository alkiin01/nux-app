<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/ 


Route::POST('/check_robot', [App\Http\Controllers\Auth\LoginController::class, 'check_robot'])->name('check_robot');    
Route::POST('register_account', [App\Http\Controllers\Auth\RegisterController::class, 'register_account'])->name('register_account');    
Route::POST('verification_account', [App\Http\Controllers\Auth\RegisterController::class, 'verification_account'])->name('verification_account');    
Route::get('/reset', [App\Http\Controllers\Auth\RegisterController::class, 'reset_form']) ;    
Route::POST('/confirm_reset', [App\Http\Controllers\Auth\RegisterController::class, 'confirm_reset'])->name('confirm_reset');    
Route::POST('/change_password', [App\Http\Controllers\Auth\RegisterController::class, 'change_password'])->name('change_password');    
Route::get('/confirm_password', [App\Http\Controllers\Auth\RegisterController::class, 'confirm_password']) ;    

 
Auth::routes(); 
Route::group(['middleware' => ['auth']], function () { 
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home'); 
      
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index']);    
}) ; 


 


