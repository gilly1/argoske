<?php
require_once  "routeArray.php";

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


//make it a multidimentional array with key value pair, so as to pass middleware


// Route::group(['domain' => '{domain}'], function () use ($routes){
Route::group(['domain' => '{subdomain}.' . env('APP_URL_NAME', 'tenancy.test')], function () use ($routes) {

    if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] != env('APP_URL_NAME', 'tenancy.test')) {

        foreach ($routes as $route) {
            $secondValue = explode("/", $route)[1];
            Route::resource($route, 'MainController')->middleware(['auth']);
            Route::post($route . '-import', 'MainController@import')->name($secondValue . '.import');
            Route::get($route . '-sample', 'MainController@sample')->name($secondValue . '.sample');
            Route::get($route . '-export/{id}', 'MainController@export')->name($secondValue . '.export');
        }
    }

    //********************************Start Notification********************************
    Route::namespace('dashboard')->middleware(['auth'])->group(function () {
        Route::get('/', 'dashboardController@dashboard')->name('home');
    });
    Route::namespace('notification')->middleware(['auth'])->group(function () {
        Route::get('/notification', 'NotificationController@unread')->name('notification');
        Route::get('/notification/read', 'NotificationController@read')->name('notification.read');
        Route::get('/notification/all', 'NotificationController@all')->name('notification.all');
        Route::post('/notification', 'NotificationController@markRead')->name('notification');
    });
    Route::namespace('logs')->middleware(['auth'])->group(function () {
        Route::get('/logs', 'LogController@logs')->name('logs');
        Route::get('/notification/userLogs', 'LogController@userLogs')->name('notification.userLogs');
    });
    Route::fallback(function ($subdomain) {
        return view('errors/404')->with('subdomain', $subdomain);
    });
    Route::namespace('CrudGenerator')->middleware(['auth'])->group(function () {
        Route::resource('/main/main', 'Generator');
    });
    Route::namespace('reports')->middleware(['auth'])->group(function () {
        // Route::resource('/reports','dynamicReportController');
        Route::get('/api/reports/{table}', 'dynamicReportController@getTableColumns')->name('reports.getTableColumns');
        Route::post('/api/reports/saveTemplate', 'dynamicReportController@saveTemplate')->name('reports.saveTemplate');
        Route::get('/api/reports/templates/{id}', 'dynamicReportController@templates')->name('reports.templates');
        Route::patch('demos/tasks/{id}', 'dynamicReportController@updateTasksStatus');
        Route::put('demos/tasks/updateAll', 'dynamicReportController@updateTasksOrder');
    });


    Route::namespace('Logic')->middleware(['auth'])->group(function () {
        Route::get('/contracts/contract_group', 'ContractLogic@group')->name('contract_group');
        Route::get('/contracts/contract_year/{year}', 'ContractLogic@year')->name('contract_year');
        Route::post('/contracts/contract_month/import', 'ContractLogic@import_month')->name('import.contract');
        Route::get('/contracts/contract_month/{month}', 'ContractLogic@month')->name('contract_month');
        Route::post('/contracts/contract_month/{month}', 'ContractLogic@month')->name('contract_month');
        Route::get('/contracts/contract_month/export/{type}/{data}/{employer}', 'ContractLogic@export_month')->name('contract_month.export');
    });

    Route::get('/gil2', function () {

        $debtor_transaction = DB::select("
            SELECT trans.*,ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount AS Total,cust.name AS DebtorName, cust.address, cust.debtor_ref, cust.curr_code, cust.tax_id,
		trans.prep_amount>0 as prepaid,com.memo_, shippers.shipper_name, sales_types.sales_type, sales_types.tax_included, branch.*, cust.discount, tax_groups.name AS tax_group_name, tax_groups.id AS tax_group_id  FROM debtor_trans trans
					LEFT JOIN comments com ON trans.type=com.type AND trans.trans_no=com.id
					LEFT JOIN shippers ON shippers.shipper_id=trans.ship_via, 
					debtors_master cust, sales_types, cust_branch branch, tax_groups  
                    WHERE trans.trans_no='1'
		AND trans.type='10'
		AND trans.debtor_no=cust.debtor_no AND 
        trans.debtor_no='1' AND sales_types.id = trans.tpe
			AND branch.branch_code = trans.branch_code
			AND branch.tax_group_id = tax_groups.id");


        return $debtor_transaction;


        $url = 'http://localhost:8000/modules/api/sales/';
        $data =  array(
            'trans_type' => '10',
            'ref' => 'NoGuia0001',
            'customer_id' => '3',
            'branch_id' => '3',
            'location' => 'DEF',
            'deliver_to' => 'ABC, S.A. DE C.V.',
            'delivery_date' => '2022/02/09',
            'delivery_address' => 'Karachi',
            'order_date' => '2022/02/09',
            'phone' => '',
            'cust_ref' => '',
            'comments' => '',
            'ship_via' => '1',
            'payment' => '1',
            'sales_type' => '1',
            'items' => array(
                0 => array(
                    'stock_id' => '101',
                    'description' => 'iPad Air 2 16GB',
                    'qty' => '1',
                    'price' => '300',
                    'discount' => '0'
                ),
                0 => array(
                    'stock_id' => '102',
                    'description' => 'iPhone 6 64GB',
                    'qty' => '1',
                    'price' => '250',
                    'discount' => '0'
                ),
            ),

        );

        $auth = base64_encode('admin:password');
        $options = array(
            'http' => array(
                'header'  => "Authorization: Basic $auth",
                'method'  => 'POST',
                'content' => http_build_query($data),
            )
        );

        $context  = stream_context_create($options);
        dd($context);
        $result = file_get_contents($url, false, $context);
        return ($result);
    });
});

Route::namespace('dashboard')->middleware(['auth'])->group(function () {
    Route::get('/', 'dashboardController@main_dashboard')->name('home');
});

Route::fallback(function () {
    // return  "user may not be authenticated. Return to a login screen";
    return view('errors/404_blank');
});

Auth::routes();

Route::namespace('dashboard')->middleware(['auth'])->group(function () {
    Route::get('/copy', 'dashboardController@copy')->name('copy');
});
// Route::namespace('CrudGenerator')->middleware(['auth'])->group(function(){
//     Route::resource('main','Generator')->middleware(['auth']);
// });


// \Artisan::call('migrate',
//  array(
//    '--path' => 'database/migrations',
//    '--database' => 'dynamicdb',
//    '--force' => true));



//api

Route::namespace('logic')->middleware(['auth'])->group(function () {
    Route::get('/api/mappings', 'ModelMappingLogic@mappings');
    Route::get('/api/mappingApprovers/{id}', 'ModelMappingLogic@mappingApprovers');
});


Route::get('/api/getStock', 'ItemsController@getStock');
Route::get('/api/populateFields/{id}', 'ItemsController@populateFields');
Route::get('/api/populateContractFields/{id}', 'ItemsController@populateContractFields');
