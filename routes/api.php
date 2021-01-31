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

Route::post('/customers', 'CustomerController@index');
Route::get('/customers/search', 'CustomerController@search');
Route::post('/customers/store', 'CustomerController@store');

Route::post('/routes', 'RouteController@index');
Route::post('/routes/store', 'RouteController@store');
Route::post('/routes/delete', 'RouteController@destroy');

Route::post('/expenses', 'ExpenseController@index');
Route::post('/expenses/store', 'ExpenseController@store');
Route::post('/expenses/delete', 'ExpenseController@destroy');

Route::post('/boxes', 'BoxController@index');
Route::post('/boxes/store', 'BoxController@store');
Route::post('/boxes/open', 'BoxController@abrirCaja');
Route::post('/boxes/delete', 'BoxController@destroy');

Route::post('/banks', 'BankController@index');
Route::post('/banks/store', 'BankController@store');
Route::post('/banks/delete', 'BankController@destroy');

Route::post('/accounts', 'AccountController@index');
Route::post('/accounts/store', 'AccountController@store');
Route::post('/accounts/delete', 'AccountController@destroy');

Route::post('/loans', 'LoanController@index');
Route::get('/loans/search', 'LoanController@search');
Route::post('/loans/show', 'LoanController@show');
Route::post('/loans/store', 'LoanController@store');
Route::post('/loans/delete', 'LoanController@destroy');

Route::post('/loansettings', 'LoansettingController@index');
Route::post('/loansettings/store', 'LoansettingController@store');

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
