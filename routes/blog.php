<?php

use App\Http\Controllers\Blog\PublicController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'index'])->name('blog.home');
Route::get('/page/{page}', [PublicController::class, 'index'])->whereNumber('page')->name('blog.page');
Route::get('/rss', [PublicController::class, 'rss'])->name('blog.rss');
Route::get('/search', [PublicController::class, 'search'])->name('blog.search');
Route::get('/search/page/{page}', [PublicController::class, 'search'])->whereNumber('page');
Route::get('/tag/{slug}', [PublicController::class, 'tag'])->name('blog.tag');
Route::get('/tag/{slug}/page/{page}', [PublicController::class, 'tag'])->whereNumber('page');
Route::get('/author/{slug}', [PublicController::class, 'author'])->name('blog.author');
Route::get('/author/{slug}/page/{page}', [PublicController::class, 'author'])->whereNumber('page');
Route::get('/{year}/{month}/{day}/{slug}', [PublicController::class, 'showDated'])
    ->where(['year' => '[0-9]{4}', 'month' => '[0-9]{2}', 'day' => '[0-9]{2}'])
    ->name('blog.post.dated');
Route::get('/{slug}', [PublicController::class, 'show'])->name('blog.post');
