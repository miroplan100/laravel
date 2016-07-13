<?php

namespace App\Http\Controllers;

use URL;

use Request;

use App\Http\Requests;

use Illuminate\Support\Facades\Redirect;

use App\Models\model_input as model;

class input extends Controller {

public function index () {

if (array_key_exists('user',$_COOKIE)) {

return(Redirect::to('admin'));

} else return(view('input'));

}

public function check () {

return(model::db_check());

}

}
