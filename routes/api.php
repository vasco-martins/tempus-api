<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Builder\ShowFieldsListController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectModelController;
use Illuminate\Support\Facades\Route;

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

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('register/validate', [AuthController::class, 'validateRegister'])->name('auth.validateRegister');

    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('auth.logout');
    Route::get('currentUser', [AuthController::class, 'currentUser'])->middleware('auth:api')->name('auth.currentUser');

});

Route::get('builder/fields', ShowFieldsListController::class)->name('builder.fields');

Route::middleware('auth:api')->group(function () {
    Route::resource('projects', ProjectController::class);
    Route::resource('project-models', ProjectModelController::class);
});
