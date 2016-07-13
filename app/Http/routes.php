<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


if (Request::isMethod('get')) {

Route::get('/','index@index'); 

Route::get('article','article@index');

Route::get('input','input@index');

Route::get('admin','admin@index');

} else {

Route::post($_POST['url'],$_POST['url'].'@'.$_POST['act']);

}