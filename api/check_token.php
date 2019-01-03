<?php
header('Content-Type: application/json');
include "../lib/data.php";
$res = null;
if (isset($_POST["TOKEN"])) {
    $token = $_POST["TOKEN"];
 
    //Decrypt token is sent by client
    $token_decrypt = dencryptData($token,KEY_ENCRYPT);
    //decode json to class
//    if($token_decrypt!==null){
//         $token_data = json_decode($token_decrypt);
//         $user_id = $token_data->user_id;
//         $device_id = $token_data->device_id;
//         $time_start = $token_data->time_start;
//         $time_life  = $token_data->time_life;

//         //Take time at the present time
//         $time_now = time();
//         if(($time_start+$time_life)>=$time_now){
//             //token is alive => return user data
//             $sql = "SELECT * FROM public.user WHERE user_id = '$user_id'";

//             // ket noi database
//             include "../lib/db.php";
//             $dbconnection = new postgresql("");
//             if($dbconnection->isValid()){
//                 $result = $dbconnection->select($sql);
//                 if ($result !== null) {
//                   if (pg_num_rows($result) > 0) {
//                     $user = null;
//                     while ($data = pg_fetch_object($result)) {
//                       $user = $data;
//                       break;
//                     }
//                     unset($user->pass_word);             
//                     $res = new Result(Constant::SUCCESS, 'Operation complete successfully.');
//                     $res->data = $user;

//                     //Set online status that was login successfully
//                     setOnline($dbconnection,$user_id,$device_id);

//                     //Token: user_id, timestart, timelife
//                     date_default_timezone_set("Asia/Ho_Chi_Minh");
//                     $time = time();
//                     $token_raw = (json_encode(new Token($user_id,$device_id,$time,604800)));
//                     //Encrypt token
//                     $token = encryptData($token_raw,KEY_ENCRYPT);
//                     //Thêm token
//                     $res->token = $token;
//                   }else{
//                     $res = new Result(Constant::INVALID_USER , 'User is invalid.');  
//                   }
//                 }else{
//                   $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
//                 }
//             }else{
//                $res = new Result(Constant::INVALID_DATABASE , 'Database is invalid.');  
//             }
//         }else{
//             //Token is die
//             $res = new Result(Constant::INVALID_TOKEN , 'Token is invalid.');  
//         }
//     }else{
//         $res = new Result(Constant::INVALID_TOKEN , 'Token is invalid.');
//     }
    
}else{
  $res = new Result(Constant::INVALID_PARAMETERS, 'Invalid parameters.');
}
echo json_encode($res);
