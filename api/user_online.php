<?php
header('Content-Type: application/json');
include "../lib/data.php";
include "../lib/db.php";
$res = null;

//Connect database
$dbconnection = new postgresql("");
if($dbconnection->isValid()){

//     //Delete offline user
//     $time = time();
//     $time_check = $time - 300; //set online time is 5 minutes
//     $sql_deleOff = "delete from public.user_online where time < $time_check";
//     $dbconnection->execute($sql);

    //online user request
    if(isset($_GET['guid'])){
        $guid = $_GET['guid'];
        date_default_timezone_set("Asia/Ho_Chi_Minh"); 
        $time = time();
        $sql = "select * from public.user_online where guid = '$guid';


        $result = $dbconnection->select($sql);

        if($result!=null){
            if(pg_num_rows($result) != 0){
                //Nếu tồn tại thì update thời gian mới nhất
                $sql_updateSS = "update public.user_online set time_start = '$time' where guid = '$user_id'";
                $dbconnection->execute($sql_updateSS);
            }else{
                //Không tồn tại thì insert mới
                $sql_insertSS = "insert into public.user_online values('$guid','$time_start')";
                $dbconnection->execute($sql_insertSS);
            }

            $res = new Result(Constant::SUCCESS, 'Update is completed');

            $dbconnection->closeResult($result);
        }else{
            $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
        }
        $dbconnection->close();
    }else{
        $res = new Result(Constant::INVALID_PARAMETERS, 'Invalid parameters.');
    }
}
else{
    $res = new Result(Constant::INVALID_DATABASE , 'Database is invalid.');  
}
echo (json_encode($res));
