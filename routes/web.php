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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::group(array('prefix' => 'controladmin', 'namespace' => 'Backend'), function() {
    Route::group(['middleware' => ['auth', 'admin', 'xssprotection']], function() {
        Route::resource('pages', 'PageController', ['names' => [
            'index' => 'backend.pages.index',
            'create' => 'backend.pages.create',
            'destroy' => 'backend.pages.destroy',
        ]]);
               
        
        /*
        Route::controller( '/admin-accounts', 'UserController',
            [
                'getIndex'          => 'backend.login',
                'postAuthenticate'  => 'backend.authenticate',
                'getLogout'         => 'backend.logout',
                'getProfile'        => 'backend.profile',
                'postUpdateProfile' => 'backend.profile.update',
            ]
        );
        */
    });

});