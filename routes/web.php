<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [PostController::class, 'index'])->name('home');

Route::get('/tags', [TagController::class, 'index'])->name('tags');

Route::get('/posts/create', [PostController::class, 'create'])->middleware('auth')->name('posts.create');
Route::post('/posts', [PostController::class, 'store'])->middleware('auth')->name('posts.store');
Route::get('/posts/{slug}', [PostController::class, 'show'])->name('posts.show');

Route::post('/posts/{post}/comments', [\App\Http\Controllers\CommentController::class, 'store'])
    ->middleware('auth')
    ->name('comments.store');

Route::put('/comments/{comment}', [\App\Http\Controllers\CommentController::class, 'update'])
    ->middleware('auth')
    ->name('comments.update');

Route::delete('/comments/{comment}', [\App\Http\Controllers\CommentController::class, 'destroy'])
    ->middleware('auth')
    ->name('comments.destroy');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard', [
        'comments' => auth()->user()
            ->comments()
            ->with('post')
            ->latest()
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

// Socialite
Route::get('/auth/{provider}/redirect', [\App\Http\Controllers\Auth\SocialiteController::class, 'redirect'])
    ->whereIn('provider', ['github', 'google'])
    ->name('oauth.redirect');

Route::get('/auth/{provider}/callback', [\App\Http\Controllers\Auth\SocialiteController::class, 'callback'])
    ->whereIn('provider', ['github', 'google'])
    ->name('oauth.callback');

require __DIR__.'/settings.php';
