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


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

Route::group(['prefix' => 'api/v1', 'namespace' => 'Api'], function () {

  Route::post('/auth/register', ['as' => 'auth.register','uses' => 'UserController@register']);
  Route::post('/auth/login', ['as' => 'auth.login','uses' => 'UserController@login']);
  Route::post('/user/location', ['as' => 'user.register','uses' => 'UserController@updatelocation']);
  Route::post('/user/update', ['as' => 'user.update','uses' => 'UserController@updateUser']);
  Route::post('/user/userlike', ['as' => 'user.like','uses' => 'UserController@userlike']);
  Route::get('/user/userlike', ['as' => 'user.like','uses' => 'UserController@sendchat']);
  Route::get('/user/matchedusers', ['as' => 'user.match','uses' => 'UserController@matchedusers']);
  Route::get('/user/userlist', ['as' => 'user.list','uses' => 'UserController@userlist']);
  Route::post('/user/linked', ['as' => 'user.linked','uses' => 'UserController@userlinked']);
  Route::post('/user/setpaid', ['as' => 'user.setpaid','uses' => 'UserController@setPaid']);
  Route::post('/user/setstatus', ['as' => 'user.setstatus','uses' => 'UserController@setStatus']);
  Route::post('/user/testAPN', ['as' => 'user.testAPN','uses' => 'UserController@testAPN']);
  Route::get('/user/botmatch', ['as' => 'user.botmatch','uses' => 'UserController@botmatch']);
  Route::post('/user/push', ['as' => 'user.push','uses' => 'UserController@updatePush']);

  Route::get('/user/searchuserlist', ['as' => 'user.searchuserlist', 'uses' => 'UserController@searchUserlist']);
  Route::post('/user/givefeedback', ['as' => 'user.givefeedback', 'uses' => 'UserController@givefeedback']);
  Route::post('/user/imagelike', ['as' => 'user.imagelike', 'uses' => 'UserController@imagelike']);
  Route::post('/user/imageliked', ['as' => 'user.imageliked', 'uses' => 'UserController@imageliked']);
  Route::get('/user/searchstylelist', ['as' => 'user.searchstylelist', 'uses' => 'UserController@searchstylelist']);
  Route::get('/user/getAllUser', ['as' => 'user.getAllUser', 'uses' => 'UserController@getAllUser']);
  Route::get('/user/getuser', ['as' => 'user.getuser', 'uses' => 'UserController@getuser']);
  Route::post('/user/favouriteuser', ['as' => 'user.favouriteuser', 'uses' => 'UserController@favouriteuser']);
  Route::post('/user/contactaccept', ['as' => 'user.contactaccept', 'uses' => 'UserController@contactaccept']);
  Route::post('/user/contactrequest', ['as' => 'user.contactrequest', 'uses' => 'UserController@contactrequest']);
  Route::get('/user/getcontacteduser', ['as' => 'user.getcontacteduser', 'uses' => 'UserController@getcontacteduser']);
  Route::get('/user/contacted', ['as' => 'user.contacted', 'uses' => 'UserController@contacted']);
  Route::get('/user/getkeyandprice', ['as' => 'user.getkeyandprice', 'uses' => 'UserController@getkeyandprice']);
  Route::post('/user/purchase', ['as' => 'user.purchase', 'uses' => 'UserController@purchase']);

  Route::post('/user/sendMail', ['as' => 'user.sendMail', 'uses' => 'UserController@sendMail']);

  Route::post('/image/uploadprofileImage', ['as' => 'image.uploadprofileImage', 'uses' => 'ImageController@update_proilfeImage']);
  Route::post('/image/upload_image', ['as' => 'image.upload_image', 'uses' => 'ImageController@upload_image']);
  Route::get('/image/get_images', ['as' => 'image.get_images', 'uses' => 'ImageController@get_images']);
  Route::get('/image/searchimage', ['as' => 'image.searchimage', 'uses' => 'ImageController@searchimage']);

  Route::post('/job/jobrequest', ['as' => 'job.jobrequest', 'uses' => 'JobController@jobrequest']);
  Route::get('/job/getAllJob', ['as' => 'job.getAllJob', 'uses' => 'JobController@getAllJob']);
  Route::post('/job/jobAccept', ['as' => 'job.jobAccept', 'uses' => 'JobController@jobAccept']);
  Route::post('/job/jobEnd', ['as' => 'job.jobEnd', 'uses' => 'JobController@jobEnd']);
  Route::post('/job/jobedit', ['as' => 'job.jobedit', 'uses' => 'JobController@jobedit']);
  Route::post('/job/jobdelete', ['as' => 'job.jobdelete', 'uses' => 'JobController@jobdelete']);

  Route::get('/braintree/getClientToken', ['as' => 'braintree.getClientToken', 'uses' => 'BraintreeController@getClientToken']);
  Route::get('/braintree/getTransaction', ['as' => 'braintree.getTransaction', 'uses' => 'BraintreeController@getTransaction']);


  Route::post('/image/upload', ['as' => 'image.upload','uses' => 'ImageController@upload_profile']);
  Route::get('/image/list', ['as' => 'image.list','uses' => 'ImageController@get_images']);
  Route::delete('image/{id}', ['as' => 'image.delete', 'uses' => 'ImageController@deleteImage']);
  
});


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
