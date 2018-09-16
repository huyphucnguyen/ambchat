<?php
header('Content-Type: application/json');
include "../lib/data.php";
include "../lib/db.php";
$res = null;

//Connect database
$dbconnection = new postgresql("");

//Delete offline user
$time = time();
$time_check = $time - 300; //set online time is 5 minutes
$sql_deleOff = "delete from public.user_online where time < $time_check";
$dbconnection->execute($sql);

//online user request
if(isset($_GET['session'])&&isset($_GET['user_id'])&&isset($_GET['device_id'])){
    $session = $_GET['session'];
    $user_id = $_GET['user_id'];
    $device_id = $_GET['device_id'];


    $sq1 = "select * from public.user_online where user_id = '$user_id' and device_id = '$device_id'";


    $result = $dbconnection->select($sql);

    if($result!=null){
        if(pg_num_rows($result) != 0){
            //Nếu tồn tại thì update session
            $sql_updateSS = "update public.user_online set session = '$session',time = '$time' where user_id = '$user_id' and device_id = '$device_id' ";
            $dbconnection->execute($sql_updateSS);
        }else{
            //Không tồn tại thì insert mới
            $sql_insertSS = "insert into public.user_online values('$session','$time','$user_id','$device_id') ";
            $dbconnection->execute($sql_insertSS);
        }
        $dbconnection->closeResult($result);
    }else{
        $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
    }
    $dbconnection->close();
}else{
    $res = new Result(Constant::INVALID_PARAMETERS, 'Invalid parameters.');
}

echo (json_encode($res));
