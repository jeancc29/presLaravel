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

Route::get('/customers', 'CustomerController@index');
Route::get('/customers/search', 'CustomerController@search');
Route::post('/customers/store', 'CustomerController@store');

Route::get('/routes', 'RouteController@index');
Route::post('/routes/store', 'RouteController@store');
Route::post('/routes/delete', 'RouteController@destroy');

Route::get('/expenses', 'ExpenseController@index');
Route::post('/expenses/store', 'ExpenseController@store');
Route::post('/expenses/delete', 'ExpenseController@destroy');

Route::get('/boxes', 'BoxController@index');
Route::post('/boxes/store', 'BoxController@store');
Route::post('/boxes/open', 'BoxController@abrirCaja');
Route::post('/boxes/delete', 'BoxController@destroy');

Route::get('/banks', 'BankController@index');
Route::post('/banks/store', 'BankController@store');
Route::post('/banks/delete', 'BankController@destroy');

Route::get('/accounts', 'AccountController@index');
Route::post('/accounts/store', 'AccountController@store');
Route::post('/accounts/delete', 'AccountController@destroy');

Route::get('/loans', 'LoanController@index');
Route::get('/loans/search', 'LoanController@search');
Route::post('/loans/store', 'LoanController@store');
Route::post('/loans/delete', 'LoanController@destroy');

Route::get('/loansettings', 'LoansettingController@index');
Route::post('/loansettings/store', 'LoansettingController@store');
