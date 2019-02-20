<?php

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

Auth::routes();

Route::get('/kp/{uuid}', 'COController@display')->where('uuid', '[a-zA-Z0-9/_-]+')->name('showCO');
Route::get('/download/{uuid}', 'COController@downloadAsPdf')->where('uuid', '[a-zA-Z0-9/_-]+')->name('downloadAsPdf');

Route::group(['middleware' => ['web', 'auth']], function () {

    Route::get('/', function () {
        return view('kpGenerator.index');
    })->name('/');

    Route::post('/getTypesByClass', 'TypeController@getTypesByClass')->name('getTypes');
    Route::post('/getAllEquipmentTypes', 'TypeController@getAllEquipmentTypes')->name('getAllEquipmentTypes');
    Route::post('/getDefaultTypesWithEquipment', 'TypeController@getDefaultTypesWithEquipment')->name('getDefaultTypesWithEquipment');
    Route::post('/getDefaultWorks', 'TypeController@getDefaultWorks')->name('getDefaultWorks');
    Route::post('/getTaxBySlug', 'SettingsController@getTaxBySlug')->name('getTax');
    Route::post('/getOfferGroup', 'COController@getOfferGroup')->name('getOfferGroup');
    Route::post('/findEquipmentByCode', 'EquipmentController@findEquipmentByCode')->name('findEquipment');
    Route::post('/findWorkByCode', 'EquipmentController@findWorkByCode')->name('findWork');
    Route::get('/searchEquipment', 'EquipmentController@searchEquipment')->name('searchEquipment');


    Route::post('/calculateAllPrices', 'COController@calculateAllPrices')->name('calculateAllPrices');
    Route::post('/calculatePrePrices', 'COController@calculatePrePrices')->name('calculatePrePrices');

    Route::post('/saveOfferGroup', 'OfferController@saveOfferGroup');
    Route::post('/updateOfferGroup', 'OfferController@updateOfferGroup');

    Route::post('/excel-upload', 'UploadsController@excelUploadEquipment')->name('excel-upload');

});
