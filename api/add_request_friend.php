<?php
header('Content-Type: application/json');
include "../lib/data.php";
$result = null;
$res = null;
if(isset($_POST['user_id'])&&isset($_POST['others_id'])){
  $user_id = $_POST['user_id'];
  $others_id = $_POST['others_id'];
  include "../lib/db.php";
  include "../lib/function.php";
  $dbconnection = new postgresql("");
  if($dbconnection->isValid()){
    $sql = "SELECT friend_id_list FROM public.friends WHERE user_id = '$user_id'";
    $result = $dbconnection->select($sql);
    if($result!==null){
      if(pg_num_rows($result)>0){
        $friend_id_list = (pg_fetch_object($result))->friend_id_list;
        addFriendToList($dbconnection,$friend_id_list,$others_id,$user_id);
      }
      else{
        $sql = "INSERT INTO public.friends VALUES('$user_id','$others_id')";
        $dbconnection->execute($sql);
      }
      $res = new Result(Constant::SUCCESS, 'Operation complete successfully.');
      $dbconnection->closeResult($result); 
    }
    else{
      $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
    }  
  }
  else{
    $res = new Result(Constant::INVALID_DATABASE , 'Database is invalid.');  
  }
}
else{
    $res = new Result(Constant::INVALID_PARAMETERS, 'Invalid parameters.');
}
echo (json_encode($res));
