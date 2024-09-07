<?php

use App\Http\Controllers\Api;
use App\Http\Controllers\BulkTicketCreateController;
use App\Http\Controllers\BulkTicketStatusUpdateController;
use App\Http\Controllers\ClickActivityController;
use App\Http\Controllers\DNDUserController;
use App\Http\Controllers\EmailConfigController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\NCCallCategoryController;
use App\Http\Controllers\NCCallSubCategoryController;
use App\Http\Controllers\NCCallTypeController;
use App\Http\Controllers\NCNotificationController;
use App\Http\Controllers\NCReportController;
use App\Http\Controllers\NCRequiredConfigController;
use App\Http\Controllers\NCServiceTypeConfigController;
use App\Http\Controllers\NCTicketController;
use App\Http\Controllers\NCTicketTimelineController;
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
DELETE /users/{user} → destroy method
 */

// WITHOUT AUTHENTICATION ROUTES STARTED
Route::post('login', [Api\AuthController::class, 'login']);
// THIS ROUTE USED TO COMPLETE THE LOGIN WITH USER LOCATION OF LATITUDE AND LONGITUDE
Route::post('complete-login', [Api\AuthController::class, 'completeLogin']);

Route::post('forgot', [Api\ForgotController::class, 'forgot']);
Route::post('reset', [Api\ForgotController::class, 'reset']);
Route::get('email/resend/{user}', [Api\VerifyController::class, 'resend'])->name('verification.resend');
Route::get('email/verify/{id}', [Api\VerifyController::class, 'verify'])->name('verification.verify');
// WITHOUT AUTHENTICATION ROUTES ENDED

// SANCTUM BASED API ROUTES
Route::group(['middleware' => 'auth:sanctum'], function () {

    // AUTHENTICATION ROUTES
    Route::post('/logout', [Api\AuthController::class, 'logout']);

    // LOGGED IN USER
    Route::post('user', [Api\AuthController::class, 'user']);
    Route::post('user/register', [Api\AuthController::class, 'register']);
    // CHANGE PASSWORD
    Route::post('change-password', [Api\AuthController::class, 'changePassword']);

    // GET CURRENTLY LOGGED IN USER ID
    Route::get('/userPermissions', [PermissionsController::class, 'getCurrentUserPermissions']);

    Route::get('/user-permissions/{id}', [PermissionsController::class, 'getUserPermissionsById']);

    // GET LOCATION OF REQUIRES GROUPS
    Route::get('/userLocations', [UsersController::class, 'getUserLocation']);

    // GET ACTIVE USERS FOR FORWARD THE TICKET
    Route::get('/getActiveUsers', [UsersController::class, 'getActiveUsers']);
    Route::get('/getActiveGroups', [GroupController::class, 'getActiveGroups']);

    // BULK TICKET CREATE/STATUS UPDATES

    Route::post('/bulk-ticket-status-update', [BulkTicketStatusUpdateController::class, 'store']);
    Route::get('/bulk-ticket-statuses', [BulkTicketStatusUpdateController::class, 'index']);
    Route::delete('/bulk-ticket-statuses/{id}', [BulkTicketStatusUpdateController::class, 'destroy']);

    Route::post('/bulk-ticket-create', [BulkTicketCreateController::class, 'store']);
    Route::get('/bulk-tickets', [BulkTicketCreateController::class, 'index']);
    Route::delete('/bulk-tickets/{id}', [BulkTicketCreateController::class, 'destroy']);

    // Group Permissions

    Route::post('/group/permissions/{id}', [GroupController::class, 'assignPermission']);

    // ROLES ROUTES WITH ROLE AND PERMISSION MIDDLEWARE
    Route::middleware(['role:owner|superadmin|admin'])->group(function () {

        // ROLES RELATED ROUTES
        Route::middleware('permission:role-list')->group(function () {
            Route::get('roles', [RolesController::class, 'roles']);
            Route::get('roles/{id}', [RolesController::class, 'getRoleById']);
        });
        Route::middleware('permission:role-create')->post('roles/create', [RolesController::class, 'store']);
        Route::middleware('permission:role-edit')->put('roles/{id}', [RolesController::class, 'update']);
        Route::middleware('permission:role-delete')->delete('roles/delete/{id}', [RolesController::class, 'destroy']);

        // PERMISSIONS RELATED ROUTES
        Route::prefix('permissions')->group(function () {
            Route::middleware('permission:permission-list')
                ->get('/', [PermissionsController::class, 'permissions']);

            Route::middleware('permission:permission-edit')
                ->post('{id}', [PermissionsController::class, 'getPermissionById']);

            Route::middleware('permission:permission-create')
                ->post('/', [PermissionsController::class, 'store']);

            Route::middleware('permission:permission-edit')
                ->put('{id}', [PermissionsController::class, 'update']);

            Route::middleware('permission:permission-delete')
                ->delete('{id}', [PermissionsController::class, 'destroy']);
        });
    });

    // ASSIGN ROLES TO A SPECIFIC USER
    Route::post('/user/{id}/roles', [UsersController::class, 'assignRoles']);
    // RETRIEVE USER ROLES
    Route::get('/user/{id}/roles', [UsersController::class, 'getUserRoles']);
    // REMOVE ROLE FROM A USER
    Route::delete('/user/{id}/roles/{roleId}', [UsersController::class, 'removeRole']);

    // ASSIGN ROLES TO GROUP ROUTES GOES HERE
    Route::post('group/{group}/roles', [GroupController::class, 'assignRoles']);
    Route::delete('group/{group}/roles/{role}', [GroupController::class, 'removeRole']);

    // USERS ROUTES
    Route::get('users', [UsersController::class, 'index']);
    Route::get('user/{id}', [UsersController::class, 'getUserById']);
    Route::put('user/{id}', [UsersController::class, 'edit']);
    Route::put('user/{id}/reset-password', [UsersController::class, 'resetPassword']);
    Route::post('usersdata/save', [UsersController::class, 'store']);
    Route::delete('user/{id}/delete', [UsersController::class, 'destroy']);
    Route::post('/user/permissions/{id}', [UsersController::class, 'assignPermission']);

    // APPLICATION RELATED ROUTES
    Route::get('get-category/{id}', [NCCallCategoryController::class, 'getActiveCategoryByCallTypeId']);
    Route::get('call-sub-by-call-cat-id/{ctid}/{ccid}', [NCCallSubCategoryController::class, 'getCallSubCatByCallAndCategoryId']);
    Route::get('get-required-fields/{ctid}/{ccid}/{cscid}', [NCRequiredConfigController::class, 'getRequiredFieldConfigsData']);
    Route::get('get-required-field-by-sub-cat-id/{id}', [NCRequiredConfigController::class, 'getRequiredFieldConfigBySubCatId']);

    Route::get('get-service-types', [NCCallTypeController::class, 'getActiveCallType']);
    Route::get('get-service-category', [NCCallCategoryController::class, 'getActiveServiceCategory']);
    Route::get('get-service-type-configs/{cti}/{cci}/{csci}', [NCServiceTypeConfigController::class, 'getServiceTypeConfigs']);

    // Dashboard related apis
    Route::get('get-total-report-count/{id?}', [NCReportController::class, 'getTotalReportCount']);
    Route::get('get-daily-report-count/{id?}', [NCReportController::class, 'getDailyReportCount']);
    Route::get('get-month-wise-ticket-status-count/{id?}/{month?}', [NCReportController::class, 'getMonthWiseTicketStatusCount']);
    Route::get('get-date-wise-column-chart-data-count/{id?}/{month?}', [NCReportController::class, 'getDateWiseColumnChartDataCount']);

    // Get email configurations
    // email-configs
    Route::get('email-configs', [EmailConfigController::class, 'allActiveEmailConfigs']);

    // user-in dnd
    Route::get('in-dnd/{mobile_no}', [DNDUserController::class, 'getDNDNumberVerification']);

    // Ticket related routes
    Route::get('previous-ticket/{mobile_no}', [NCTicketController::class, 'getPreviousTicket']);

    Route::get('ticket/{id}/timeline', [NCTicketTimelineController::class, 'show']);

    // Ticket assignId
    Route::post('/ticket/assign/{id}', [NCTicketController::class, 'assignTicket']);
    Route::get('/ticket/status/{id}', [NCTicketController::class, 'ticketStatus']);

    Route::post('/ticket/forward/{id}', [NCTicketController::class, 'forwardTicket']);

    Route::post('/ticket/timeline/{id}', [NCTicketController::class, 'addTimelineForFirstTimePageLoad']);

    Route::get('users-index', [UsersController::class, 'userIndex']);

    Route::apiResources([
        'click-activity' => ClickActivityController::class,
        'tickets' => NCTicketController::class,
        'notifications' => NCNotificationController::class,
        'groups' => GroupController::class,
        'call-categories' => NCCallCategoryController::class,
        'call-types' => NCCallTypeController::class,
        'call-sub-categories' => NCCallSubCategoryController::class,
        'required-fields-configs' => NCRequiredConfigController::class,
        'service-type-config' => NCServiceTypeConfigController::class,
        'email-config' => EmailConfigController::class,
        'dnd-user' => DNDUserController::class,
    ]);
});
