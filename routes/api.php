<?php

use App\Http\Controllers\Api;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupRoleController;
use App\Http\Controllers\NCCallCategoryController;
use App\Http\Controllers\NCCallSubCategoryController;
use App\Http\Controllers\NCCallSubSubCategoryController;
use App\Http\Controllers\NCCallTypeController;
use App\Http\Controllers\NCRequiredConfigController;
use App\Http\Controllers\NCServiceTypeConfigController;
use App\Http\Controllers\NCTicketController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UsersController;
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
// Route::post('register', [Api\RegisterController::class, 'register']);
Route::post('forgot', [Api\ForgotController::class, 'forgot']);
Route::post('reset', [Api\ForgotController::class, 'reset']);
Route::get('email/resend/{user}', [Api\VerifyController::class, 'resend'])->name('verification.resend');
Route::get('email/verify/{id}', [Api\VerifyController::class, 'verify'])->name('verification.verify'); // Make sure to keep this as your route name

/*Route::group(['middleware' => ['auth:api']], function () {
Route::get('user', [Api\AuthController::class, 'user']);
});*/

Route::group(['middleware' => 'auth:sanctum'], function () {

    // from another project

    //roles routes
    Route::get('roles', [RolesController::class, 'roles']);
    Route::get('role/{id}', [RolesController::class, 'getRoleById']);
    Route::post('role/save', [RolesController::class, 'store']);
    Route::put('role/update/{id}', [RolesController::class, 'update']);
    Route::delete('role/delete/{id}', [RolesController::class, 'destroy']);

    //permissions routes
    Route::get('permissions', [PermissionsController::class, 'permissions']);
    Route::post('permissions/{id}', [PermissionsController::class, 'getPermissionById']);
    Route::post('permission/save', [PermissionsController::class, 'store']);
    Route::delete('permissions/delete/{id}', [PermissionsController::class, 'destroy']);

    //logged in user
    Route::post('user', [AuthController::class, 'user']);

    //users routes
    Route::get('users', [UsersController::class, 'index']);
    Route::get('user/{id}', [UsersController::class, 'getUserById']);
    Route::put('user/{id}', [UsersController::class, 'edit']);
    Route::post('usersdata/save', [UsersController::class, 'store']);
    Route::delete('users/delete/{id}', [UsersController::class, 'destroy']);

    Route::get('get-category/{id}', [NCCallCategoryController::class, 'getActiveCategoryByCallTypeId']);

    Route::get('call-sub-by-call-cat-id/{ctid}/{ccid}', [NCCallSubCategoryController::class, 'getCallSubCatByCallAndCategoryId']);
    Route::get('get-required-fields/{ctid}/{ccid}/{cscid}', [NCRequiredConfigController::class, 'getRequiredFieldConfigsData']);
    Route::get('get-required-field-by-sub-cat-id/{id}', [NCRequiredConfigController::class, 'getRequiredFieldConfigBySubCatId']);

    Route::post('groups/{group}/roles', [GroupRoleController::class, 'assignRoles']);
    Route::delete('groups/{group}/roles/{role}', [GroupRoleController::class, 'removeRoles']);

    Route::post('user/register', [AuthController::class, 'register']);

    Route::get('get-service-types', [NCCallTypeController::class, 'getActiveCallType']);
    Route::get('get-service-category', [NCCallCategoryController::class, 'getActiveServiceCategory']);

    // for nagad api
    Route::apiResources([
        'tickets' => NCTicketController::class,
        'groups' => GroupController::class,
        'call-categories' => NCCallCategoryController::class,
        'call-types' => NCCallTypeController::class,
        'call-sub-categories' => NCCallSubCategoryController::class,
        'call-sub-sub-categories' => NCCallSubSubCategoryController::class,
        'required-fields-configs' => NCRequiredConfigController::class,
        'service-type-config' => NCServiceTypeConfigController::class,
    ]);
});
