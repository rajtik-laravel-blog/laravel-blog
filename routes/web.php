<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/tags', [TagController::class, 'index'])->name('tags');
Route::get('/posts/{slug}', [PostController::class, 'show'])->name('posts.show');

// Socialite
Route::get('/auth/{provider}/redirect', [\App\Http\Controllers\Auth\SocialiteController::class, 'redirect'])
    ->whereIn('provider', ['github', 'google'])
    ->name('oauth.redirect');
Route::get('/auth/{provider}/callback', [\App\Http\Controllers\Auth\SocialiteController::class, 'callback'])
    ->whereIn('provider', ['github', 'google'])
    ->name('oauth.callback');

// Auth
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/posts/{post}/comments', [\App\Http\Controllers\CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [\App\Http\Controllers\CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [\App\Http\Controllers\CommentController::class, 'destroy'])->name('comments.destroy');
    Route::get('/comments', [\App\Http\Controllers\CommentController::class, 'index'])->name('comments.index');
    Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Author routes
    Route::prefix('author')->name('author.')->group(function () {
        Route::get('/posts', [\App\Http\Controllers\Author\PostController::class, 'index'])->name('posts.index');
        Route::get('/posts/create', [\App\Http\Controllers\Author\PostController::class, 'create'])->name('posts.create');
        Route::post('/posts', [\App\Http\Controllers\Author\PostController::class, 'store'])->name('posts.store');
        Route::get('/posts/{post}/edit', [\App\Http\Controllers\Author\PostController::class, 'edit'])->name('posts.edit');
        Route::post('/posts/{post}', [\App\Http\Controllers\Author\PostController::class, 'update'])->name('posts.update');
    });
});

require __DIR__.'/settings.php';
