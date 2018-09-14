<?php
session_start();
header('Content-Type: application/json');
include "db.php";
$res = null;

$session = session_id();
$time = time();
$time_check = $time - 300; //set time online is 5 minutes

$dbconnection = new postgresql("");
$sql = "select * from public.user_online where session = '$session'";
$result = $dbconnection->select($sql);

if($result!=null){
    if(pg_num_rows($result) == 0){
        $sql1 = "insert into public.user_online(session,time) values('$session','$time')";
        $result1 = $dbconnection->select($sql1);
        if($result==null)
            $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
        $dbconnection->closeResult($result1);
    }
    else{
        $sql2 = "update public.user_online set time='$time' where session = '$session'";
        $result2 = $dbconnection->select($sql2);
        if($result==null)
            $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
        $dbconnection->closeResult($result2);
    }

    $sql3 = "select * from public.user_online";
    $result3 = $dbconnection->select($sql3);
    $count_user_online = pg_num_rows($result3);
    $dbconnection->closeResult($result3);
    $res = new Result(Constant::SUCCESS,'User online number: '.$count_user_online);

    //Delete record when user is offline
    $sql4 = "delete from public.user_online where time < $time_check";
    $result4 = $dbconnection->select($sql4);
    $dbconnection->closeResult($result4);
    $dbconnection->closeResult($result);
    $dbconnection->close();
}
else
    $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
echo $res;

?>
