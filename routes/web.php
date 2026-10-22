<?php

use App\Http\Controllers\AgentAssignment\AgentAssignmentController;
use App\Http\Controllers\Assembly\AssemblyController;
use App\Http\Controllers\AuthAdmin;
use App\Http\Controllers\BillAdjustment\BillAdjustmentController;
use App\Http\Controllers\BillsManagement\BillsManagementController;
use App\Http\Controllers\BillsManagement\BusBillsManagementController;
use App\Http\Controllers\Blocks\BlockController;
use App\Http\Controllers\Building\BuildingController;
use App\Http\Controllers\BusinessClass\BusinessClassController;
use App\Http\Controllers\BusinessClassTypes\BusinessClassTypeController;
use App\Http\Controllers\Businesses\BusinessController;
use App\Http\Controllers\BusinessType\BusinessTypeController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Citizens\CitizenController;
use App\Http\Controllers\CustomerSupport\CustomerSupportController;
use App\Http\Controllers\CustomerType\CustomerTypeController;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\Dashboard\AccountDashboardController;
use App\Http\Controllers\Divisions\DivisionController;
use App\Http\Controllers\Donation\DonationController;
use App\Http\Controllers\ItemDescription\ItemDescriptionController;
use App\Http\Controllers\Locality\LocalityBusinessController;
use App\Http\Controllers\MainItem\MainItemController;
use App\Http\Controllers\Mmda\MmdaController;
use App\Http\Controllers\NewBlock\NewBlockController;
use App\Http\Controllers\NewBusiness\NewBusinessController;
use App\Http\Controllers\NewDivision\NewDivisionController;
use App\Http\Controllers\NewProperty\NewPropertyController;
use App\Http\Controllers\Owner\OwnerController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Permission;
use App\Http\Controllers\Properties\PropertyController;
use App\Http\Controllers\PropertyClasses\PropertyClassesController;
use App\Http\Controllers\PropertyRevenue\PropertyRevenueController;
use App\Http\Controllers\PropertyType\PropertyTypeController;
use App\Http\Controllers\PropertyUser\PropertyUserController;
use App\Http\Controllers\PublicProPaymentRequestController;
use App\Http\Controllers\Rate\BusRateController;
use App\Http\Controllers\Rate\RateController;
use App\Http\Controllers\Report;
use App\Http\Controllers\RevenueStream\RevenueStreamController;
use App\Http\Controllers\RevenueStreamType\RevenueStreamTypeController;
use App\Http\Controllers\Role;
use App\Http\Controllers\SubItem\SubItemController;
use App\Http\Controllers\TaskAssignment\TaskAssignmentController;
use App\Http\Controllers\User;
use App\Http\Controllers\Zone\ZoneController;
use Illuminate\Support\Facades\Route;

Route::get('/faqAction', [Dashboard\DashboardController::class, 'faqAction'])->name('dashboard.faq');

// ── SUPPORT / DONATIONS (public — no auth required)
Route::get('/support', [DonationController::class, 'index'])->name('donation.index');
Route::post('/donate/stripe', [DonationController::class, 'initializeStripe'])->name('donation.stripe');
Route::get('/donate/verify/paystack', [DonationController::class, 'verifyPaystack'])->name('donation.verify.paystack');
Route::get('/donate/verify/flutterwave', [DonationController::class, 'verifyFlutterwave'])->name('donation.verify.flutterwave');
Route::get('/donate/verify/stripe', [DonationController::class, 'verifyStripe'])->name('donation.verify.stripe');
Route::get('/pay/{paymentRequest}', [PublicProPaymentRequestController::class, 'show'])->name('public.pro-payment-requests.show');
Route::match(['get', 'post'], '/pay/{paymentRequest}/callback', [PublicProPaymentRequestController::class, 'callback'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('public.pro-payment-requests.callback');

Route::view('/check-locality', 'auth.check-locality')->name('locality.check');
Route::get('/check-locality/business-data', LocalityBusinessController::class)->name('locality.business-data');

Route::view('/business-we-support', 'igf.index')->name('igf.index');

Route::group(['prefix' => 'payment', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/create/{bill}', [PaymentController::class, 'create'])->name('payments.create');
    Route::get('/customer/create/{bill}', [PaymentController::class, 'customerCreate'])->name('payments.customerCreate');
    Route::post('/create', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/{payment}/show', [PaymentController::class, 'show'])->name('payments.show');
    Route::get('/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');
    Route::get('/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
    Route::post('/{payment}/update', [PaymentController::class, 'update'])->name('payments.update');
    Route::post('/make/payment', [PaymentController::class, 'makePayment'])->name('payments.makePayment');
    Route::get('/verify', [PaymentController::class, 'verify'])
        ->withoutMiddleware('auth:sanctum')
        ->name('payments.verify');
});

// route for bils
Route::group(['prefix' => 'bills-management', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [BillsManagementController::class, 'index'])->name('bills.index');
    Route::get('/create', [BillsManagementController::class, 'create'])->name('bills.create');
    Route::post('/', [BillsManagementController::class, 'store'])->name('bills.store');
    Route::get('/single', [BillsManagementController::class, 'singleCreate'])->name('bills.singleCreate');
    Route::post('/single', [BillsManagementController::class, 'singleStore'])->name('bills.singleStore');
    Route::get('/division/bill', [BillsManagementController::class, 'divisionCreate'])->name('bills.divisionCreate');
    Route::post('/division/bill', [BillsManagementController::class, 'divisionStore'])->name('bills.divisionStore');
    Route::get('/block/bill', [BillsManagementController::class, 'blockCreate'])->name('bills.blockCreate');
    Route::post('/block/bill', [BillsManagementController::class, 'blockStore'])->name('bills.blockStore');
    Route::post('/property', [BillsManagementController::class, 'property'])->name('bills.property');
    Route::get('/fetch/bill', [BillsManagementController::class, 'fetchBill'])->name('bills.fetchBill');
    Route::get('/{bill}/receipt', [BillsManagementController::class, 'receipt'])->name('bills.receipt');
    Route::get('/{bill}', [BillsManagementController::class, 'show'])->name('bills.show');
    //    Route::get('/{bill}/edit', [BillsManagementController::class, 'edit'])->name('bills.edit');
    Route::put('/{bill}', [BillsManagementController::class, 'update'])->name('bills.update');
    Route::delete('/{bill}', [BillsManagementController::class, 'destroy'])->name('bills.destroy');
    Route::get('/fetch/properties', [BillsManagementController::class, 'ajaxList'])->name('bills.properties.ajax');
    Route::post('/preview', [BillsManagementController::class, 'preview'])->name('bills.preview');
});

Route::group(['prefix' => 'bus-bills-management', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [BusBillsManagementController::class, 'index'])->name('bills.bus.index');
    Route::get('/create', [BusBillsManagementController::class, 'create'])->name('bills.bus.create');
    Route::post('/create', [BusBillsManagementController::class, 'store'])->name('bills.bus.store');
    Route::get('/{bill}/show', [BusBillsManagementController::class, 'show'])->name('bills.bus.show');
    Route::get('/{bill}/edit', [BusBillsManagementController::class, 'edit'])->name('bills.bus.edit');
    Route::post('/{bill}/update', [BusBillsManagementController::class, 'update'])->name('bills.bus.update');
    Route::get('/single', [BusBillsManagementController::class, 'singleCreate'])->name('bills.bus.singleCreate');
    Route::post('/single', [BusBillsManagementController::class, 'singleStore'])->name('bills.bus.singleStore');
    Route::get('/division/bill', [BusBillsManagementController::class, 'divisionCreate'])->name('bills.bus.divisionCreate');
    Route::post('/division/bill', [BusBillsManagementController::class, 'divisionStore'])->name('bills.bus.divisionStore');
    Route::get('/block/bill', [BusBillsManagementController::class, 'blockCreate'])->name('bills.bus.blockCreate');
    Route::post('/block/bill', [BusBillsManagementController::class, 'blockStore'])->name('bills.bus.blockStore');
    Route::post('/business/data', [BusBillsManagementController::class, 'business'])->name('bills.business');
    Route::get('/fetch/businesses', [BusBillsManagementController::class, 'ajaxList'])->name('bills.businesses.ajax');
    Route::post('/preview', [BusBillsManagementController::class, 'preview'])->name('bills.bus.preview');
});

// route for properties
Route::group(['prefix' => 'property', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [PropertyController::class, 'index'])->name('properties.index');
    Route::get('/create', [PropertyController::class, 'create'])->name('properties.create');
    Route::post('/', [PropertyController::class, 'store'])->name('properties.store');
    Route::post('/ratepayer', [PropertyController::class, 'ratePayerStore'])->name('properties.ratePayer');
    Route::get('/{property}', [PropertyController::class, 'show'])->name('properties.show');
    Route::get('/{property}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
    Route::post('/{property}', [PropertyController::class, 'update'])->name('properties.update');
    Route::delete('/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy');
    Route::get('/{property}', [PropertyController::class, 'getDetails'])->name('properties.details');
    Route::get('/citizens/search', [PropertyController::class, 'searchCitizens'])->name('citizens.search');
    Route::post('/assign-citizen', [PropertyController::class, 'assignCitizen'])->name('properties.assignCitizen');
    Route::get('/{property}', [PropertyController::class, 'show'])->name('properties.show');
    Route::post('/block/data', [PropertyController::class, 'block'])->name('ajax.block');
    Route::get('/import/data', [PropertyController::class, 'import'])->name('properties.import');
    Route::post('/import/data', [PropertyController::class, 'importData'])->name('properties.importData');
    Route::get('/template/data', [PropertyController::class, 'downloadTemplate'])->name('properties.downloadTemplate');
    Route::get('/get-all/data', [PropertyController::class, 'getAllProperties'])->name('properties.getAll');
});

Route::group(['prefix' => 'new-property', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [NewPropertyController::class, 'index'])->name('new-properties.index');
    Route::get('/create', [NewPropertyController::class, 'create'])->name('new-properties.create');
    Route::post('/create', [NewPropertyController::class, 'store'])->name('new-properties.store');
    Route::get('/show/{property}', [NewPropertyController::class, 'show'])->name('new-properties.show');
    Route::get('/edit/{property}', [NewPropertyController::class, 'edit'])->name('new-properties.edit');
    Route::put('/update/{property}', [NewPropertyController::class, 'update'])->name('new-properties.update');
    Route::get('/revenue-stream/{property}', [NewPropertyController::class, 'assignRevenueStream'])->name('properties.assignRevenueStream');
    Route::get('/assign/revenue-stream', [NewPropertyController::class, 'assignRevenueStreamStore'])->name('properties.assignRevenue');
    Route::get('/map-data', [NewPropertyController::class, 'getMapData'])->name('properties.mapData');
    Route::get('/polygons', [NewPropertyController::class, 'polygons'])->name('properties.polygons');
    Route::get('/map', [NewPropertyController::class, 'map'])->name('properties.map');
    Route::get('/allocations/{uuid}', [NewPropertyController::class, 'allocations'])->name('properties.allocations');
    Route::get('/ajax/rated-properties', [NewPropertyController::class, 'ratedProperties'])->name('ajax.rated-properties');
    Route::get('/ajax/search-rated-property', [NewPropertyController::class, 'searchRatedProperty'])->name('ajax.search-rated-property');
});

// Route for propert Class Types mistakenly named it business class type
Route::group(['prefix' => 'business-class-type', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [BusinessClassTypeController::class, 'index'])->name('business-class-types.index');
    Route::get('/create', [BusinessClassTypeController::class, 'create'])->name('business-class-types.create');
    Route::post('/', [BusinessClassTypeController::class, 'store'])->name('business-class-types.store');
    Route::get('/{businessClassType}', [BusinessClassTypeController::class, 'show'])->name('business-class-types.show');
    Route::get('/{businessClassType}/edit', [BusinessClassTypeController::class, 'edit'])->name('business-class-types.edit');
    Route::post('/{businessClassType}', [BusinessClassTypeController::class, 'update'])->name('business-class-types.update');
    Route::delete('/{businessClassType}', [BusinessClassTypeController::class, 'destroy'])->name('business-class-types.destroy');
});

// Route for Business
Route::group(['prefix' => 'business', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [BusinessController::class, 'index'])->name('businesses.index');
    Route::get('/create', [BusinessController::class, 'create'])->name('businesses.create');
    Route::post('/', [BusinessController::class, 'store'])->name('businesses.store');
    Route::get('/{business}', [BusinessController::class, 'show'])->name('businesses.show');
    Route::get('/{business}/edit', [BusinessController::class, 'edit'])->name('businesses.edit');
    Route::post('/{business}', [BusinessController::class, 'update'])->name('businesses.update');
    Route::delete('/{business}', [BusinessController::class, 'destroy'])->name('businesses.destroy');
    Route::post('/business/class', [BusinessController::class, 'businessClass'])->name('ajax.business_class');
});

Route::group(['prefix' => 'new-business', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [NewBusinessController::class, 'index'])->name('new-businesses.index');
    Route::get('/create', [NewBusinessController::class, 'create'])->name('new-businesses.create');
    Route::post('/create', [NewBusinessController::class, 'store'])->name('new-businesses.store');
    Route::get('/show/{business}', [NewBusinessController::class, 'show'])->name('new-businesses.show');
    Route::get('/edit/{business}', [NewBusinessController::class, 'edit'])->name('new-businesses.edit');
    Route::post('/update/{business}', [NewBusinessController::class, 'update'])->name('new-businesses.update');
    Route::get('/revenue-stream/{business}', [NewBusinessController::class, 'assignRevenueStream'])->name('businesses.assignRevenueStream');
    Route::get('/assign/revenue-stream', [NewBusinessController::class, 'assignRevenueStreamStore'])->name('businesses.assignRevenue');
    Route::get('/map-data', [NewBusinessController::class, 'getMapData'])->name('businesses.mapData');
});

// Route for business types
Route::group(['prefix' => 'business-type', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [BusinessTypeController::class, 'index'])->name('business-types.index');
    Route::get('/create', [BusinessTypeController::class, 'create'])->name('business-types.create');
    Route::post('/', [BusinessTypeController::class, 'store'])->name('business-types.store');
    Route::get('/{businessType}', [BusinessTypeController::class, 'show'])->name('business-types.show');
    Route::get('/{businessType}/edit', [BusinessTypeController::class, 'edit'])->name('business-types.edit');
    Route::post('/{businessType}', [BusinessTypeController::class, 'update'])->name('business-types.update');
});

// Route for business classes
Route::group(['prefix' => 'business-class', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [BusinessClassController::class, 'index'])->name('business-classes.index');
    Route::get('/create', [BusinessClassController::class, 'create'])->name('business-classes.create');
    Route::post('/', [BusinessClassController::class, 'store'])->name('business-classes.store');
    Route::get('/{businessClass}', [BusinessClassController::class, 'show'])->name('business-classes.show');
    Route::get('/{businessClass}/edit', [BusinessClassController::class, 'edit'])->name('business-classes.edit');
    Route::post('/{businessClass}', [BusinessClassController::class, 'update'])->name('business-classes.update');
});

// Route for Customer Registration
Route::group(['prefix' => 'citizen', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [CitizenController::class, 'index'])->name('citizens.index');
    Route::get('/create', [CitizenController::class, 'create'])->name('citizens.create');
    Route::post('/', [CitizenController::class, 'store'])->name('citizens.store');
    Route::get('/{citizen}', [CitizenController::class, 'show'])->name('citizens.show');
    Route::get('/{citizen}/edit', [CitizenController::class, 'edit'])->name('citizens.edit');
    Route::post('/{citizen}', [CitizenController::class, 'update'])->name('citizens.update');
    Route::delete('/{citizen}', [CitizenController::class, 'destroy'])->name('citizens.destroy');
    Route::get('/view/bill/{bill}', [CitizenController::class, 'viewBill'])->name('citizens.viewBill');
    Route::get('/view/bill/{bill}/invoice', [CitizenController::class, 'viewBillInvoice'])->name('citizens.viewBill.invoice');
    Route::get('/view/bill/{bill}/invoice/download', [CitizenController::class, 'downloadBillInvoice'])->name('citizens.viewBill.invoice.download');
    Route::get('/view/property/{property}', [CitizenController::class, 'viewProperty'])->name('citizens.viewProperty');
    Route::get('/view/business/{business}', [CitizenController::class, 'viewBusiness'])->name('citizens.viewBusiness');
    Route::get('/view/payment/{payment}', [CitizenController::class, 'viewPayment'])->name('citizens.viewPayment');
    Route::get('/view/payment/{payment}/receipt', [CitizenController::class, 'viewPaymentReceipt'])->name('citizens.viewPayment.receipt');
    Route::get('/view/payment/{payment}/receipt/download', [CitizenController::class, 'downloadPaymentReceipt'])->name('citizens.viewPayment.receipt.download');
    Route::get('/import/data', [CitizenController::class, 'import'])->name('citizens.import');
    Route::post('/import/data', [CitizenController::class, 'importData'])->name('citizens.importData');
    Route::get('/template/data', [CitizenController::class, 'downloadTemplate'])->name('citizens.downloadTemplate');
    Route::get('/linkproperty/data', [CitizenController::class, 'linkProperty'])->name('citizens.linkProperty');
});

Route::group(['prefix' => 'owner', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [OwnerController::class, 'index'])->name('owners.index');
    Route::get('/create', [OwnerController::class, 'create'])->name('owners.create');
    Route::post('/create', [OwnerController::class, 'store'])->name('owners.store');
    Route::get('/show/{owner}', [OwnerController::class, 'show'])->name('owners.show');
    Route::get('/edit/{owner}', [OwnerController::class, 'edit'])->name('owners.edit');
    Route::put('/update/{owner}', [OwnerController::class, 'update'])->name('owners.update');
});

// Route for Customer Types
Route::group(['prefix' => 'customer-type', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [CustomerTypeController::class, 'index'])->name('customer-types.index');
    Route::get('/create', [CustomerTypeController::class, 'create'])->name('customer-types.create');
    Route::post('/', [CustomerTypeController::class, 'store'])->name('customer-types.store');
    Route::get('/{customerType}', [CustomerTypeController::class, 'show'])->name('customer-types.show');
    Route::get('/{customerType}/edit', [CustomerTypeController::class, 'edit'])->name('customer-types.edit');
    Route::post('/{customerType}', [CustomerTypeController::class, 'update'])->name('customer-types.update');
});

// LOGIN MANAGEMENT
Route::prefix('')->group(function () {
    Route::middleware('guest:sanctum')->group(function () {
        Route::get('/', [AuthAdmin\LoginAdminController::class, 'LandingPage'])->name('auth.LandingPage');
        Route::get('/login', [AuthAdmin\LoginAdminController::class, 'index'])->name('auth.index');
        Route::get('/register', [AuthAdmin\LoginAdminController::class, 'register'])->name('auth.register');
        Route::get('/activate', [CitizenController::class, 'activate'])->name('citizens.activate');
        Route::post('/activate', [CitizenController::class, 'activateCitizen'])->name('citizens.activateCitizen');
        Route::get('/resend/otp', [CitizenController::class, 'resend'])->name('citizens.resend');
        Route::post('/resend/otp', [CitizenController::class, 'resendOTP'])->name('citizens.resendOTP');
        Route::post('/frontstore', [CitizenController::class, 'frontstore'])->name('citizens.frontstore');
        Route::post('/login', [AuthAdmin\LoginAdminController::class, 'login'])->name('auth.login');
        Route::get('/change/password/front', [AuthAdmin\LoginAdminController::class, 'changePasswordFront'])->name('auth.changePasswordFront');
        Route::post('/change/password/front', [AuthAdmin\LoginAdminController::class, 'changePasswordFrontWa'])->middleware('throttle:5,1')->name('auth.changePasswordFrontWa');
        Route::get('/send/otp', [AuthAdmin\LoginAdminController::class, 'sendOTP'])->name('auth.sendOTP');
        Route::post('/send/otp', [AuthAdmin\LoginAdminController::class, 'sendUserOTP'])->middleware('throttle:5,1')->name('auth.sendUserOTP');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/logout', [AuthAdmin\LoginAdminController::class, 'logout'])->withoutMiddleware(['guest:sanctum'])->middleware(['auth'])->name('auth.logout');
        Route::get('/change/password', [AuthAdmin\LoginAdminController::class, 'change'])->name('auth.change');
        Route::post('/change/password', [AuthAdmin\LoginAdminController::class, 'changePassword'])->name('auth.changePassword');
    });
});

// DASHBOARD
Route::group(['prefix' => 'dashboard', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [AccountDashboardController::class, 'entry'])->name('dashboard.entry');
    Route::get('/subscriber', [AccountDashboardController::class, 'subscriber'])->name('dashboard.subscriber');
    Route::get('/subscriber/news', [AccountDashboardController::class, 'subscriberNews'])->name('dashboard.subscriber.news');
    Route::get('/subscriber/alerts', [AccountDashboardController::class, 'subscriberAlerts'])->name('dashboard.subscriber.alerts');
    Route::get('/subscriber/locality', [AccountDashboardController::class, 'subscriberLocality'])->name('dashboard.subscriber.locality');
    Route::get('/subscriber/newsletter', [AccountDashboardController::class, 'subscriberNewsletter'])->name('dashboard.subscriber.newsletter');
    Route::post('/subscriber/newsletter', [AccountDashboardController::class, 'subscriberNewsletterUpdate'])->name('dashboard.subscriber.newsletter.update');
    Route::get('/subscriber/profile', [AccountDashboardController::class, 'subscriberProfile'])->name('dashboard.subscriber.profile');
    Route::post('/subscriber/profile', [AccountDashboardController::class, 'subscriberProfileUpdate'])->name('dashboard.subscriber.profile.update');
    Route::get('/olives-account', [AccountDashboardController::class, 'account'])->name('dashboard.olives-account');
    Route::get('/olives-account/properties', [AccountDashboardController::class, 'accountProperties'])->name('dashboard.olives-account.properties');
    Route::get('/olives-account/properties/add-from-map', [AccountDashboardController::class, 'accountPropertyMap'])->name('dashboard.olives-account.properties.map');
    Route::post('/olives-account/properties/add-from-map', [AccountDashboardController::class, 'accountPropertyMapStore'])->name('dashboard.olives-account.properties.map.store');
    Route::get('/olives-account/properties/{property}', [CitizenController::class, 'viewProperty'])->name('dashboard.olives-account.properties.show');
    Route::get('/olives-account/businesses', [AccountDashboardController::class, 'accountBusinesses'])->name('dashboard.olives-account.businesses');
    Route::get('/olives-account/businesses/{business}', [CitizenController::class, 'viewBusiness'])->name('dashboard.olives-account.businesses.show');
    Route::get('/olives-account/bills', [AccountDashboardController::class, 'accountBills'])->name('dashboard.olives-account.bills');
    Route::get('/olives-account/bills/{bill}/invoice', [CitizenController::class, 'viewBillInvoice'])->name('dashboard.olives-account.bills.invoice');
    Route::get('/olives-account/bills/{bill}/invoice/download', [CitizenController::class, 'downloadBillInvoice'])->name('dashboard.olives-account.bills.invoice.download');
    Route::get('/olives-account/bills/{bill}', [CitizenController::class, 'viewBill'])->name('dashboard.olives-account.bills.show');
    Route::get('/olives-account/payments', [AccountDashboardController::class, 'accountPayments'])->name('dashboard.olives-account.payments');
    Route::get('/olives-account/payments/{payment}/receipt', [CitizenController::class, 'viewPaymentReceipt'])->name('dashboard.olives-account.payments.receipt');
    Route::get('/olives-account/payments/{payment}/receipt/download', [CitizenController::class, 'downloadPaymentReceipt'])->name('dashboard.olives-account.payments.receipt.download');
    Route::get('/olives-account/payments/{payment}', [CitizenController::class, 'viewPayment'])->name('dashboard.olives-account.payments.show');
    Route::get('/olives-account/reports', [AccountDashboardController::class, 'accountReports'])->name('dashboard.olives-account.reports');
    Route::get('/olives-account/reports/bills', [AccountDashboardController::class, 'accountBillReport'])->name('dashboard.olives-account.reports.bills');
    Route::get('/olives-account/reports/payments', [AccountDashboardController::class, 'accountPaymentReport'])->name('dashboard.olives-account.reports.payments');
    Route::get('/olives-account/reports/waste-pickups', [AccountDashboardController::class, 'accountWasteReport'])->name('dashboard.olives-account.reports.waste');
    Route::get('/olives-account/reports/audit-trail', [AccountDashboardController::class, 'accountAuditReport'])->name('dashboard.olives-account.reports.audit');
    Route::get('/olives-account/locality', [AccountDashboardController::class, 'accountLocality'])->name('dashboard.olives-account.locality');
    Route::get('/olives-account/waste-pickups', [Dashboard\WasteManagementController::class, 'accountIndex'])->name('dashboard.olives-account.waste');
    Route::post('/olives-account/waste-pickups/book', [Dashboard\WasteManagementController::class, 'storeBooking'])->name('dashboard.olives-account.waste.book');
    Route::post('/olives-account/waste-pickups/{pickup}/complete', [Dashboard\WasteManagementController::class, 'completePickup'])->name('dashboard.olives-account.waste.pickups.complete');
    Route::post('/olives-account/waste-pickups/{pickup}/missed', [Dashboard\WasteManagementController::class, 'reportMissed'])->name('dashboard.olives-account.waste.pickups.missed');
    Route::post('/olives-account/waste-pickups/bookings/{booking}/switch-provider', [Dashboard\WasteManagementController::class, 'switchProvider'])->name('dashboard.olives-account.waste.bookings.switch-provider');
    Route::get('/olives-account/profile', [AccountDashboardController::class, 'accountProfile'])->name('dashboard.olives-account.profile');
    Route::post('/olives-account/profile', [AccountDashboardController::class, 'accountProfileUpdate'])->name('dashboard.olives-account.profile.update');
    Route::get('/olives-pro', [AccountDashboardController::class, 'pro'])->name('dashboard.olives-pro');
    Route::get('/olives-pro/properties', [AccountDashboardController::class, 'proProperties'])->name('dashboard.olives-pro.properties');
    Route::post('/olives-pro/properties', [AccountDashboardController::class, 'proPropertyStore'])->name('dashboard.olives-pro.properties.store');
    Route::post('/olives-pro/properties/{property}/validate', [AccountDashboardController::class, 'proPropertyValidate'])->name('dashboard.olives-pro.properties.validate');
    Route::get('/olives-pro/businesses', [AccountDashboardController::class, 'proBusinesses'])->name('dashboard.olives-pro.businesses');
    Route::post('/olives-pro/businesses', [AccountDashboardController::class, 'proBusinessStore'])->name('dashboard.olives-pro.businesses.store');
    Route::post('/olives-pro/businesses/{business}', [AccountDashboardController::class, 'proBusinessUpdate'])->name('dashboard.olives-pro.businesses.update');
    Route::get('/olives-pro/bills', [AccountDashboardController::class, 'proBills'])->name('dashboard.olives-pro.bills');
    Route::post('/olives-pro/bills/property-rates/generate', [AccountDashboardController::class, 'proPropertyRateBillsGenerate'])->name('dashboard.olives-pro.bills.property-rates.generate');
    Route::get('/olives-pro/payments', [AccountDashboardController::class, 'proPayments'])->name('dashboard.olives-pro.payments');
    Route::post('/olives-pro/payment-links', [AccountDashboardController::class, 'proPaymentLinkStore'])->name('dashboard.olives-pro.payment-links.store');
    Route::get('/olives-pro/laws-fees', [AccountDashboardController::class, 'proLawsFees'])->name('dashboard.olives-pro.laws-fees');
    Route::get('/olives-pro/citizens', [AccountDashboardController::class, 'proCitizens'])->name('dashboard.olives-pro.citizens');
    Route::get('/olives-pro/users', [AccountDashboardController::class, 'proUsers'])->name('dashboard.olives-pro.users');
    Route::post('/olives-pro/users/agents', [AccountDashboardController::class, 'proAgentStore'])->name('dashboard.olives-pro.agents.store');
    Route::get('/olives-pro/reports', [AccountDashboardController::class, 'proReports'])->name('dashboard.olives-pro.reports');
    Route::get('/olives-pro/integrations', [AccountDashboardController::class, 'proIntegrations'])->name('dashboard.olives-pro.integrations');
    Route::post('/olives-pro/integrations', [AccountDashboardController::class, 'proIntegrationStore'])->name('dashboard.olives-pro.integrations.store');
    Route::get('/olives-pro/profile', [AccountDashboardController::class, 'proProfile'])->name('dashboard.olives-pro.profile');
    Route::post('/olives-pro/profile', [AccountDashboardController::class, 'proProfileUpdate'])->name('dashboard.olives-pro.profile.update');
    Route::get('/olives-pro/waste-companies', [Dashboard\WasteManagementController::class, 'proIndex'])->name('dashboard.olives-pro.waste-companies');
    Route::post('/olives-pro/waste-companies', [Dashboard\WasteManagementController::class, 'storeCompany'])->name('dashboard.olives-pro.waste-companies.store');
    Route::post('/olives-pro/waste-companies/{company}/plans', [Dashboard\WasteManagementController::class, 'storePlan'])->name('dashboard.olives-pro.waste-companies.plans.store');
    Route::get('/olives-sky', [AccountDashboardController::class, 'sky'])->name('dashboard.olives-sky');
    Route::get('/olives-cloud', [AccountDashboardController::class, 'cloud'])->name('dashboard.olives-cloud');
    Route::get('/olives-shell', [AccountDashboardController::class, 'shell'])->name('dashboard.olives-shell');
    Route::get('/operational', [Dashboard\DashboardController::class, 'operational'])->name('dashboard.operational');
    Route::get('/MyBills', [Dashboard\DashboardController::class, 'Mybills'])->name('dashboard.mybills');
    Route::get('/myprofile', [Dashboard\DashboardController::class, 'myProfile'])->name('dashboard.myprofile');
    Route::post('/myprofile/{citizen}', [Dashboard\DashboardController::class, 'updateMyProfile'])->name('dashboard.updateMyProfile');
    Route::get('/myproperties', [Dashboard\DashboardController::class, 'Myproperties'])->name('dashboard.myproperties');
    Route::get('/mybusiness', [Dashboard\DashboardController::class, 'Mybusiness'])->name('dashboard.mybusiness');
    Route::get('/mypaymenthistory', [Dashboard\DashboardController::class, 'Mypaymenthistory'])->name('dashboard.mypaymenthistory');
    Route::get('/property/analytics', [Dashboard\DashboardController::class, 'propertyAnalytic'])->name('dashboard.propertyAnalytic');
    Route::get('/nationwide/overview', [Dashboard\DashboardController::class, 'overview'])->name('dashboard.overview');
    Route::get('/payment/analytics', [Dashboard\DashboardController::class, 'paymentAnalytic'])->name('dashboard.paymentAnalytic');
    Route::get('/bill/analytics', [Dashboard\DashboardController::class, 'billAnalytic'])->name('dashboard.billAnalytic');
    Route::get('/fetch-regional-data', [Dashboard\DashboardController::class, 'fetchRegionalData'])->name('fetch.regional.data');
    Route::get('/fetch-regional-graph-data', [Dashboard\DashboardController::class, 'fetchRegionalGraphData'])->name('fetch.regional.graph.data');
    Route::get('/fetch-regional-donut-data', [Dashboard\DashboardController::class, 'fetchRegionalDonutData'])->name('fetch.regional.donut.data');
});

// USER MANAGEMENT
Route::group(['prefix' => 'user', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [User\UserController::class, 'index'])->name('users.index');
    Route::get('/create', [User\UserController::class, 'create'])->name('users.create');
    Route::post('/', [User\UserController::class, 'store'])->name('users.store');
    Route::get('/show/{user}', [User\UserController::class, 'show'])->name('users.show');
    Route::get('/edit/{user}', [User\UserController::class, 'edit'])->name('users.edit');
    Route::put('/update/{user}', [User\UserController::class, 'update'])->name('users.update');
});

// ROLES MANAGEMENT
Route::group(['prefix' => 'role', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [Role\RoleController::class, 'index'])->name('roles.index');
    Route::get('/create', [Role\RoleController::class, 'create'])->name('roles.create');
    Route::post('/', [Role\RoleController::class, 'store'])->name('roles.store');
    Route::get('/show/{role}', [Role\RoleController::class, 'show'])->name('roles.show');
    Route::get('/edit/{role}', [Role\RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/update/{role}', [Role\RoleController::class, 'update'])->name('roles.update');
    Route::patch('/{role}/activate', [Role\RoleController::class, 'activate'])->name('roles.activate');
});

// PERMISSIONS MANAGEMENT
// Route::group(['prefix' => 'permission', 'middleware' => 'auth:sanctum'], function () {
//    Route::get('/', [Permission\PermissionController::class, 'index'])->name('permissions.index');
//    Route::get('/create', [Permission\PermissionController::class, 'create'])->name('permissions.create');
//    Route::post('/', [Permission\PermissionController::class, 'store'])->name('permissions.store');
//    Route::get('/show/{permission}', [Permission\PermissionController::class, 'show'])->name('permissions.show');
//    Route::get('/edit/{permission}', [Permission\PermissionController::class, 'edit'])->name('permissions.edit');
//    Route::post('/update/{permission}', [Permission\PermissionController::class, 'update'])->name('permissions.update');
// });

// assemblies Route
Route::group(['prefix' => 'assembly', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [AssemblyController::class, 'index'])->name('assembly.index');
    Route::get('/create', [AssemblyController::class, 'create'])->name('assembly.create');
    Route::post('/create', [AssemblyController::class, 'store'])->name('assembly.store');
    Route::get('/{assembly}/edit', [AssemblyController::class, 'edit'])->name('assembly.edit');
    Route::put('/{assembly}', [AssemblyController::class, 'update'])->name('assembly.update');
    Route::delete('/{assembly}', [AssemblyController::class, 'destroy'])->name('assembly.destroy');
    Route::get('/{assembly}', [AssemblyController::class, 'show'])->name('assembly.show');
    Route::post('/fetch/data', [AssemblyController::class, 'fetchAssembly'])->name('assembly.fetch');
    Route::post('/get/selected/data', [AssemblyController::class, 'getSelectedAssembly'])->name('assembly.getSelectedAssembly');
    Route::get('/import/data', [AssemblyController::class, 'import'])->name('assembly.import');
    Route::post('/import/data', [AssemblyController::class, 'importData'])->name('assembly.importData');
});

// Division Routes
Route::group(['prefix' => 'division', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [DivisionController::class, 'index'])->name('divisions.index');
    Route::get('/create', [DivisionController::class, 'create'])->name('divisions.create');
    Route::post('/', [DivisionController::class, 'store'])->name('divisions.store');
    Route::get('/{division}/edit', [DivisionController::class, 'edit'])->name('divisions.edit');
    Route::put('/{division}', [DivisionController::class, 'update'])->name('divisions.update');
    Route::delete('/{division}', [DivisionController::class, 'destroy'])->name('divisions.destroy');
    Route::get('/{division}', [DivisionController::class, 'show'])->name('divisions.show');
});

Route::group(['prefix' => 'new-division', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [NewDivisionController::class, 'index'])->name('new-divisions.index');
    Route::get('/create', [NewDivisionController::class, 'create'])->name('new-divisions.create');
    Route::post('/create', [NewDivisionController::class, 'store'])->name('new-divisions.store');
    Route::get('/show/{division}', [NewDivisionController::class, 'show'])->name('new-divisions.show');
    Route::get('/edit/{division}', [NewDivisionController::class, 'edit'])->name('new-divisions.edit');
    Route::put('/update/{division}', [NewDivisionController::class, 'update'])->name('new-divisions.update');
    Route::get('/api/divisions/{assemblyCode}', [NewDivisionController::class, 'getDivisionsByAssembly'])->name('new-divisions.api.divisions');
});

// Block Routes
Route::group(['prefix' => 'block', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [BlockController::class, 'index'])->name('blocks.index');
    Route::get('/create', [BlockController::class, 'create'])->name('blocks.create');
    Route::post('/', [BlockController::class, 'store'])->name('blocks.store');
    Route::get('/{block}/edit', [BlockController::class, 'edit'])->name('blocks.edit');
    Route::put('/{block}', [BlockController::class, 'update'])->name('blocks.update');
    Route::delete('/{block}', [BlockController::class, 'destroy'])->name('blocks.destroy');
    Route::post('/division/data', [BlockController::class, 'division'])->name('ajax.division');
    Route::get('/{block}', [BlockController::class, 'show'])->name('blocks.show');
});

Route::group(['prefix' => 'new-block', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [NewBlockController::class, 'index'])->name('new-blocks.index');
    Route::get('/create', [NewBlockController::class, 'create'])->name('new-blocks.create');
    Route::post('/create', [NewBlockController::class, 'store'])->name('new-blocks.store');
    Route::get('/show/{block}', [NewBlockController::class, 'show'])->name('new-blocks.show');
    Route::get('/edit/{block}', [NewBlockController::class, 'edit'])->name('new-blocks.edit');
    Route::put('/update/{block}', [NewBlockController::class, 'update'])->name('new-blocks.update');
    Route::get('/api/blocks/{divisionId}', [NewBlockController::class, 'getBlocksByDivision'])->name('new-blocks.api.blocks');
});

// Zone Routes
Route::group(['prefix' => 'zone', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [ZoneController::class, 'index'])->name('zones.index');
    Route::get('/create', [ZoneController::class, 'create'])->name('zones.create');
    Route::post('/create', [ZoneController::class, 'store'])->name('zones.store');
    Route::get('/{zone}/show', [ZoneController::class, 'show'])->name('zones.show');
    Route::get('/{zone}/edit', [ZoneController::class, 'edit'])->name('zones.edit');
    Route::post('/{zone}/update', [ZoneController::class, 'update'])->name('zones.update');
});

Route::group(['prefix' => 'property-user', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [PropertyUserController::class, 'index'])->name('property-users.index');
    Route::get('/create', [PropertyUserController::class, 'create'])->name('property-users.create');
    Route::post('/create', [PropertyUserController::class, 'store'])->name('property-users.store');
    Route::get('/{propertyUser}/show', [PropertyUserController::class, 'show'])->name('property-users.show');
    Route::get('/{propertyUser}/edit', [PropertyUserController::class, 'edit'])->name('property-users.edit');
    Route::post('/{propertyUser}/update', [PropertyUserController::class, 'update'])->name('property-users.update');
});

Route::group(['prefix' => 'rate', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [RateController::class, 'index'])->name('rates.index');
    Route::get('/create', [RateController::class, 'create'])->name('rates.create');
    Route::post('/create', [RateController::class, 'store'])->name('rates.store');
    Route::get('/{rate}/show', [RateController::class, 'show'])->name('rates.show');
    Route::get('/{rate}/edit', [RateController::class, 'edit'])->name('rates.edit');
    Route::post('/{rate}/update', [RateController::class, 'update'])->name('rates.update');
    Route::post('/property-use', [RateController::class, 'propertyUse'])->name('rates.property-use');
    Route::post('/zone/data', [RateController::class, 'zone'])->name('rates.zone');
    Route::get('/import/data', [RateController::class, 'import'])->name('rates.import');
    Route::post('/import/data', [RateController::class, 'importData'])->name('rates.importData');
    Route::get('/template', [RateController::class, 'downloadTemplate'])->name('rates.downloadTemplate');
});

Route::group(['prefix' => 'bus-rate', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [BusRateController::class, 'index'])->name('rates.bus.index');
    Route::get('/create', [BusRateController::class, 'create'])->name('rates.bus.create');
    Route::post('/create', [BusRateController::class, 'store'])->name('rates.bus.store');
    Route::get('/{rate}/show', [BusRateController::class, 'show'])->name('rates.bus.show');
    Route::get('/{rate}/edit', [BusRateController::class, 'edit'])->name('rates.bus.edit');
    Route::post('/{rate}/update', [BusRateController::class, 'update'])->name('rates.bus.update');
    Route::get('/import/data', [BusRateController::class, 'import'])->name('rates.bus.import');
    Route::post('/import/data', [BusRateController::class, 'importData'])->name('rates.bus.importData');
    Route::get('/template', [BusRateController::class, 'downloadTemplate'])->name('rates.bus.downloadTemplate');
});

Route::group(['prefix' => 'report', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/customer-report', [Report\CustomerReportController::class, 'index'])->name('customer-reports.index');
    Route::get('/business-report', [Report\BusinessReportController::class, 'index'])->name('business-reports.index');
    Route::get('/property-report', [Report\PropertyReportController::class, 'index'])->name('property-reports.index');
    Route::get('/bill-report', [Report\BillReportController::class, 'index'])->name('bill-reports.index');
    Route::get('/payment-report', [Report\PaymentReportController::class, 'index'])->name('payment-reports.index');
    Route::get('/debtors-report', [Report\DebtorsReportController::class, 'index'])->name('debtors-reports.index');
    Route::get('/agent-performance-report', [Report\Level10ReportController::class, 'agentPerformanceReport'])->name('agent-performance-reports.index');
    Route::get('/system-performance-report', [Report\Level10ReportController::class, 'systemPerformanceReport'])->name('system-peroformance-reports.index');
    Route::get('/payment-history-report', [Report\PaymentHistoryReportController::class, 'index'])->name('payment-history-reports.index');
    Route::get('/support-request-report', [Report\SupportRequestReportController::class, 'index'])->name('support-request-reports.index');
    Route::get('/tax-collection-summary-report', [Report\TaxCollectionSummaryReportController::class, 'index'])->name('tax-collection-reports.index');
    Route::get('/revenue-property-type-report', [Report\RevenuePropertyTypeReportController::class, 'index'])->name('revenue-property-type-reports.index');
    Route::get('/revenue-collection-efficiency-report', [Report\RevenueCollectionEfficiencyReportController::class, 'index'])->name('revenue-collection-efficiency-reports.index');
    Route::get('/service-usage-report', [Report\ServiceUsageReport::class, 'index'])->name('service-usage-reports.index');
    Route::get('/location-analysis-report', [Report\LocationAnalysisReportController::class, 'index'])->name('location-analysis-reports.index');
    Route::get('/audit-trail-report', [Report\AuditTrailReportController::class, 'index'])->name('audit-trail-reports.index');
    Route::get('/job-allocation-report', [Report\JobAllocationReportController::class, 'index'])->name('job-allocation-reports.index');
});

Route::group(['prefix' => 'agent-assignment', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [AgentAssignmentController::class, 'index'])->name('agent-assignments.index');
    Route::get('/create', [AgentAssignmentController::class, 'create'])->name('agent-assignments.create');
    Route::post('/create', [AgentAssignmentController::class, 'store'])->name('agent-assignments.store');
    Route::get('/{agentAssignment}/show', [AgentAssignmentController::class, 'show'])->name('agent-assignments.show');
    Route::get('/{agentAssignment}/edit', [AgentAssignmentController::class, 'edit'])->name('agent-assignments.edit');
    Route::post('/{agentAssignment}/update', [AgentAssignmentController::class, 'update'])->name('agent-assignments.update');
});

Route::group(['prefix' => 'task-assignment', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [TaskAssignmentController::class, 'index'])->name('task-assignments.index');
    Route::get('/create', [TaskAssignmentController::class, 'create'])->name('task-assignments.create');
    Route::post('/create', [TaskAssignmentController::class, 'store'])->name('task-assignments.store');
    Route::get('/{taskAssignment}/show', [TaskAssignmentController::class, 'show'])->name('task-assignments.show');
    Route::get('/{taskAssignment}/edit', [TaskAssignmentController::class, 'edit'])->name('task-assignments.edit');
    Route::post('/{taskAssignment}/update', [TaskAssignmentController::class, 'update'])->name('task-assignments.update');
    Route::get('/update/status', [TaskAssignmentController::class, 'updateStatus'])->name('task-assignments.updateStatus');
});

Route::group(['prefix' => 'customer-support', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [CustomerSupportController::class, 'index'])->name('customer-supports.index');
    Route::get('/create', [CustomerSupportController::class, 'create'])->name('customer-supports.create');
    Route::post('/create', [CustomerSupportController::class, 'store'])->name('customer-supports.store');
    Route::get('/show/{customerSupport}', [CustomerSupportController::class, 'show'])->name('customer-supports.show');
    Route::get('/edit/{customerSupport}', [CustomerSupportController::class, 'edit'])->name('customer-supports.edit');
    Route::post('/update/{customerSupport}', [CustomerSupportController::class, 'update'])->name('customer-supports.update');
});

Route::group(['prefix' => 'building', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [BuildingController::class, 'index'])->name('buildings.index');
    Route::get('/create', [BuildingController::class, 'create'])->name('buildings.create');
    Route::post('/create', [BuildingController::class, 'store'])->name('buildings.store');
    Route::get('/polygons', [BuildingController::class, 'polygons'])->name('buildings.polygons');
    Route::get('/map', [BuildingController::class, 'map'])->name('buildings.map');
    Route::get('/import', [BuildingController::class, 'importBlock'])->name('buildings.importBlock');
    Route::post('/import', [BuildingController::class, 'importBlockStore'])->name('buildings.importBlockStore');
    Route::get('/allocations/{uuid}', [BuildingController::class, 'allocations'])->name('buildings.allocations');
});

Route::group(['prefix' => 'mmda', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [MmdaController::class, 'index'])->name('mmdas.index');
    Route::get('/create', [MmdaController::class, 'create'])->name('mmdas.create');
    Route::post('/create', [MmdaController::class, 'store'])->name('mmdas.store');
    Route::get('/show/{mmda}', [MmdaController::class, 'show'])->name('mmdas.show');
    Route::get('/edit/{mmda}', [MmdaController::class, 'edit'])->name('mmdas.edit');
    Route::post('/update/{mmda}', [MmdaController::class, 'update'])->name('mmdas.update');
    Route::get('/import/data', [MmdaController::class, 'import'])->name('mmdas.import');
    Route::post('/import/data', [MmdaController::class, 'importData'])->name('mmdas.importData');
});

Route::group(['prefix' => 'property-revenue', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [PropertyRevenueController::class, 'index'])->name('property-revenues.index');
    Route::get('/create', [PropertyRevenueController::class, 'create'])->name('property-revenues.create');
    Route::post('/create', [PropertyRevenueController::class, 'store'])->name('property-revenues.store');
    Route::get('/show/{propertyRevenue}', [PropertyRevenueController::class, 'show'])->name('property-revenues.show');
    Route::get('/edit/{propertyRevenue}', [PropertyRevenueController::class, 'edit'])->name('property-revenues.edit');
    Route::post('/update/{propertyRevenue}', [PropertyRevenueController::class, 'update'])->name('property-revenues.update');
});

Route::group(['prefix' => 'revenue-stream-type', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [RevenueStreamTypeController::class, 'index'])->name('revenue-stream-types.index');
    Route::get('/create', [RevenueStreamTypeController::class, 'create'])->name('revenue-stream-types.create');
    Route::post('/create', [RevenueStreamTypeController::class, 'store'])->name('revenue-stream-types.store');
    Route::get('/show/{revenueStreamType}', [RevenueStreamTypeController::class, 'show'])->name('revenue-stream-types.show');
    Route::get('/edit/{revenueStreamType}', [RevenueStreamTypeController::class, 'edit'])->name('revenue-stream-types.edit');
    Route::post('/update/{revenueStreamType}', [RevenueStreamTypeController::class, 'update'])->name('revenue-stream-types.update');
});

Route::group(['prefix' => 'property-classes', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [PropertyClassesController::class, 'index'])->name('property-classes.index');
    Route::get('/create', [PropertyClassesController::class, 'create'])->name('property-classes.create');
    Route::post('/create', [PropertyClassesController::class, 'store'])->name('property-classes.store');
    Route::get('/show/{propertyClass}', [PropertyClassesController::class, 'show'])->name('property-classes.show');
    Route::get('/edit/{propertyClass}', [PropertyClassesController::class, 'edit'])->name('property-classes.edit');
    Route::post('/update/{propertyClass}', [PropertyClassesController::class, 'update'])->name('property-classes.update');
});

Route::group(['prefix' => 'revenue-stream', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [RevenueStreamController::class, 'index'])->name('revenue-streams.index');
    Route::get('/create', [RevenueStreamController::class, 'create'])->name('revenue-streams.create');
    Route::post('/create', [RevenueStreamController::class, 'store'])->name('revenue-streams.store');
    Route::get('/show/{revenueStream}', [RevenueStreamController::class, 'show'])->name('revenue-streams.show');
    Route::get('/edit/{revenueStream}', [RevenueStreamController::class, 'edit'])->name('revenue-streams.edit');
    Route::post('/update/{revenueStream}', [RevenueStreamController::class, 'update'])->name('revenue-streams.update');
    Route::get('/import/data', [RevenueStreamController::class, 'import'])->name('revenue-streams.import');
    Route::post('/import/data', [RevenueStreamController::class, 'importData'])->name('revenue-streams.importData');
    Route::get('/template', [RevenueStreamController::class, 'downloadTemplate'])->name('revenue-streams.downloadTemplate');

    Route::get('/bop', [RevenueStreamController::class, 'indexBOP'])->name('revenue-streams.indexBOP');
    Route::get('/bop/create', [RevenueStreamController::class, 'createBOP'])->name('revenue-streams.createBOP');
    Route::post('/bop/create', [RevenueStreamController::class, 'storeBOP'])->name('revenue-streams.storeBOP');
    Route::get('/bop/show/{revenueStream}', [RevenueStreamController::class, 'showBOP'])->name('revenue-streams.showBOP');
    Route::get('/bop/edit/{revenueStream}', [RevenueStreamController::class, 'editBOP'])->name('revenue-streams.editBOP');
    Route::post('/bop/update/{revenueStream}', [RevenueStreamController::class, 'updateBOP'])->name('revenue-streams.updateBOP');
    Route::get('/bop/import/data', [RevenueStreamController::class, 'importBOP'])->name('revenue-streams.importBOP');
    Route::post('/bop/import/data', [RevenueStreamController::class, 'importBOPData'])->name('revenue-streams.importBOPData');
    Route::get('/bop/template', [RevenueStreamController::class, 'downloadBOPTemplate'])->name('revenue-streams.downloadBOPTemplate');
});

Route::group(['prefix' => 'property-type', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [PropertyTypeController::class, 'index'])->name('property-types.index');
    Route::get('/create', [PropertyTypeController::class, 'create'])->name('property-types.create');
    Route::post('/create', [PropertyTypeController::class, 'store'])->name('property-types.store');
    Route::get('/show/{propertyType}', [PropertyTypeController::class, 'show'])->name('property-types.show');
    Route::get('/edit/{propertyType}', [PropertyTypeController::class, 'edit'])->name('property-types.edit');
    Route::post('/update/{propertyType}', [PropertyTypeController::class, 'update'])->name('property-types.update');
});

Route::group(['prefix' => 'category', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/create', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/show/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::get('/edit/{category}', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::post('/update/{category}', [CategoryController::class, 'update'])->name('categories.update');
});

Route::group(['prefix' => 'item-description', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [ItemDescriptionController::class, 'index'])->name('item-descriptions.index');
    Route::get('/create', [ItemDescriptionController::class, 'create'])->name('item-descriptions.create');
    Route::post('/create', [ItemDescriptionController::class, 'store'])->name('item-descriptions.store');
    Route::get('/show/{itemDescription}', [ItemDescriptionController::class, 'show'])->name('item-descriptions.show');
    Route::get('/edit/{itemDescription}', [ItemDescriptionController::class, 'edit'])->name('item-descriptions.edit');
    Route::post('/update/{itemDescription}', [ItemDescriptionController::class, 'update'])->name('item-descriptions.update');
});

Route::group(['prefix' => 'bill-adjustment', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [BillAdjustmentController::class, 'index'])->name('bill-adjustments.index');
    Route::get('/create', [BillAdjustmentController::class, 'create'])->name('bill-adjustments.create');
    Route::post('/create', [BillAdjustmentController::class, 'store'])->name('bill-adjustments.store');
    Route::get('/show/{billAdjustment}', [BillAdjustmentController::class, 'show'])->name('bill-adjustments.show');
    Route::get('/edit/{billAdjustment}', [BillAdjustmentController::class, 'edit'])->name('bill-adjustments.edit');
    Route::post('/update/{billAdjustment}', [BillAdjustmentController::class, 'update'])->name('bill-adjustments.update');
});

Route::group(['prefix' => 'main-item', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [MainItemController::class, 'index'])->name('main-items.index');
    Route::get('/create', [MainItemController::class, 'create'])->name('main-items.create');
    Route::post('/create', [MainItemController::class, 'store'])->name('main-items.store');
    Route::get('/show/{mainItem}', [MainItemController::class, 'show'])->name('main-items.show');
    Route::get('/edit/{mainItem}', [MainItemController::class, 'edit'])->name('main-items.edit');
    Route::post('/update/{mainItem}', [MainItemController::class, 'update'])->name('main-items.update');
});

Route::group(['prefix' => 'sub-item', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [SubItemController::class, 'index'])->name('sub-items.index');
    Route::get('/create', [SubItemController::class, 'create'])->name('sub-items.create');
    Route::post('/create', [SubItemController::class, 'store'])->name('sub-items.store');
    Route::get('/show/{subItem}', [SubItemController::class, 'show'])->name('sub-items.show');
    Route::get('/edit/{subItem}', [SubItemController::class, 'edit'])->name('sub-items.edit');
    Route::post('/update/{subItem}', [SubItemController::class, 'update'])->name('sub-items.update');
});

Route::get('bills/{bill}', [BillsManagementController::class, 'publicShow'])
    ->name('admin.public.bills.show');

Route::post('bills/{bill}', [BillsManagementController::class, 'publicShow'])
    ->domain(config('erms.website'))->name('public.bills.show');

Route::post('bills/{bill}/payment/initialize', [BillsManagementController::class, 'initializePublicPayment'])->name('public.bills.payment.initialize');
Route::get('payment/public/verify', [PaymentController::class, 'publicVerify'])->name('public.payments.verify');
