<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarsController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('utilisateurs/inscription', [
    UserController::class,
    'inscription',
])->name('inscription');
Route::post('utilisateurs/connexion', [
    UserController::class,
    'connexion',
])->name('connexion');

Route::get('/cars', [CarsController::class, 'index']);
Route::get('/cars/{id}', [CarsController::class, 'show']);

Route::group([
    'middleware' => ['auth:sanctum']],
    function () {
        Route::post('/cars/store', [CarsController::class, 'store']);
        Route::put('/cars/{id}', [CarsController::class, 'update']);
        Route::delete('/cars/{id}', [CarsController::class, 'destroy']);
    });


