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
    NCGroupConfigsController,
    AuthController,
    RolesController,
    PermissionsController,
    UsersController,
    NCRequiredConfigController
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


/*
// Resource Controller Methods

GET /users → index method
GET /users/create → create method
POST /users → store method
GET /users/{user} → show method
GET /users/{user}/edit → edit method
PUT /users/{user} → update method
PATCH /users/{user} → update method
DELETE /users/{user} → destroy method*/

//Route::post('/login', [AuthController::class, 'login']);
//Route::post('/register',[AuthController::class,'register']);
Route::post('/forgetpassword', [AuthController::class, 'forgetpassword']);
Route::post('/resetpassword', [AuthController::class, 'resetpassword']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::post('login', [Api\AuthController::class, 'login']);
Route::post('register', [Api\RegisterController::class, 'register']);
Route::post('forgot', [Api\ForgotController::class, 'forgot']);
Route::post('reset', [Api\ForgotController::class, 'reset']);
Route::get('email/resend/{user}', [Api\VerifyController::class, 'resend'])->name('verification.resend');
Route::get('email/verify/{id}', [Api\VerifyController::class, 'verify'])->name('verification.verify');; // Make sure to keep this as your route name

/*Route::group(['middleware' => ['auth:api']], function () {
    Route::get('user', [Api\AuthController::class, 'user']);
});*/

Route::group(['middleware' => 'auth:sanctum'], function () {

    // from another project

    //roles routes
    Route::get('roles', [RolesController::class, 'roles']);
    Route::post('roles/{id}', [RolesController::class, 'getRoleById']);
    Route::post('role/save', [RolesController::class, 'store']);
    Route::delete('roles/delete/{id}', [RolesController::class, 'destroy']);

    //permissions routes
    Route::get('permissions', [PermissionsController::class, 'permissions']);
    Route::post('permissions/{id}', [PermissionsController::class, 'getPermissionById']);
    Route::post('permission/save', [PermissionsController::class, 'store']);
    Route::delete('permissions/delete/{id}', [PermissionsController::class, 'destroy']);

    //logged in user
    Route::post('user', [AuthController::class, 'user']);

    //users routes
    Route::get('users', [UsersController::class, 'users']);
    Route::post('users/{id}', [UsersController::class, 'getUserById']);
    Route::post('usersdata/save', [UsersController::class, 'store']);
    Route::delete('users/delete/{id}', [UsersController::class, 'destroy']);

    Route::get('get-category/{id}', [NCCallCategoryController::class, 'getCategoryByCallTypeId']);

    Route::get('call-sub-by-call-cat-id/{ctid}/{ccid}', [NCCallSubCategoryController::class, 'getCallSubCatByCallAndCategoryId']);

    // for nagad api
    Route::apiResources([
        'groups' => NCGroupController::class,
        'call-categories' => NCCallCategoryController::class,
        'call-types' => NCCallTypeController::class,
        'call-sub-categories' => NCCallSubCategoryController::class,
        'call-sub-sub-categories' => NCCallSubSubCategoryController::class,
        'access-lists' => NCAccessListController::class,
        'group-configs' => NCGroupConfigsController::class,
        'required-fields-configs' => NCRequiredConfigController::class,
    ]);
});
