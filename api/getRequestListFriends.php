<?php
header('Content-Type:application/json');
include "../lib/data.php";
$res = null;
if(isset($_POST['user_id'])){
    $user_id = $_POST['user_id'];
    $sql = "SELECT * FROM public.request_friends WHERE user_id = '$user_id'";
    //init database
    include "../lib/db.php";
    $dbconnection = new postgresql("");
    if($dbconnection->isValid()){
        $result = $dbconnection->select($sql);
        if($result!==null){
            if(pg_num_rows($result)>0){
                $data = pg_fetch_object($result);
                $request_id = $data->request_id;
		$request_id = '('.$request_id.')';
                $sql = "select user_id,full_name,picture,email,gender,
			    user_id,phone from public.user where user_id in $request_id";
                $result1 = $dbconnection->select($sql);
		$list_request = array();
		while($list = pg_fetch_object($result1)){
			array_push($list_request,$list);
		}
                $res = new Result(Constant::SUCCESS, 'Operation complete successfully.');
	        $res->data = $true_friends_list;
		$dbconnection->closeResult($result1);
            }//pg_num_rows($result)>0
            else {
                $res = new Result(Constant::INVALID_USER, 'User is not exist');
            }
	$dbconnection->closeResult($result);
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
