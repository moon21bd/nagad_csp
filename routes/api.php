<?php

use App\Http\Controllers\Api;
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
use Illuminate\Http\Request;
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

// Without Authentication Routes
Route::post('login', [Api\AuthController::class, 'login']);
// this route used to complete the login with latitude and longitude
Route::post('complete-login', [Api\AuthController::class, 'completeLogin']);

Route::post('forgot', [Api\ForgotController::class, 'forgot']);
Route::post('reset', [Api\ForgotController::class, 'reset']);
Route::get('email/resend/{user}', [Api\VerifyController::class, 'resend'])->name('verification.resend');
Route::get('email/verify/{id}', [Api\VerifyController::class, 'verify'])->name('verification.verify');

// sanctum based routes
Route::group(['middleware' => 'auth:sanctum'], function () {

    // authentication routes
    Route::post('/logout', [Api\AuthController::class, 'logout']);

    //logged in user
    Route::post('user', [Api\AuthController::class, 'user']);
    Route::post('user/register', [Api\AuthController::class, 'register']);

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

    //users routes
    Route::get('users', [UsersController::class, 'index']);
    Route::get('user/{id}', [UsersController::class, 'getUserById']);
    Route::put('user/{id}', [UsersController::class, 'edit']);
    Route::post('usersdata/save', [UsersController::class, 'store']);
    Route::delete('users/delete/{id}', [UsersController::class, 'destroy']);

    // application related routes
    Route::get('get-category/{id}', [NCCallCategoryController::class, 'getActiveCategoryByCallTypeId']);
    Route::get('call-sub-by-call-cat-id/{ctid}/{ccid}', [NCCallSubCategoryController::class, 'getCallSubCatByCallAndCategoryId']);
    Route::get('get-required-fields/{ctid}/{ccid}/{cscid}', [NCRequiredConfigController::class, 'getRequiredFieldConfigsData']);
    Route::get('get-required-field-by-sub-cat-id/{id}', [NCRequiredConfigController::class, 'getRequiredFieldConfigBySubCatId']);
    Route::post('groups/{group}/roles', [GroupRoleController::class, 'assignRoles']);
    Route::delete('groups/{group}/roles/{role}', [GroupRoleController::class, 'removeRoles']);
    Route::get('get-service-types', [NCCallTypeController::class, 'getActiveCallType']);
    Route::get('get-service-category', [NCCallCategoryController::class, 'getActiveServiceCategory']);

    /*
    // Resource Controller Methods

    GET /users → index method
    GET /users/create → create method
    POST /users → store method
    GET /users/{user} → show method
    GET /users/{user}/edit → edit method
    PUT /users/{user} → update method
    PATCH /users/{user} → update method
    DELETE /users/{user} → destroy method
     */

    // various resource apis
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

// for renewing token
Route::middleware('auth:sanctum')->post('/renew-token', function (Request $request) {
    $user = $request->user();
    // $token = $user->createToken('authToken')->plainTextToken;
    $token = $user->createToken('Personal Access Token')->plainTextToken;

    return response()->json(['token' => $token]);
});
