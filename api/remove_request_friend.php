<?php 
header('Content-Type: application/json');
include "../lib/data.php";
$result = null;
$res = null;
if(isset($_POST['user_id'])&&isset($_POST['user_request_id'])){
  $user_id = $_POST['user_id'];
  $user_request_id = $_POST['user_request_id'];
  include "../lib/db.php";
  $dbconnection = new postgresql("");
  if($dbconnection->isValid()){
    //Xoa trong bang request
    $sql = "SELECT friend_id_list FROM public.friends WHERE user_id = '$user_request_id'";
    $result = $dbconnection->select($sql);
    if($result!==null){
      $friend_id_list = (pg_fetch_object($result))->friend_id_list;
      $arr = explode(",",$friend_id_list);
      if (in_array($user_id, $arr)) 
      {
          if(sizeof($arr)>1){
            unset($arr[array_search($user_id,$arr)]);
            $string2 = implode(",",$arr);
            //update
            $sql = "UPDATE public.friends SET friend_id_list = '$string2' WHERE user_id = '$user_request_id'";
            $dbconnection->execute($sql);
          } 
          else {
            $sql = "DELETE FROM public.friends WHERE user_id = '$user_request_id'"; 
            $dbconnection->execute($sql);
          }  
          $dbconnection->closeResult($result); 
          $res = new Result(Constant::SUCCESS, 'Operation complete successfully.');
      } 
      else{
          $res = new Result(Constant::INVALID_FRIEND, 'Friend is not exist.');
      }  
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
