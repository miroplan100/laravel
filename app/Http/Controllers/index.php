<?php

namespace App\Http\Controllers;

use URL;

use Request;

use App\Http\Requests;

use App\Lib\SypexGeo\SxGeo;

use App\Models\model_index as model;

class index extends Controller {

public function __construct () {}
 
public function index () {

$articles = model::get_articles();

if ($articles) {

(new SxGeo())->setInfo(); 

return(view('index',['articles' => $articles]));

} else {



}

}

public function __destruct () {}

}
