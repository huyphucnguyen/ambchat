<?php
 header('Content-Type: application/json');
$res = null;
include "../lib/data.php";
if(isset($_POST['user_id'])){
  $user_id = $_POST['user_id'];
	
  //Connect to database 
  include '../lib/db.php';
  $dbconnection = new postgresql("");
  if($dbconnection->isValid()){
     $sql = "SELECT * FROM public.friends WHERE user_id = 129";
     $result = $dbconnection->select($sql);
    if($result !==null){
	$user = null;
       if(pg_num_rows($result)>0){
	  $user = pg_fetch_object($result);
	  $friend_list = $user->friend_id_list;
	  $arr = explode(",",$friend_list);
          $size = sizeof($arr);
	  $true_friends_list = array();
	  for($i = 0;$i < $size ; $i++){
		$fr_id = $arr[$i];
		//Kiem tra trong database có dòng user_id là $fr chứa $user_id không?
		$sql2 = "SELECT * FROM public.friends WHERE user_id = '{$fr_id}'";
		$result2 = $dbconnection->select($sql2);
		if($result2 !== null){
		   if(pg_num_rows($result2)>0){
			$data = pg_fetch_object($result2);
			$str_friends = $data->friend_id_list;
			$arr2 = explode(",",$str_friends);
			if(in_array($user_id,$arr2)){
			    $sql3 = "SELECT user_id,full_name,picture,email,gender,
			    user_id,phone FROM public.user WHERE user_id = '$fr_id'";
			    $result3 = $dbconnection->select($sql2);
			    if($result3!==null){
				$data_fr = pg_fetch_object($result3);
				array_push($true_friends_list,$data_fr);
				$dbconnection->closeResult($result3);
			    }//$result3!=null;
			    else{
				$res = new Result(Constant::GENERAL_ERROR,
				   'There was an error while processing request. Please try again later.');
			    }
					
			}//in_array($user_id,$arr2)
		    }//pg_num_rows($result2)>0
		    else{
			$res = new Result(Constant::INVALID_USER, 'User is not exist');
		    }
		    $dbconnection->closeResult($result2);
		}//$result2 !== null
		else{
			$res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
		}
		
	}//for()
	$res->data = $true_friends_list;
        $res = new Result(Constant::SUCCESS, 'Operation complete successfully.');
      } //pg_num_rows($result)>1
      else{
        $res = new Result(Constant::INVALID_USER, 'User is not exist');
      }
      $dbconnection->closeResult($result);
    } //$result !==null
    else {
      $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
    }
    $dbconnection->close();
  
 }//$dbconnection->isValid()
 else{
    $res = new Result(Constant::INVALID_DATABASE , 'Database is invalid.');  
 }
  
}  //isset($_GET['user_id'])
else {
  $res = new Result(Constant::INVALID_PARAMETERS, 'Invalid parameters.');
}
echo (json_encode($res));
?>
