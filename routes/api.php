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
Route::post('/customers/store', 'CustomerController@store');

Route::get('/routes', 'RouteController@index');
Route::post('/routes/store', 'RouteController@store');
Route::post('/routes/delete', 'RouteController@destroy');

Route::get('/expenses', 'ExpenseController@index');
Route::post('/expenses/store', 'ExpenseController@store');
Route::post('/expenses/delete', 'ExpenseController@destroy');
