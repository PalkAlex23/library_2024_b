<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CopyController;
use App\Http\Controllers\LendingController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\Admin;
use App\Http\Middleware\Librarian;
use App\Http\Middleware\Warehouseman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// bárki által elérhető
Route::post('/register',[RegisteredUserController::class, 'store']);
Route::post('/login',[AuthenticatedSessionController::class, 'store']);
//authentikált felhasználó

Route::middleware(['auth:sanctum'])
    ->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        //kölcsönzések száma
        Route::get('/lendings-count-distinct', [LendingController::class, 'lendingsCountDistinct']);

        Route::get('/active-lendings-count', [LendingController::class, 'activeLendingsData']);

        Route::apiResource('/auth-users', UserController::class)->except(['destroy']);

        Route::get('lendings-copies', [LendingController::class, 'lendingsFilterByUser']);

        Route::get('books-copies', [BookController::class, 'booksFilterByCopies']);

        Route::get('user-lendings', [UserController::class, 'userLendingsFilterByUser']);

        Route::patch('update-password/{id}', [UserController::class, 'updatePassword']);

        Route::get('/reserved-books', [ReservationController::class, 'reservedBooks']);

        Route::get('/user-reservation-details', [UserController::class, 'userReservationDetails']);

        Route::get('/reserved-count', [UserController::class, 'reservedCount']);

        Route::patch('/bringback/{copy_id}/{start}', [LendingController::class, 'bringBack']);

        // kijelentkezési útvonal
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
});

Route::middleware(['auth:sanctum',Admin::class])
->group(function () {
    // Route::get('/admin/users', [UserController::class, 'index']);
    Route::apiResource('/admin/users', UserController::class);
});

// librarian réteg
Route::middleware(['auth:sanctum', Librarian::class])
->group(function () {
    // útvonalak
    Route::post('/store-lending', [LendingController::class, 'store']);
});

// warehouseman réteg
Route::middleware(['auth:sanctum', Warehouseman::class])
->group(function () {
    // útvonalak
    Route::get('/warehouseman/copies/{title}', [CopyController::class, 'bookCopyCount']);
});

// összes kérés egy útvonalon
Route::apiResource('/users', UserController::class);