<?php

use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;


Route::post('/login', [UserController::class, 'check_login'])->name('check_login');
Route::get('/login', [UserController::class, 'login'])->name('login');
// Route::get('/login', [HomeController::class, 'index'])->name('login');

Route::get('logout', [UserController::class, 'logout'])->name('user.logout');

Route::get('/active', [UserController::class, 'login'])->name('active');
Route::post('/active', [UserController::class, 'check_active'])->name('check_active_post');
