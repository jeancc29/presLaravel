<?php

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/customers', 'CustomerController@index')->name("customers.index");
Route::post('/customers/indexAdd', 'CustomerController@indexAdd')->name("customers.indexAdd");
Route::get('/customers/search', 'CustomerController@search');
Route::post('/customers/store', 'CustomerController@store')->name("customers.store");
Route::post('/customers/delete', 'CustomerController@destroy')->name("customers.destroy");
Route::post('/customers/testingDatabase', 'CustomerController@testingDatabase')->name("customers.testingDatabase");

Route::post('/routes', 'RouteController@index');
Route::post('/routes/store', 'RouteController@store');
Route::post('/routes/delete', 'RouteController@destroy');

Route::post('/expenses', 'ExpenseController@index');
Route::post('/expenses/store', 'ExpenseController@store')->name("expenses.store");;
Route::post('/expenses/delete', 'ExpenseController@destroy')->name("expenses.destroy");

Route::post('/boxes', 'BoxController@index');
Route::post('/boxes/store', 'BoxController@store');
Route::post('/boxes/open', 'BoxController@abrirCaja');
Route::post('/boxes/adjust', 'BoxController@adjust')->name("boxes.adjust");
Route::post('/boxes/transfer', 'BoxController@transfer')->name("boxes.transfer");
Route::post('/boxes/delete', 'BoxController@destroy');
Route::post('/boxes/indexTransacciones', 'BoxController@indexTransacciones')->name("boxes.indexTransacciones");
Route::post('/boxes/transacciones', 'BoxController@transacciones');
Route::post('/boxes/close', 'BoxController@close');
Route::post('/boxes/showClosure', 'BoxController@showClosure');

Route::post('/banks', 'BankController@index');
Route::post('/banks/store', 'BankController@store');
Route::post('/banks/delete', 'BankController@destroy');

Route::post('/accounts', 'AccountController@index');
Route::post('/accounts/store', 'AccountController@store');
Route::post('/accounts/delete', 'AccountController@destroy');

Route::post('/loans', 'LoanController@index')->name("loans.index");
Route::post('/loans/indexAdd', 'LoanController@indexAdd')->name("loans.indexAdd");
Route::post('/loans/testCustomFirst', 'LoanController@testCustomFirst')->name("loans.testCustomFirst");
Route::get('/loans/search', 'LoanController@search');
Route::post('/loans/show', 'LoanController@show');
Route::post('/loans/store', 'LoanController@store');
Route::post('/loans/delete', 'LoanController@destroy');

Route::post('/loansettings', 'LoansettingController@index');
Route::post('/loansettings/store', 'LoansettingController@store');

Route::post('/receipts', 'ReceiptController@index')->name("receipts.index");
Route::post('/receipts/store', 'ReceiptController@store')->name("receipts.store");


Route::post('/othersettings', 'OthersettingController@index')->name("othersettings.index");
Route::post('/othersettings/store', 'OthersettingController@store')->name("othersettings.store");


Route::post('/roles', 'RoleController@index');
Route::post('/roles/store', 'RoleController@store');
Route::post('/roles/delete', 'RoleController@destroy');

Route::post('/branchoffices', 'BranchofficeController@index');
Route::post('/branchoffices/store', 'BranchofficeController@store');
Route::post('/branchoffices/delete', 'BranchofficeController@destroy');

Route::post('/companies', 'CompanyController@index');
Route::post('/companies/store', 'CompanyController@store');
Route::post('/companies/delete', 'CompanyController@destroy');

Route::post('/users', 'UserController@index');
Route::post('/users/login', 'UserController@login');
Route::post('/users/store', 'UserController@store');
Route::post('/users/delete', 'UserController@destroy');
Route::post('/users/get', 'UserController@get');

Route::post('/pays', 'PayController@index')->name("pays.index");
Route::post('/pays/store', 'PayController@store');
Route::post('/pays/delete', 'PayController@destroy')->name("pays.destroy");

Route::post('/dashboard', 'DashBoardController@index');


Route::post('/guarantees', 'GuaranteeController@index')->name("guarantees.index");
Route::post('/guarantees/store', 'GuaranteeController@store')->name("guarantees.store");
Route::post('/guarantees/delete', 'GuaranteeController@destroy')->name("guarantees.destroy");
