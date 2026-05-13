<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';

Route::livewire('/vendors', 'vendors.index')->middleware(['auth', 'role:admin']);
Route::livewire('/vendor-users', 'vendor-users.index')->middleware(['auth', 'role:admin']);
Route::livewire('/contacts', 'contacts.index')->middleware(['auth']);