<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;
use App\Http\Controllers\Api\NcRecuiredConfigController;
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


Route::get('get_call_type', [Api\CrudController::class, 'getCallType']);
Route::post('store_call_type', [Api\CrudController::class, 'addCallType']);
Route::get('edit_call_type/{id}', [Api\CrudController::class, 'editCallType']);
Route::post('update_call_type/{id}', [Api\CrudController::class, 'updateCallType']);
Route::get('delete_call_type/{id}', [Api\CrudController::class, 'deleteCallType']);

Route::get('get_call_category', [Api\CrudController::class, 'getCallCategory']);
Route::post('store_call_category', [Api\CrudController::class, 'addCallCategory']);
Route::get('edit_call_category/{id}', [Api\CrudController::class, 'editCallCategory']);
Route::post('update_call_category/{id}', [Api\CrudController::class, 'updateCallCategory']);
Route::get('delete_call_category/{id}', [Api\CrudController::class, 'deleteCallCategory']);

Route::get('get_call_sub_category', [Api\CrudController::class, 'getCallSubCategory']);
Route::post('store_call_sub_category', [Api\CrudController::class, 'addCallSubCategory']);
Route::get('edit_call_sub_category/{id}', [Api\CrudController::class, 'editCallSubCategory']);
Route::post('update_call_sub_category/{id}', [Api\CrudController::class, 'updateCallSubCategory']);
Route::get('delete_call_sub_category/{id}', [Api\CrudController::class, 'deleteCallSubCategory']);

Route::get('get_call_sub_sub_category', [Api\CrudController::class, 'getCallSubSubCategory']);
Route::post('store_call_sub_sub_category', [Api\CrudController::class, 'addCallSubSubCategory']);
Route::get('edit_call_sub_sub_category/{id}', [Api\CrudController::class, 'editCallSubSubCategory']);
Route::post('update_call_sub_sub_category/{id}', [Api\CrudController::class, 'updateCallSubSubCategory']);
Route::get('delete_call_sub_sub_category/{id}', [Api\CrudController::class, 'deleteCallSubSubCategory']);

//Route::apiResource('store_config', NcRecuiredConfigController::class);
Route::get('get-category/{id}', [NCCallCategoryController::class, 'getCategoryByCallTypeId']);
Route::get('get-sub-category/{id}', [NCCallSubCategoryController::class, 'getSubCategoryByCategoryId']);
Route::get('get-nc-filed_config-data/{id}', [NcRecuiredConfigController::class, 'getNcFiledConfigDataByConfigId']);


    Route::apiResources([
        'groups' => NCGroupController::class,
        'call-categories' => NCCallCategoryController::class,
        'call-types' => NCCallTypeController::class,
        'call-sub-categories' => NCCallSubCategoryController::class,
        'call-sub-sub-categories' => NCCallSubSubCategoryController::class,
        'access-lists' => NCAccessListController::class,
        'group-configs' => NCGroupConfigsController::class,
        'store_config' => NcRecuiredConfigController::class,
    ]);

