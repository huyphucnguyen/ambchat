<?php 
 header('Content-Type: application/json');
$res = null;
include "../lib/data.php";
if(isset($_POST['user_id'])&&isset($_POST['friend_id'])){
  $user_id = $_POST['user_id'];
  $friend_id = $_POST['friend_id'];
 

  //Connect to database 
  include '../lib/db.php';
  include '../lib/function.php';
  $dbconnection = new postgresql("");
  if($dbconnection->isValid()){
     $sql = "SELECT * FROM public.friends WHERE user_id = '$user_id'";
     $result = $dbconnection->select($sql);
    if($result !==null){
      //TH1: User is not exits
       if(pg_num_rows($result)==0){
          //ex: "'1','444','0545'"
          $sql_i = "INSERT INTO \"public\".\"friends\" VALUES('$user_id','$friend_id')";
          $dbconnection->execute($sql_i);
       }
       //TH2: User is  exits
       else{
          $data = pg_fetch_object($result);
          $str_friends = $data->friend_id_list;
          addFriendToList($dbconnection,$str_friends,$friend_id,$user_id);
        }
     
    $res = new Result(Constant::SUCCESS, 'Operation complete successfully.');
    $dbconnection->closeResult($result);
    }
    else {
      $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
    }
    $dbconnection->close();
  
 }
  else{
    $res = new Result(Constant::INVALID_DATABASE , 'Database is invalid.');  
  }
  
} 
else {
  $res = new Result(Constant::INVALID_PARAMETERS, 'Invalid parameters.');
}
echo (json_encode($res));
