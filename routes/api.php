<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;
use App\Http\Controllers\{
    NCGroupController,
    NCCallCategoryController,
    NCCallTypeController,
    NCCallSubCategoryController,
    NCCallSubSubCategoryController,
    NCAccessListController,
    NCGroupConfigsController
};

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

Route::post('login', [Api\AuthController::class, 'login']);
Route::post('register', [Api\RegisterController::class, 'register']);
Route::post('forgot', [Api\ForgotController::class, 'forgot']);
Route::post('reset', [Api\ForgotController::class, 'reset']);
Route::get('email/resend/{user}', [Api\VerifyController::class, 'resend'])->name('verification.resend');
Route::get('email/verify/{id}', [Api\VerifyController::class, 'verify'])->name('verification.verify');; // Make sure to keep this as your route name

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('user', [Api\AuthController::class, 'user']);
});
Route::middleware('auth:api')->get('/user/permissions', [Api\UserPermissionsController::class, 'index']);

Route::group(['middleware' => 'auth:api'], function () {
    Route::apiResources([
        'groups' => NCGroupController::class,
        'call-categories' => NCCallCategoryController::class,
        'call-types' => NCCallTypeController::class,
        'call-sub-categories' => NCCallSubCategoryController::class,
        'call-sub-sub-categories' => NCCallSubSubCategoryController::class,
        'access-lists' => NCAccessListController::class,
        'group-configs' => NCGroupConfigsController::class,
    ]);
});
