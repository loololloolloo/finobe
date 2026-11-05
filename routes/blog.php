<?php
use App\Http\Controllers\Blog\PublicController;
use Illuminate\Support\Facades\Route;
Route::get('/',[PublicController::class,'index'])->name('blog.home');
Route::get('/tag/{slug}',[PublicController::class,'tag'])->name('blog.tag');
Route::get('/author/{slug}',[PublicController::class,'author'])->name('blog.author');
Route::get('/{slug}',[PublicController::class,'show'])->name('blog.post');
