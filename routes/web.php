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

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/profile', 'Auth\ProfileController@edit')->name('profile');
Route::post('/profile', 'Auth\ProfileController@update')->name('profile');

Route::name('cms.')->group(function () {
    Route::prefix('cms')->middleware([
        'auth',
    ])->group(function () {
        Route::any('/', function (){
            return view('cms.index');
        })->name('index');


        Route::name('categories.')->group(function () {
            Route::prefix('categories')->group(function() {
                Route::any('/',  'Cms\Categories\CategoriesController@index')->name('index');
                Route::post('/store',  'Cms\Categories\CategoriesController@store')->name('store');
                Route::post('/update',  'Cms\Categories\CategoriesController@update')->name('update');
                Route::post('/delete',  'Cms\Categories\CategoriesController@delete')->name('delete');
            });
        });

        Route::name('users.')->group(function () {
            Route::prefix('users')->group(function() {
                Route::any('/',  'Cms\Users\UsersController@index')->name('index');
                Route::post('/store',  'Cms\Users\UsersController@store')->name('store');
                Route::post('/update',  'Cms\Users\UsersController@update')->name('update');
                Route::post('/delete',  'Cms\Users\UsersController@delete')->name('delete');
            });
        });
    });
});

Route::get('/', function () {
    $userId = \Auth::user()->id ?? 0;
    if ($userId) {
        return redirect(route('dashboard.index'));
    }
    return view('welcome');
})->middleware([
    'ShareCommonData'
]);

Route::name('dashboard.')->group(function () {
    Route::prefix('/dashboard/')->middleware([
        'ShareCommonData', 'auth',
    ])->group(function () {
        Route::get('/',  'Common\Dashboard\DashboardController@index')->name('index');
        Route::post('/',  'Common\Dashboard\DashboardController@load')->name('index');
        Route::post('/store',  'Common\Dashboard\DashboardController@store')->name('store');
        Route::post('/update',  'Common\Dashboard\DashboardController@update')->name('update');
        Route::post('/delete',  'Common\Dashboard\DashboardController@delete')->name('delete');
        Route::post('/delete_category',  'Common\Dashboard\DashboardController@deleteCategory')->name('delete_category');
        Route::post('/update_category',  'Common\Dashboard\DashboardController@updateCategory')->name('update_category');
        Route::post('/store_category',  'Common\Dashboard\DashboardController@storeCategory')->name('store_category');
    });
});

Route::post('/webhook', 'TelegramController@webhook');
