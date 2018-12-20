<?php
header('Content-Type: application/json');
include "../lib/data.php";

$res = null;
if (isset($_POST["TOKEN"])) {
    $token = $_POST["TOKEN"];
    
    $sql = "SELECT * FROM \"public\".\"user\" WHERE token='$token'";
    
    // ket noi database
    include "../lib/db.php";

    $dbconnection = new postgresql("");
    if($dbconnection->isValid()){
        $result = $dbconnection->select($sql);
        if ($result !== null) {
          if (pg_num_rows($result) > 0) {
            //Ton tại thì cập nhât lại token và trả về cho client
            $user_id = null;
            $device_id = null;
            while ($data = pg_fetch_object($result)) {
              $user_id = $data->user_id;
              $device_id = $data->device_id;
              break;
            }
            
            //Cập nhật token
            $sql_info = "SELECT * FROM public.user WHERE user_id = '$user_id'";
            $result_info = $dbconnection->select($sql_info);
            if($result_info!==null){
              if(pg_num_rows($result_info) > 0){
                $user = null;
                while ($data = pg_fetch_object($result)) {
                    $user = $data;
                    break;
                }
                unset($user->pass_word);
                $res = new Result(Constant::SUCCESS, 'Operation complete successfully.');
                $res->data = $user;
                
                
                //Set online status that was login successfully
                setOnline($dbconnection,$user_id,$device_id);
                
                  //Tạo token & save token vào db
                  $token = createJsonWebToken($res);
                  //Xóa token cũ (nếu có)
                  $sql_delete_token = "DELETE FROM public.token WHERE user_id = '$user_id' and device_id = '$device_id'";
                  $sql_insert_token = "INSERT INTO public.token VALUES('$user_id','$device_id','$token')";
                  $dbconnection->execute($sql_delete_token);
                  $dbconnection->execute($sql_insert_token);
              }else{
                $res = new Result(Constant::INVALID_USER , 'User is invalid.');  
              }
            }else{
              $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
            }
          }else{
            $res = new Result(Constant::INVALID_TOKEN, 'Token is not exist');
          }
        }else{
          $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
        }
    }else{
       $res = new Result(Constant::INVALID_DATABASE , 'Database is invalid.');  
    }
}else{
  $res = new Result(Constant::INVALID_PARAMETERS, 'Invalid parameters.');
}

echo createJsonWebToken($res);
  
