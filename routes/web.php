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

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('signup', 'UserController@signup')->name('signup');
Route::post('signup/post', 'UserController@signuppost')->name('signuppost');
Route::get('login', 'UserController@login')->name('login');
Route::post('login/post', 'UserController@loginpost')->name('loginpost');
Route::get('/', 'UserController@homepage')->name('home');

Route::group(['prefix'=>'admin','middleware' => 'auth'], function () {
    
    Route::get('logout', 'UserController@logout')->name('logout');

    


    Route::group(['prefix' => 'courses'], function(){       
        Route::get('/', 'CoursesController@addcourses')->name('addcourses');
        Route::post('/post', 'CoursesController@addcoursespost')->name('addcoursespost');
        Route::get('/managecourses', 'CoursesController@managecourses')->name('managecourses');
        Route::post('/delete', 'CoursesController@deletecourses')->name('deletecourses');
        Route::post('/status', 'CoursesController@statuscourses')->name('statuscourses');

        Route::get('/edit/{id}', 'CoursesController@editcourses')->name('editcourses');

        Route::post('/edit/post', 'CoursesController@editcoursespost')->name('editcoursespost');
    });

  

});
