<?php

use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\Auth\Logout;
use App\Http\Controllers\Auth\Register;
use App\Http\Controllers\HooterController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HooterController::class, 'index']);

Route::middleware('auth') -> group(function(){
    Route::post('/hoots', [HooterController::class, 'store']);
    Route::get('/hoots/{hoot}/edit', [HooterController::class, 'edit']); //{} this is called route model binding
    Route::put('/hoots/{hoot}', [HooterController::class, 'update']);
    Route::delete('/hoots/{hoot}', [HooterController::class, 'destroy']);
});

//REGISTER ROUTES
Route::view('/register','auth.register')
->middleware('guest')
->name('register');

Route::post('/register', Register::class)
->middleware('guest');

//LOGOUT
Route::post('/logout', Logout::class)
    ->middleware('auth')
    ->name('logout');

//We can also do this alternatively
//Route::resource('/hoots', HooterController::class)
//->only(['store','edit','update','destroy']);

//LOGIN
Route::view('/login','auth.login')
    ->middleware('guest')
    ->name('login');

Route::post('/login', Login::class)
->middleware('guest');
