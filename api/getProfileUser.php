<?php
header('Content-Type: application/json');
include "../lib/data.php";
$res=null;
if(isset($_POST["username"])){

	  $username = $_POST["username"];
	  $sql = "SELECT * FROM \"public\".\"user\" WHERE user_name='$username'";
     // ket noi database
     include "../lib/db.php";
     //$dbconnection=getDatabase();
     $dbconnection = new postgresql("");
     $result = $dbconnection->select($sql);
      if ($result !== null) {
        if (pg_num_rows($result) > 0) {
            //user exist
       
            $user = null;
            while ($data = pg_fetch_object($result)) {
                $user = $data;
                break;
            }
            $res = new Result(Constant::SUCCESS, 'Operation complete successfully.');
                    unset($user->pass_word);
                    $res->data = $user;
        } else {
            $res = new Result(Constant::INVALID_USER, 'User is not exist');
        }
        $dbconnection->closeResult($result);
    } else {
        $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
    }
    $dbconnection->close();

}   else {
    $res = new Result(Constant::INVALID_PARAMETERS, 'Invalid parameters.');
}
?>