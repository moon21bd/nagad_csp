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

Route::middleware(['auth', 'path.permission'])->group(function () {
    Route::get('/dashboard', function (){
        dd('hello');
    });
    // Route::get('/profile', [ProfileController::class, 'show']);
});

Route::get('/{any?}', function () {
    return view('welcome');
})->where('any', '.*');
