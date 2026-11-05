<?php
use App\Http\Controllers\Blog\AdminController;
use Illuminate\Support\Facades\Route;
Route::get('/',[AdminController::class,'index'])->name('admin.blog');
Route::get('/posts/new',[AdminController::class,'edit'])->name('admin.blog.new');
Route::post('/posts',[AdminController::class,'store'])->name('admin.blog.store');
Route::get('/posts/{id}/edit',[AdminController::class,'edit'])->whereNumber('id');
Route::put('/posts/{id}',[AdminController::class,'update'])->whereNumber('id');
Route::delete('/posts/{id}',[AdminController::class,'delete'])->whereNumber('id');
Route::get('/authors',[AdminController::class,'authors']);
Route::post('/authors',[AdminController::class,'authorSave']);
Route::delete('/authors/{id}',[AdminController::class,'authorDelete'])->whereNumber('id');
Route::get('/tags',[AdminController::class,'tags']);
Route::post('/tags',[AdminController::class,'tagSave']);
Route::delete('/tags/{id}',[AdminController::class,'tagDelete'])->whereNumber('id');
Route::get('/settings',[AdminController::class,'settings']);
Route::post('/settings',[AdminController::class,'settingsSave']);
