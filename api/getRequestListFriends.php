<?php
header('Content-Type:application/json');
include "../lib/data.php";
$res = null;
if(isset($_POST['user_id'])){
    $user_id = $_POST['user_id'];
    $sql = "SELECT * FROM public.friend_requests WHERE user_id = '$user_id'";
    //init database
    include "../lib/db.php";
    $dbconnection = new postgresql("");
    if($dbconnection->isValid()){
        $result = $dbconnection->select($sql);
        if($result!==null){
            if(pg_num_rows($result)>0){
                $data = pg_fetch_object($result);
                $request_id = $data->$request_id;
                $arr = explode(",",$request_id);
                $sql = "select * from public.user where user_id in '{$arr}'";
                $result1 = $dbconnection->select($sql);
                $res = new Result(Constant::SUCCESS, 'Operation complete successfully.');
                $true_friends_list = pg_fetch_array($result1);
	              $res->data = $true_friends_list;
            }//pg_num_rows($result)>0
            else {
                $res = new Result(Constant::INVALID_USER, 'User is not exist');
            }
        }//$result!==null
        else {
            $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
        }
        $dbconnection->close();
    }//$dbconnection->isValid()
    else {
        $res = new Result(Constant::INVALID_DATABASE , 'Database is invalid.');  
    }
}//isset($_POST['user_id'])
else {
    $res = new Result(Constant::INVALID_PARAMETERS, 'Invalid parameters.');
}

echo (json_encode($res));
?>
