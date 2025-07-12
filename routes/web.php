<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');
Route::get('/dashboard', fn() => inertia('(panel)/dashboard/page'));
Route::get('/categories', fn() => inertia('(panel)/categories/page'));
Route::get('/products', fn() => inertia('(panel)/products/page'));
Route::get('/board', fn() => inertia('(panel)/board/page'));

Route::get('/login', fn() => inertia('(auth)/login/page'));
Route::get('/register', fn() => inertia('(auth)/register/page'));
