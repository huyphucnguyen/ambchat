<?php
header('Content-Type: application/json');
include "../lib/data.php";

//Test
$res = null;
if (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["device_id"])) {

    $username = $_POST["username"];
    $password = $_POST["password"];
    $device_id = $_POST["device_id"]
    
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
                    
                    //Tiến hành ghi bảng user_history và xóa bỏ các records có guid của bảng user_online
                    //trùng với guid trong bảng user_history có user_id và device_id trùng khớp
                    $sql_getRe = "SELECT * FROM public.user_history WHERE user_id = '$user->user_id' and device_id = '$device_id'";
                    $result_getRe = $dbconnection->select($sql);
                    if($result_getRe!=null){
                        if (pg_num_rows($result_getRe) > 0){
                            //Có tồn tại
                            $data1 = pg_fetch_object($result_getRe);
                            $guid_old = $data->$guid;
                            
                            //Tiến hành xóa những record đã tồn tại trong bảng user_online
                            $sql_remove_online = "DELETE FROM public.user_online WHERE guid = '$guid_old'";
                            //Cập nhật guid trong bảng $user_history
                            $sql_update_history = "UPDATE public.user_history SET guid = '$guid' WHERE user_id = '$user->user_id' and device_id = '$device_id'";
                            
                            $dbconnection->execute($sql_remove_online);
                            $dbconnection->execute($sql_update_history);
                            
                        } else{
                            //Không tồn tại thì insert vô | timeout = 1 tuần: 604800
                            $sql_insert_hi = "INSERT INTO public.user_history VALUES('$user->user_id','$device_id','$guid')";
                            $dbconnection->execute($sql_insert_hi);
                        }
                        date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $sql_insert_on = "INSERT INTO public.user_online VALUES('$guid',time(),604800)";
                        $dbconnection->execute($sql_insert_on);
                        
                        $dbconnection->closeResult($result_getRe);
                    } else{
                        //Trả về thông báo lỗi => Đã đăng nhập thành công thì có thông báo thành công => có cần thông báo không?
                    }
                    
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
//echo (json_encode($res));
echo createJsonWebToken($res);
