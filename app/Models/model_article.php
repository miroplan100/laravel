<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class model_article extends Model {

public static function get_article () {

$result = DB::select("

SELECT * FROM articles 

WHERE 

article_id = ".(array_key_exists('id',$_REQUEST) ? $_REQUEST['id'] : 0)."

");

return($result ? $result : false);

}


}
