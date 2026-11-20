<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TaskController::class, 'today'])->name('today');
Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/dashboard', fn () => redirect()->route('today'))->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('tasks', TaskController::class)->except(['index', 'show']);
    Route::patch('/tasks/{task}/status', [TaskController::class, 'status'])->name('tasks.status');
});

require __DIR__.'/auth.php';
