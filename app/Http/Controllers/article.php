<?php

namespace App\Http\Controllers;

use URL;

use Request;

use App\Http\Requests;

use App\Lib\SypexGeo\SxGeo;

use Illuminate\Support\Facades\Redirect;

use App\Models\model_article as model;

class article extends Controller {

public function __construct () {}

 
public function index () {

$article = model::get_article();

if ($article) {

(new SxGeo())->setInfo(); 

return(view('article',$article[0]));

} else return('Error');

}


public function __destruct () {}


}
