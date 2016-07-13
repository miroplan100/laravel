<?php

namespace App\Http\Controllers;

use URL;

use Request;

use App\Http\Requests;

use Illuminate\Support\Facades\Redirect;

use App\Models\model_admin as model;

class admin extends Controller {

public function __construct () {}

 
public function index () {

if (array_key_exists('user',$_COOKIE)) {

$redis = new \Redis();

$redis->connect('127.0.0.1');

$get = $redis->get('red');

if ($get) {

return(view('admin',['arr' => json_decode($get,true)]));

} else return('[empty]');

} else return(Redirect::to('input'));

}


public function __destruct () {}


}

