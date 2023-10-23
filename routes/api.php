<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BlogController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\SubscriptionController;
use App\Http\Controllers\API\Admin\SubscriptionController as AdminSubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login')->name('login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::group(['prefix' => 'posts', 'as' => 'posts.'], function () {
        Route::get('/', [PostController::class, 'index'])->name('index');
        Route::post('/', [PostController::class, 'store'])->name('store');
        Route::get('{post}', [PostController::class, 'show'])->name('show');
        Route::put('{post}', [PostController::class, 'update'])->name('update')
            ->middleware('can:update,post');
        Route::put('{post}/publish', [PostController::class, 'publish'])->name('publish')
            ->middleware('can:update,post');
        Route::put('{post}/un-publish', [PostController::class, 'unPublish'])->name('unPublish')
            ->middleware('can:update,post');
        Route::delete('{post}', [PostController::class, 'destroy'])->name('delete')
            ->middleware('can:delete,post');
    });

    Route::group(['prefix' => 'subscriptions', 'as' => 'subscriptions.'], function () {
        Route::get('/', [SubscriptionController::class, 'index'])->name('index');
        Route::get('/active', [SubscriptionController::class, 'show']);
        Route::get('/list', [SubscriptionController::class, 'list']);
        Route::post('/{subscription}', [SubscriptionController::class, 'choose']);
//        Route::delete('/subscriptions/{subscription}', 'SubscriptionController@cancel');
    });

    Route::group(['middleware' => 'checkAdmin', 'prefix' => 'admin', 'as' => 'admin.'], function () {
        Route::get('/subscriptions', [AdminSubscriptionController::class, 'index']);
        Route::post('/subscriptions', [AdminSubscriptionController::class, 'store']);
        Route::put('/subscriptions/{subscription}', [AdminSubscriptionController::class, 'update']);
        Route::delete('/subscriptions/{subscription}', [AdminSubscriptionController::class, 'destroy']);
    });
});

Route::post('/payment', [PaymentController::class, 'processPayment']);

Route::get('/blog', [BlogController::class, 'index']);
Route::get('/blog/{post}', [BlogController::class, 'show']);
