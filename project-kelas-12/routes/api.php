<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GenreController;
use App\Http\Controllers\Api\AktorController;
use App\Http\Controllers\Api\FilmController;

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);


    Route::get('/genre', [GenreController::class, 'index']);
    Route::post('/genre', [GenreController::class, 'store']);
    Route::put('/genre/{id}', [GenreController::class, 'update']);
    Route::delete('/genre/{id}', [GenreController::class, 'destroy']);

    route::get('/aktor', [AktorController::class, 'index']);
    route::post('/aktor', [AktorController::class, 'store']);
    route::put('/aktor/{id}', [AktorController::class, 'update']);
    route::delete('/aktor/{id}', [AktorController::class, 'destroy']);

    route::get('/film', [FilmController::class, 'index']);
    route::post('/film', [FilmController::class, 'store']);
    route::get('/film/{id}', [FilmController::class, 'show']);
    route::put('/film/{id}', [FilmController::class, 'update']);
    route::delete('/film/{id}', [FilmController::class, 'destroy']);
});
