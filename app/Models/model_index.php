<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class model_index extends Model {

public static function get_articles () {

$result = DB::select("SELECT * FROM articles");

return($result ? $result : false);

}
 
}
