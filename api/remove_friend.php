<?php
header('Content-Type: application/json');
include "../lib/data.php";
$result = null;
$res = null;
if(isset($_POST['user_id'])&&isset($_POST['friend_id'])){
  $user_id = $_POST['user_id'];
  $friend_id = $_POST['friend_id'];
  include "../lib/db.php";
  include "../lib/function.php";
  $dbconnection = new postgresql("");
  if($dbconnection->isValid()){
      $isRemoveA = removeFriend($dbconection,$user_id,$friend_id);
      if($isRemoveA == true){
        $isRemoveB = removeFriend($dbconection,$friend_id,$user_id);
        
        if($isRemoveA == true && $isRemoveB == true){
          $res = new Result(Constant::SUCCESS, 'Remove friend successfuly');
        }
        else{
          $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
        }
      }
  }  else{
    $res = new Result(Constant::INVALID_DATABASE , 'Database is invalid.');  
  }
}
else{
    $res = new Result(Constant::INVALID_PARAMETERS, 'Invalid parameters.');
}
echo (json_encode($res));
