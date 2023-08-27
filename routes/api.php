<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\PatronController;
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
Route::prefix('v1')->group(function () {
    Route::post('login', [AuthorController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        //Authors Routes
        Route::post('create-author', [AuthorController::class, 'createAuthor']);
        Route::put('update-author/{id}', [AuthorController::class, 'updateAuthor']);
        Route::get('get-authors', [AuthorController::class, 'getAuthors']);
        Route::get('get-author-books/{id}', [AuthorController::class, 'authorBooks']);
        Route::get('get-single-author/{id}', [AuthorController::class, 'getSingleAuthor']);
        Route::delete('delete-author/{id}', [AuthorController::class, 'destroy']);
        // Book Routes
        Route::post('store-book', [BookController::class, 'storeBook']);
        Route::post('search-book', [BookController::class, 'searchBook']);
        Route::put('update-book/{id}', [BookController::class, 'updateBook']);
        Route::get('get-books', [BookController::class, 'getBooks']);
        Route::get('get-single-book/{id}', [BookController::class, 'getSinglebook']);
        Route::get('get-book-authors/{id}', [BookController::class, 'bookAuthors']);
        Route::delete('delete-book/{id}', [BookController::class, 'destroy']);
        //Patron Routes
        Route::post('create-patron', [PatronController::class, 'storePatron']);
        Route::put('update-patron/{id}', [PatronController::class, 'updatePatron']);
        Route::get('get-patrons', [PatronController::class, 'getPatrons']);
        Route::delete('delete-patron/{id}', [PatronController::class, 'destroy']);
        Route::post('patrons-borrow/{patronId}/{bookId}', [PatronController::class, 'borrowBook']);
        Route::post('patrons-return/{patronId}/{bookId}', [PatronController::class, 'returnBook']);


    });
});


