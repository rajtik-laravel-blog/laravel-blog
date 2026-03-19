<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/tags', [TagController::class, 'index'])->name('tags');
Route::get('/posts/{slug}', [PostController::class, 'show'])->name('posts.show');
Route::get('/privacy-policy', fn () => inertia('PrivacyPolicy'))->name('privacy-policy');

// Auth
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Author routes
    Route::prefix('author')->name('author.')->group(function () {
        Route::resource('posts', App\Http\Controllers\Author\PostController::class)->only(['create', 'store', 'edit', 'update']);
        Route::patch('/posts/{post}/toggle-publish', [App\Http\Controllers\Author\PostController::class, 'togglePublish'])->name('posts.toggle-publish');
    });
});

require __DIR__.'/settings.php';
