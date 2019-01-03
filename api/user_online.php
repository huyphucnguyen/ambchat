<?php
header('Content-Type: application/json');
include "../lib/data.php";
include "../lib/db.php";
$res = null;

//Connect database
$dbconnection = new postgresql("");
if($dbconnection->isValid()){
    //online user request
    if(isset($_GET['token'])){
        $token = $_GET['token'];
        
         $token_decrypt = dencryptData($token,KEY_ENCRYPT);
        //decode json to class
        if($token_decrypt!==null){
            $token_data = json_decode($token_decrypt);
            $user_id = $token_data->user_id;
            $device_id = $token_data->device_id;
            
            setOnline($dbconnection,$user_id,$device_id);
            $res = new Result(Constant::SUCCESS, 'Update is completed');
        }else{
            $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
        }
        $dbconnection->close();
    } else{
         $res = new Result(Constant::INVALID_TOKEN , 'Token is invalid.');
    }
}
else{
    $res = new Result(Constant::INVALID_DATABASE , 'Database is invalid.');  
}
echo (json_encode($res));
