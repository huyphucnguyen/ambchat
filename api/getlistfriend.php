<?php
 header('Content-Type: application/json');
$res = null;
include "../lib/data.php";
if(isset($_POST['user_id'])){
  $user_id = $_POST['user_id'];
  
  //Connect to database 
  include '../lib/db.php';
  include '../lib/function.php';
  $dbconnection = new postgresql("");
  if($dbconnection->isValid()){
    $sql = "SELECT * FROM public.friends WHERE user_id = '$user_id'";
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
          $b_friend_a = isFriend($dbconnection,$fr_id,$user_id);
          if($b_friend_a==true){
            $sql3 = "SELECT user_id,full_name,picture,email,gender,user_id,phone FROM public.user WHERE user_id = '$fr_id'";
            $result3 = $dbconnection->select($sql3);
            if($result3!==null){
              $data_fr = pg_fetch_object($result3);
              array_push($true_friends_list,$data_fr);
              $dbconnection->closeResult($result3);
            }
            else{
              $res = new Result(Constant::GENERAL_ERROR,'There was an error while processing request. Please try again later.');
            }    
          }
        }
        if(sizeof($true_friends_list)==0){
          $res = new Result(Constant::HAVE_NO_FRIEND, 'You have no friends.');
        }
        else{
          $res = new Result(Constant::SUCCESS, 'Operation complete successfully.');
          $res->data = $true_friends_list;
        }  
      }
      else{
        $res = new Result(Constant::INVALID_USER, 'User is not exist');
      }
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
?>
