<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/main', function () {
//     $data['cookie'] = cookie('name') ? cookie('name') : null;
//     return view('welcome', $data);
// })->name('main');

// Route::get('/main', function () {

// });

Route::get('/location', [App\Http\Controllers\Admin\PointController::class, 'location']);


// Route::get('/ip', function (Request $request) {
//     $data['cookie'] = $request->cookie('name');
//     return view('cookie', $data);
// })->name('ip');

// Route::post('/cookie', function (Request $request) {
//     Cookie::forget('name');
//     $cookie = Cookie::make('name', $request->cookie, 60);
//     return redirect()->route('ip')->cookie($cookie);
// })->name('cookie.store');

// Route::get('/cookie-delete', function (Request $request) {

//     $cookie = Cookie::forget('name');
//     return redirect()->route('main');
// })->name('cookie.delete');



// Super Routes ----------------------------------------------------------------------------------------
Route::get('/super/login', [App\Http\Controllers\Super\Auth\LoginController::class, 'login'])->name('super.login');
Route::post('/super/login', [App\Http\Controllers\Super\Auth\LoginController::class, 'loginPost'])->name('super.login.post');
Route::get('/super/logout', [App\Http\Controllers\Super\Auth\LoginController::class, 'logout'])->name('super.logout')->middleware('auth');

Route::group(['prefix' => '/super', 'as' => 'super.', 'middleware' => ['auth', 'super']], function () {
    Route::get('/dashboard', [App\Http\Controllers\Super\DashboardController::class, 'dashboard'])->name('dashboard');
    
    Route::group(['prefix' => '/admin', 'as' => 'admin.'], function () {
        Route::get("/",[App\Http\Controllers\Super\AdminController::class, "index"])->name("list");
        Route::post("/create",[App\Http\Controllers\Super\AdminController::class, "store"])->name("store");
    });
}); 

// Admin Routes ----------------------------------------------------------------------------------------
Route::get('/admin/login', [App\Http\Controllers\Admin\Auth\LoginController::class, 'login'])->name('admin.login');
Route::post('/admin/login', [App\Http\Controllers\Admin\Auth\LoginController::class, 'loginPost'])->name('admin.login.post');
Route::get('/admin/logout', [App\Http\Controllers\Admin\Auth\LoginController::class, 'logout'])->name('admin.logout');

Route::group(['prefix' => '/install', 'as' => 'install.'],function(){
    Route::get("/",[App\Http\Controllers\Admin\InstallController::class, 'install'])->name('setup');
    Route::post("/store",[App\Http\Controllers\Admin\InstallController::class, 'store'])->name('store');
    Route::post("/pincode",[App\Http\Controllers\Admin\InstallController::class, 'createPin'])->name('pincode');
    Route::get("/reading",[App\Http\Controllers\Admin\InstallController::class, 'readingStore'])->name('reading');
    Route::post("/reading",[App\Http\Controllers\Admin\InstallController::class, 'readingSave'])->name('reading.store');
});

Route::group(['prefix' => '/admin', 'as' => 'admin.', 'middleware' => ['admin']], function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get("/room/{id}", [App\Http\Controllers\Admin\DashboardController::class, 'switchRoom'])->name('room');
    
    Route::group(['prefix' => '/reading', 'as' => 'reading.'],function(){
        Route::get("/",[App\Http\Controllers\Admin\ReadingController::class, 'index'])->name('list');
        Route::post("/in",[App\Http\Controllers\Admin\ReadingController::class, 'readingIn'])->name('in');
        Route::post("/out",[App\Http\Controllers\Admin\ReadingController::class, 'readingOut'])->name('out');
    });
    
    Route::group(['prefix' => '/report', 'as' => 'report.'],function(){
        Route::get("/", [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('list');

        // STAFF REPORT
        Route::get("/staff",[App\Http\Controllers\Admin\ReportController::class, 'staff'])->name('staff');
        Route::post("/staff/point", [App\Http\Controllers\Admin\ReportController::class, 'staffPointCompress'])->name('staff.point');
        Route::post("/staff/transaction", [App\Http\Controllers\Admin\ReportController::class, 'staffTransactionCompress'])->name('staff.transaction');
    });

    Route::group(['prefix' => '/pdf', 'as' => 'pdf.'],function(){
        Route::post("/reading",[App\Http\Controllers\Admin\PDFController::class, 'reading'])->name('reading');
    });
    
    Route::group(['prefix' => '/staff', 'as' => 'staff.'],function(){
        Route::get("/",[App\Http\Controllers\Admin\StaffController::class, 'index'])->name('list');
        Route::post("/store",[App\Http\Controllers\Admin\StaffController::class, 'staffStore'])->name('store');
        Route::post("/common_store",[App\Http\Controllers\Admin\StaffController::class, 'staffCommonStore'])->name('common.store');
        Route::get("/detail", [App\Http\Controllers\Admin\StaffController::class, 'staffDetail']);
        Route::post("/permission_edit",[App\Http\Controllers\Admin\StaffController::class, 'staffPermissionEdit'])->name('permission_edit');
    });

    Route::group(['prefix' => '/machine', 'as' => 'machine.'],function(){
        Route::get("/",[App\Http\Controllers\Admin\MachineController::class, 'index'])->name('list');
        Route::post("/info",[App\Http\Controllers\Admin\MachineController::class, 'ajaxMachineInfo']);
        Route::post("/edit",[App\Http\Controllers\Admin\MachineController::class, 'editMachine'])->name('edit');
        Route::post("/store",[App\Http\Controllers\Admin\MachineController::class, 'storeMachine'])->name('store');
        
        Route::get("/active/{id}",[App\Http\Controllers\Admin\MachineController::class, 'activeMachine'])->name('active');
        Route::get("/block/{id}",[App\Http\Controllers\Admin\MachineController::class, 'blockMachine'])->name('block');
    });

    Route::group(['prefix' => '/player', 'as' => 'player.'],function(){

        Route::get("/list",[App\Http\Controllers\Admin\PlayerController::class, 'index'])->name('list');
        Route::post("/history",[App\Http\Controllers\Admin\PlayerController::class, 'playerHistory'])->name('history');

        Route::post("/create",[App\Http\Controllers\Admin\PlayerController::class, 'storePlayer'])->name('store');
        Route::get("/face/{id}",[App\Http\Controllers\Admin\PlayerController::class, 'facePlayer'])->name('face');
        Route::post("/face",[App\Http\Controllers\Admin\PlayerController::class, 'faceStorePlayer'])->name('face.store');
        Route::get("/detail", [App\Http\Controllers\Admin\PlayerController::class, 'PlayerDetail']);
        Route::get("/status/{id}", [App\Http\Controllers\Admin\PlayerController::class, 'PlayerStatus'])->name('status');

        // Player Face Match
        Route::get('/ajaxplayers', [App\Http\Controllers\Admin\PlayerController::class, 'ajaxplayers']);
        Route::post("/ajax_profile_match", [App\Http\Controllers\Admin\PlayerController::class, 'ajax_profile_match']);

        Route::get("/ajaxref",[App\Http\Controllers\Admin\PlayerController::class, 'PlayerRef']);
        Route::get("/search",[App\Http\Controllers\Admin\PlayerController::class, 'searchPlayer']);
    });

    Route::group(['prefix' => '/point', 'as' => 'point.'], function(){
        Route::post("/create", [App\Http\Controllers\Admin\PointController::class, 'PlayerPointStore'])->name('store');
        Route::post("/checkin", [App\Http\Controllers\Admin\PointController::class, 'PlayerPointCheckIn'])->name('checkin');
        Route::post("/form", [App\Http\Controllers\Admin\PointController::class, 'PlayerPointStoreForm'])->name('store.form');
        Route::post("/checkout", [App\Http\Controllers\Admin\PointController::class, 'PlayerPointCheckout'])->name('checkout');

        Route::get("/image", [App\Http\Controllers\Admin\PointController::class, 'pointImage'])->name('image');
    });

    Route::group(['prefix' => '/ticket', 'as' => 'ticket.'],function(){
        Route::get("/", [App\Http\Controllers\Admin\TicketController::class, 'index'])->name('list');
        Route::post("/create", [App\Http\Controllers\Admin\TicketController::class, 'ticketStore'])->name('store');
    });

    Route::group(['prefix' => '/bank', 'as' => 'bank.'],function(){
        Route::get("/", [App\Http\Controllers\Admin\BankController::class, 'index'])->name('list');
        Route::post("/create", [App\Http\Controllers\Admin\BankController::class, 'store'])->name('store');
    });

    Route::group(['prefix' => '/transaction', 'as' => 'transaction.'],function(){
        Route::get("/", [App\Http\Controllers\Admin\TransactionController::class, 'index'])->name('list');
        Route::post("/create", [App\Http\Controllers\Admin\TransactionController::class, 'store'])->name('store');
    });

    Route::group(['prefix' => '/setting', 'as' => 'setting.'],function(){
        Route::get("/", [App\Http\Controllers\Admin\SettingController::class, 'general'])->name('general');
        Route::post("/general", [App\Http\Controllers\Admin\SettingController::class, 'generalSetting'])->name('general.store');
        Route::get("/point", [App\Http\Controllers\Admin\SettingController::class, 'point'])->name('point');
        Route::post("/point", [App\Http\Controllers\Admin\SettingController::class, 'pointSetting'])->name('point.store');
        Route::get("/ticket", [App\Http\Controllers\Admin\SettingController::class, 'ticket'])->name('ticket');
        Route::post("/ticket", [App\Http\Controllers\Admin\SettingController::class, 'ticketSetting'])->name('ticket.store');
    });

    Route::group(['prefix' => '/payroll', 'as' => 'payroll.'],function(){
        Route::get("/staff_list", [App\Http\Controllers\Admin\PayrollController::class, 'index'])->name('list');
        Route::get("/detail", [App\Http\Controllers\Admin\PayrollController::class, 'staffDetail']);
        Route::get("/status/{id}", [App\Http\Controllers\Admin\PayrollController::class, 'staffPaymentStatus'])->name('status');
        Route::get("/checkin", [App\Http\Controllers\Admin\PayrollController::class, 'staffCheckin'])->name('checkin');
        Route::get("/checkout", [App\Http\Controllers\Admin\PayrollController::class, 'staffCheckout'])->name('checkout');
    });

});

// Staff Routes ----------------------------------------------------------------------------------------
Route::get('/staff/login', [App\Http\Controllers\Staff\Auth\LoginController::class, 'login'])->name('staff.login');
Route::post('/staff/login', [App\Http\Controllers\Staff\Auth\LoginController::class, 'loginPost'])->name('staff.login.post');
Route::get('/logout', [App\Http\Controllers\Staff\Auth\LoginController::class, 'logout'])->name('staff.logout')->middleware('auth');

Route::group(['prefix' => '/staff', 'as' => 'staff.', 'middleware' => ['auth', 'staff']], function () {
    Route::get('/dashboard', [App\Http\Controllers\Staff\DashboardController::class, 'dashboard'])->name('dashboard');

    Route::group(['prefix' => '/reading', 'as' => 'reading.'],function(){
        Route::get("/",[App\Http\Controllers\Staff\ReadingController::class, 'index'])->name('list');
        Route::post("/in",[App\Http\Controllers\Staff\ReadingController::class, 'readingIn'])->name('in');
        Route::post("/out",[App\Http\Controllers\Staff\ReadingController::class, 'readingOut'])->name('out');
    });

    Route::group(['prefix' => '/player', 'as' => 'player.'],function(){
        
        Route::get("/list",[App\Http\Controllers\Staff\PlayerController::class, 'index'])->name('list');
        
        // New Player Add
        Route::post("/create",[App\Http\Controllers\Staff\PlayerController::class, 'storePlayer'])->name('store');
        Route::get("/face/{id}",[App\Http\Controllers\Staff\PlayerController::class, 'facePlayer'])->name('face');
        Route::post("/face",[App\Http\Controllers\Staff\PlayerController::class, 'faceStorePlayer'])->name('face.store');
        Route::get("/ajaxref",[App\Http\Controllers\Staff\PlayerController::class, 'PlayerRef']);
        Route::get("/search",[App\Http\Controllers\Staff\PlayerController::class, 'searchPlayer']);

        // Player Face Detection
        Route::get('/ajaxplayers', [App\Http\Controllers\Staff\PlayerController::class, 'ajaxplayers']);
        Route::post("/ajax_profile_match", [App\Http\Controllers\Staff\PlayerController::class, 'ajax_profile_match']);

        // Route::post("/playerpoints", [PlayerController::class, 'playerPoints'])->name('add.point');
        Route::get("/detail", [App\Http\Controllers\Staff\PlayerController::class, 'PlayerDetail']);
        Route::get("/status/{id}", [App\Http\Controllers\Staff\PlayerController::class, 'PlayerStatus'])->name('status');
    });

    Route::group(['prefix' => '/point', 'as' => 'point.'],function(){
        Route::post("/create", [App\Http\Controllers\Staff\PointController::class, 'PlayerPointStore'])->name('store');
        Route::post("/form", [App\Http\Controllers\Staff\PointController::class, 'PlayerPointStoreForm'])->name('store.form');
        Route::post("/checkin", [App\Http\Controllers\Staff\PointController::class, 'PlayerPointCheckIn'])->name('checkin');
        Route::post("/checkout", [App\Http\Controllers\Staff\PointController::class, 'PlayerPointCheckout'])->name('checkout');

        Route::get("/image", [App\Http\Controllers\Staff\PointController::class, 'pointImage'])->name('image');

    });

    Route::group(['prefix' => '/ticket', 'as' => 'ticket.'],function(){
        Route::post("/create", [App\Http\Controllers\Staff\TicketController::class, 'ticketStore'])->name('store');
        // Route::post("/checkin", [App\Http\Controllers\Staff\PointController::class, 'PlayerPointCheckIn'])->name('checkin');
    });

    Route::group(['prefix' => '/machine', 'as' => 'machine.'],function(){
        Route::get("/",[App\Http\Controllers\Staff\MachineController::class, 'index'])->name('list');
        Route::post("/info",[App\Http\Controllers\Staff\MachineController::class, 'ajaxMachineInfo']);
        Route::post("/edit",[App\Http\Controllers\Staff\MachineController::class, 'editMachine'])->name('edit');
        Route::post("/store",[App\Http\Controllers\Staff\MachineController::class, 'storeMachine'])->name('store');

        Route::get("/active/{id}",[App\Http\Controllers\Staff\MachineController::class, 'activeMachine'])->name('active');
        Route::get("/block/{id}",[App\Http\Controllers\Staff\MachineController::class, 'blockMachine'])->name('block');
    });

    Route::group(['prefix' => '/staff', 'as' => 'staff.'],function(){
        Route::get("/",[App\Http\Controllers\Staff\StaffController::class, 'index'])->name('list');
        Route::post("/store",[App\Http\Controllers\Staff\StaffController::class, 'staffStore'])->name('store');
        Route::post("/common_store",[App\Http\Controllers\Staff\StaffController::class, 'staffCommonStore'])->name('common.store');
        Route::get("/detail", [App\Http\Controllers\Staff\StaffController::class, 'staffDetail']);
        Route::post("/permission_edit",[App\Http\Controllers\Staff\StaffController::class, 'staffPermissionEdit'])->name('permission_edit');
    });

    Route::group(['prefix' => '/setting', 'as' => 'setting.'],function(){
        Route::get("/", [App\Http\Controllers\Staff\SettingController::class, 'general'])->name('general');
        Route::post("/general", [App\Http\Controllers\Staff\SettingController::class, 'generalSetting'])->name('general.store');
        Route::get("/point", [App\Http\Controllers\Staff\SettingController::class, 'point'])->name('point');
        Route::post("/point", [App\Http\Controllers\Staff\SettingController::class, 'pointSetting'])->name('point.store');
        Route::get("/ticket", [App\Http\Controllers\Staff\SettingController::class, 'ticket'])->name('ticket');
        Route::post("/ticket", [App\Http\Controllers\Staff\SettingController::class, 'ticketSetting'])->name('ticket.store');
    });

    Route::group(['prefix' => '/bank', 'as' => 'bank.'],function(){
        Route::get("/", [App\Http\Controllers\Staff\BankController::class, 'index'])->name('list');
        Route::post("/create", [App\Http\Controllers\Staff\BankController::class, 'store'])->name('store');
    });

    Route::group(['prefix' => '/transaction', 'as' => 'transaction.'],function(){
        Route::get("/", [App\Http\Controllers\Staff\TransactionController::class, 'index'])->name('list');
        Route::post("/create", [App\Http\Controllers\Staff\TransactionController::class, 'store'])->name('store');
    });

    Route::group(['prefix' => '/payroll', 'as' => 'payroll.'],function(){
        Route::post("/checkin_post", [App\Http\Controllers\Staff\PayrollController::class, 'checkin'])->name('checkin');
        Route::post("/checkout_post", [App\Http\Controllers\Staff\PayrollController::class, 'checkout'])->name('checkout');

        Route::get("/staff_list", [App\Http\Controllers\Staff\PayrollController::class, 'index'])->name('list');
        Route::get("/detail", [App\Http\Controllers\Staff\PayrollController::class, 'staffDetail']);
        Route::get("/status/{id}", [App\Http\Controllers\Staff\PayrollController::class, 'staffPaymentStatus'])->name('status');
        Route::get("/checkin", [App\Http\Controllers\Staff\PayrollController::class, 'staffCheckin']);
        Route::get("/checkout", [App\Http\Controllers\Staff\PayrollController::class, 'staffCheckout']);
    });

});
