<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('product', 'ProductController@getAll');
Route::get('product/{id}', 'ProductController@get');
Route::post('product', 'ProductController@create');
Route::put('product/{id}', 'ProductController@update');
Route::delete('product/{id}','ProductController@delete');

Route::get('product_dictionaries', 'ProductDictionariesController@getAll');
Route::get('product_dictionaries/{id}', 'ProductDictionariesController@get');
Route::post('product_dictionaries', 'ProductDictionariesController@create');
Route::put('product_dictionaries/{id}', 'ProductDictionariesController@update');
Route::delete('product_dictionaries/{id}','ProductDictionariesController@delete');

Route::get('product_families', 'ProductFamiliesController@getAll');
Route::get('product_families/{id}', 'ProductFamiliesController@get');
Route::post('product_families', 'ProductFamiliesController@create');
Route::put('product_families/{id}', 'ProductFamiliesController@update');
Route::delete('product_families/{id}','ProductFamiliesController@delete');

Route::get('product_generations', 'ProductGenerationsController@getAll');
Route::get('product_generations/{id}', 'ProductGenerationsController@get');
Route::post('product_generations', 'ProductGenerationsController@create');
Route::put('product_generations/{id}', 'ProductGenerationsController@update');
Route::delete('product_generations/{id}','ProductGenerationsController@delete');

Route::get('inquiry', 'InquiryController@getAll');
Route::get('inquiry/where/{where?}', 'InquiryController@getAllWhere');
Route::get('inquiry/{id}', 'InquiryController@get');
Route::post('inquiry', 'InquiryController@create');
Route::put('inquiry/{id}', 'InquiryController@update');
Route::delete('inquiry/{id}','InquiryController@delete');

Route::get('quotation', 'QuotationController@getAll');
Route::get('quotation/where/{where?}', 'QuotationController@getAllWhere');
Route::get('quotation/{id}', 'QuotationController@get');
Route::post('quotation', 'QuotationController@create');
Route::put('quotation/{id}', 'QuotationController@update');
Route::delete('quotation/{id}','QuotationController@delete');

Route::get('order', 'OrderController@getAll');
Route::get('order/{id}', 'OrderController@get');
Route::post('order', 'OrderController@create');
Route::put('order/{id}', 'OrderController@update');
Route::delete('order/{id}','OrderController@delete');

Route::get('contact', 'ContactController@getAll');
Route::get('contact/{id}', 'ContactController@get');
Route::post('contact', 'ContactController@create');
Route::put('contact/{id}', 'ContactController@update');
Route::delete('contact/{id}','ContactController@delete');

Route::get('contractor', 'ContractorController@getAll');
Route::get('contractor/{id}', 'ContractorController@get');
Route::post('contractor', 'ContractorController@create');
Route::put('contractor/{id}', 'ContractorController@update');
Route::delete('contractor/{id}','ContractorController@delete');

Route::get('report/inquiry_by_voivodeship', 'RaportController@InquiryCountByVoivodeship');
Route::get('report/entity_count_by_month', 'RaportController@EntityCountByMonth');
Route::get('report/order_values_with_dates', 'RaportController@OrderValuesWithDates');
Route::get('report/order_values_by_month', 'RaportController@OrderValueByMonth');
Route::get('report/order_values_by_contractor', 'RaportController@OrderValueByContractor');

Route::get('report/income_by', 'RaportController@IncomeBy');

Route::get('aggregate/where/{where?}', 'AggregateController@getAllWhere');

Route::get('file/{local}','FileController@get');