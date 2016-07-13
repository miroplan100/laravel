<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class model_input extends Model {


public static function db_check () {

$result = DB::select("

SELECT * FROM users 

WHERE 

login = '".rawurldecode($_POST['login'])."' && password = '".rawurldecode($_POST['password'])."'

");

if (count($result)) {

return(json_encode(['id' => $result[0]['id'],'exist' => (boolean) true]));

} else {

return(json_encode(['exist' => (boolean) false]));

}



}


}
