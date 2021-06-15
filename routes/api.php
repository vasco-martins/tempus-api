<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Builder\CheckIfModelNameExistsController;
use App\Http\Controllers\Builder\ShowFieldsListController;
use App\Http\Controllers\Builder\ShowParentMenusController;
use App\Http\Controllers\Builder\ShowProjectModelFieldList;
use App\Http\Controllers\Builder\ShowProjectModelList;
use App\Http\Controllers\Builder\ShowProjectModelNamesController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ParentMenuController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectModelController;
use App\Http\Controllers\UserController;
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

    Route::post('forgot-password', [ForgotPasswordController::class, 'forgotPassword'])->name('auth.forgotPassword');
    Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('auth.resetPassword');


    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('auth.logout');
    Route::get('currentUser', [AuthController::class, 'currentUser'])->middleware('auth:api')->name('auth.currentUser');

});

Route::get('projects/{hash}/download', [ProjectController::class, 'download'])->name('projects.download');


Route::get('builder/fields', ShowFieldsListController::class)->name('builder.fields');
Route::post('builder/projectModelNames', ShowProjectModelNamesController::class)->middleware('auth:api')->name('builder.checkModel');
Route::post('builder/parentMenuNames', ShowParentMenusController::class)->middleware('auth:api')->name('builder.parentMenuNames');
Route::get('builder/{project}/projectModelList', ShowProjectModelList::class)->middleware('auth:api')->name('builder.projectModelList');
Route::get('builder/{project}/projectModelFieldList', ShowProjectModelFieldList::class)->middleware('auth:api')->name('builder.projectModelFieldList');


Route::middleware('auth:api')->group(function () {
    Route::resource('projects', ProjectController::class);
    Route::resource('project-models', ProjectModelController::class);

    Route::post('projects/{project}/parent-menus', [ParentMenuController::class, 'store']);
    Route::patch('projects/{project}/parent-menus/{projectModel}', [ParentMenuController::class, 'update']);
    Route::get('projects/{project}/menu', [ProjectController::class, 'showMenu']);
    Route::post('projects/{project}/menu/reorder', [MenuController::class, 'reorder']);

    Route::get('projects/{project}/deploy', [ProjectController::class, 'deploy']);
    Route::get('projects/{project}/deployStatus', [ProjectController::class, 'showDeployStatus']);

    Route::patch('users/{user}',[UserController::class, 'update'])->name('users.update');
});
