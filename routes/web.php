<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;

Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
