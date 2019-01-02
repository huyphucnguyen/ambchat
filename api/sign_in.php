<?php
header('Content-Type: application/json');
include "../lib/data.php";

//Test
$res = null;
if (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["device_id"])) {

    $username = $_POST["username"];
    $password = $_POST["password"];
    $device_id = $_POST["device_id"];
    
    $sql = "SELECT * FROM \"public\".\"user\" WHERE user_name='$username'";

    // ket noi database
    include "../lib/db.php";
    //$dbconnection=getDatabase();
    $dbconnection = new postgresql("");

    $result = $dbconnection->select($sql);

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
                    include "../lib/function.php";
                    $guid = GUID();
                    $res = new Result(Constant::SUCCESS, 'Operation complete successfully.');
                    unset($user->pass_word);
                    $res->sesson_key = $guid;
                    $res->data = $user;
                    $user_id = $user->user_id;
                    //Set online status that was login successfully
                    //setOnline($dbconnection,$user_id,$device_id);

                    //Token: user_id, timestart, timelife
                    date_default_timezone_set("Asia/Ho_Chi_Minh");
                    $time = time();
                    $token_raw = base64_encode(json_encode(new Token($user_id,$time,604800)));
                    //Encrypt token
                    $token = encryptData($token_raw,KEY_ENCRYPT);
                    //Thêm token
                    $tokende = dencryptData($token,KEY_ENCRYPT);
                    $res->token = base64_decode($tokende);
                             
                    
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
    
} else {
    $res = new Result(Constant::INVALID_PARAMETERS, 'Invalid parameters.');
}
echo (json_encode($res));
