<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum')->name('user.profile');

Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});   

Route::middleware('auth:sanctum')->prefix('posts')->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('posts.index');
    Route::post('/', [PostController::class, 'store'])->name('posts.store');
    Route::get('/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::put('/{post}', [PostController::class, 'update'])->middleware('can:update,post')->name('posts.update');
    Route::delete('/{post}', [PostController::class, 'destroy'])->middleware('can:delete,post')->name('posts.destroy');
    Route::put('/{post}/image', [PostController::class, 'updateImage'])->middleware('can:update,post')->name('posts.image.update');
    Route::delete('/{post}/image', [PostController::class, 'deleteImage'])->middleware('can:update,post')->name('posts.image.delete');

    // Additional routes for post comments
    Route::post('/{post}/comments', [CommentController::class, 'store'])->name('posts.comments.store');
    Route::put('/{post}/comments/{comment}', [CommentController::class, 'update'])->middleware('can:update,comment')->name('posts.comments.update');
    Route::delete('/{post}/comments/{comment}', [CommentController::class, 'destroy'])->middleware('can:delete,comment')->name('posts.comments.destroy');

    
});

