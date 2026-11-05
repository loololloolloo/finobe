<?php

use App\Http\Controllers\Blog\PublicController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'index'])->name('blog.home');
Route::get('/page/{page}', [PublicController::class, 'index'])->whereNumber('page')->name('blog.page');
Route::get('/rss', [PublicController::class, 'rss'])->name('blog.rss');
Route::get('/tag/{slug}', [PublicController::class, 'tag'])->name('blog.tag');
Route::get('/tag/{slug}/page/{page}', [PublicController::class, 'tag'])->whereNumber('page');
Route::get('/author/{slug}', [PublicController::class, 'author'])->name('blog.author');
Route::get('/author/{slug}/page/{page}', [PublicController::class, 'author'])->whereNumber('page');
Route::get('/{slug}', [PublicController::class, 'show'])->name('blog.post');
