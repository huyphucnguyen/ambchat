<?php
header('Content-Type: application/json');

//Test
$res = null;
if (isset($_POST["username"]) && isset($_POST["password"])) {

    $username = $_POST["username"];
    $password = $_POST["password"];

    echo "sign-in $username $password";
    //$querry1="select * from user where user_name='$username' ";
    $sql = "SELECT * FROM user WHERE user_name='$username'";

    // ket noi database
    include "../lib/db.php";
    include "../lib/data.php";
    //$dbconnection=getDatabase();
    $dbconnection = new postgresql("1");

    //$result=pg_query($dbconnection,$querry1);
    $result = $dbconnection->select($sql);

    //  if(pg_num_rows($result)==0){
    //     echo ('{"values":-1,"message":"User không tồn tại"}');
    //     exit();
    //  }
    if ($result !== null) {
        if (pg_num_rows($result) > 0) {
            //user exist, check password
            $dbpassword = null;
            $user = null;
            while ($data = pg_fetch_object($result)) {
                $dbpassword = $data->pass_word;
                $user = $data;
                break;
            }
            if ($dbpassword !== null) {
                if (strcasecmp($dbpassword, $password) == 0) {
                    $res = new Result(Constant::SUCCESS, 'Operation complete successfully.');
                    unset($user->pass_word);
                    $res->data = $user;
                } else {
                    $res = new Result(Constant::INVALID_PASSWORD, 'Password is not matching.');
                }
            } else {
                $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
            }
        } else {
            $res = new Result(Constant::INVALID_USER, 'User is not exist');
        }
        $dbconnection->closeResult($result);
    } else {
        $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
    }
    $dbconnection->close();
    //  $querry2="select * from user where (user_name='$username' AND pass_word='$password')";
    //  $result=pg_query($dbconnection,$querry2);
    //  if(pg_num_rows($result)==0){
    //      echo ('{"values":-2,"message":"Sai password"}');
    //  }
    //echo ('{"values": 1,"message": "đăng nhập thành công" }');
    //echo (json_encode($res));
} else {
    $res = new Result(Constant::INVALID_PARAMETERS, 'Invalid parameters.');
}
echo (json_encode($res));
