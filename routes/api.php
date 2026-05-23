<?php

use App\Http\Controllers\API\Agent\AgentController;
use App\Http\Controllers\API\Assembly\AssemblyController;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Bill\BillController;
use App\Http\Controllers\API\Block\BlockController;
use App\Http\Controllers\API\Business\BusinessController;
use App\Http\Controllers\API\BusinessClassType\EntityTypeController;
use App\Http\Controllers\API\Customer\CustomerController;
use App\Http\Controllers\API\CustomerSupport\CustomerSupportController;
use App\Http\Controllers\API\Dashboard\DashboardController;
use App\Http\Controllers\API\Division\DivisionController;
use App\Http\Controllers\API\Dropdown\DropdownController;
use App\Http\Controllers\API\Payment\PaymentController;
use App\Http\Controllers\API\Property\PropertyController;
use App\Http\Controllers\API\PropertyUse\PropertyUseController;
use App\Http\Controllers\API\Statement\StatementController;
use App\Http\Controllers\API\User\UserController;
use App\Http\Controllers\API\USSD\USSDController;
use App\Http\Controllers\API\Zone\ZoneController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/payment/callback', [PaymentController::class, 'paymentCallback'])->name('payments.callback');
Route::post('/payment/paystack/webhook', [PaymentController::class, 'handlePaymentPaystackWebhook']);

Route::post('/login', [LoginController::class, 'index']);
Route::post('/send/otp', [LoginController::class, 'sendOTP']);
Route::post('/change/password', [LoginController::class, 'changePassword']);
Route::post('/customer/create/front', [CustomerController::class, 'frontstore']);

Route::group(['prefix' => 'user'], function () {
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/create', [UserController::class, 'store']);
        Route::get('/show/{id}', [UserController::class, 'show']);
        Route::post('/update/{id}', [UserController::class, 'update']);
        Route::post('/logout', [LoginController::class, 'logout']);
        Route::post('/change/password/no/otp', [LoginController::class, 'changePasswordNoOTP']);
    });
});

Route::group(['prefix' => 'assembly', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [AssemblyController::class, 'index']);
    Route::get('/show/{id}', [AssemblyController::class, 'show']);
});

Route::group(['prefix' => 'division', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [DivisionController::class, 'index']);
    Route::get('/show/{id}', [DivisionController::class, 'show']);
    Route::get('/assembly/{code}', [DivisionController::class, 'assemblyDivision']);
});

Route::group(['prefix' => 'customer', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [CustomerController::class, 'index']);
    Route::get('/show/{id}', [CustomerController::class, 'show']);
    Route::get('/fetch/{id}', [CustomerController::class, 'multipleParam']);
    Route::post('/create', [CustomerController::class, 'store']);
    Route::post('/update/{id}', [CustomerController::class, 'update']);
    Route::get('/search/{id}', [CustomerController::class, 'globalSearch']);
});

Route::group(['prefix' => 'bill', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [BillController::class, 'index']);
    Route::get('/user', [BillController::class, 'myBills']);
    Route::get('/show/{id}', [BillController::class, 'show']);
    Route::get('/customer/{id}', [BillController::class, 'customerBill']);
    Route::get('/pending/customer/{id}', [BillController::class, 'pendingBill']);
    Route::post('/issue', [BillController::class, 'issueBill']);
    Route::post('/send-otp', [BillController::class, 'sendOTP']);
});

Route::group(['prefix' => 'payment', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [PaymentController::class, 'index']);
    Route::get('/show/{id}', [PaymentController::class, 'show']);
    Route::get('/customer/{id}', [PaymentController::class, 'customerPayment']);
    Route::post('/create', [PaymentController::class, 'makePayment']);
    Route::post('/confirm', [PaymentController::class, 'confirmPayment']);
});

Route::group(['prefix' => 'agent', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [AgentController::class, 'index']);
    Route::get('/show/{id}', [AgentController::class, 'show']);
    Route::post('/update/{id}', [AgentController::class, 'update']);
    Route::get('/task', [AgentController::class, 'agentTask']);
    Route::get('/task/show/{id}', [AgentController::class, 'agentTaskShow']);
    Route::get('/task/{id}', [AgentController::class, 'agentTaskAssignment']);
    Route::get('/task/block/{id}/{task_type}', [AgentController::class, 'agentTaskBlock']);
    Route::get('/task/property/{id}', [AgentController::class, 'agentPropertyBlock']);
    Route::get('/payment/{id}', [AgentController::class, 'agentPayment']);
    Route::post('/task/update/{id}', [AgentController::class, 'taskUpdate']);
    Route::post('/upload/report', [AgentController::class, 'uploadReport']);
});

Route::group(['prefix' => 'customer-support', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [CustomerSupportController::class, 'index']);
    Route::get('/show/{id}', [CustomerSupportController::class, 'show']);
    Route::get('/user/{id}', [CustomerSupportController::class, 'userShow']);
    Route::post('/create', [CustomerSupportController::class, 'store']);
    Route::post('/update/{id}', [CustomerSupportController::class, 'update']);
});

Route::group(['prefix' => 'dashboard', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/payment/{id}', [DashboardController::class, 'agentPayment']);
    Route::get('/task/{id}', [DashboardController::class, 'agentTask']);
    Route::get('/data/{id}', [DashboardController::class, 'dashboardData']);
    Route::get('/customer/{id}', [DashboardController::class, 'customerDashboardData']);
});

Route::group(['prefix' => 'property', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [PropertyController::class, 'index']);
    Route::get('/show/{id}', [PropertyController::class, 'show']);
    Route::get('/customer/{id}', [PropertyController::class, 'customerProperty']);
    Route::post('/create', [PropertyController::class, 'store']);
    Route::post('/update/{id}', [PropertyController::class, 'update']);
    Route::get('/nearby-properties/{latitude}/{longitude}', [PropertyController::class, 'getNearbyProperties']);
});

Route::group(['prefix' => 'business', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [BusinessController::class, 'index']);
    Route::get('/show/{id}', [BusinessController::class, 'show']);
    Route::get('/customer/{id}', [BusinessController::class, 'customerBusiness']);
});

Route::get('/ussd/generate/token', [USSDController::class, 'generateToken']);

Route::group(['prefix' => 'ussd', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/customer/{phone}', [USSDController::class, 'customerInfo']);
});

Route::group(['prefix' => 'entity-type', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [EntityTypeController::class, 'index']);
    Route::get('/show/{id}', [EntityTypeController::class, 'show']);
});

Route::group(['prefix' => 'block', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [BlockController::class, 'index']);
    Route::get('/show/{id}', [BlockController::class, 'show']);
});

Route::group(['prefix' => 'zone', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [ZoneController::class, 'index']);
    Route::get('/show/{id}', [ZoneController::class, 'show']);
});

Route::group(['prefix' => 'property-use', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/{zone_id}', [PropertyUseController::class, 'index']);
    Route::get('/show/{id}', [PropertyUseController::class, 'show']);
});

Route::group(['prefix' => 'dropdown', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [DropdownController::class, 'index']);
});

Route::group(['prefix' => 'statement', 'middleware' => 'auth:sanctum'], function () {
    Route::post('/bill', [StatementController::class, 'billStatement']);
    Route::post('/payment', [StatementController::class, 'paymentStatement']);
});
