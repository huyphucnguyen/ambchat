<?php
header('Content-Type: application/json');
//error_reporting(E_ALL);
include "../lib/data.php";
$result = null;
$res = null;
if(isset($_POST['user_id'])&&isset($_POST['friend_id'])){
  $user_id = $_POST['user_id'];
  $friend_id = $_POST['friend_id'];
  include "../lib/db.php";
  $dbconnection = new postgresql("");
  if($dbconnection->isValid()){
    $sql = "SELECT friend_id_list FROM public.friends WHERE user_id = '$user_id'";
    $result = $dbconnection->select($sql);
    if($result!==null){
      if(pg_num_rows($result)>0){
          //Xoa ban be
          $friend_id_list = (pg_fetch_object($result))->friend_id_list;
          $arr = explode(",",$friend_id_list);
          if (in_array($friend_id, $arr)) 
          {
              if(sizeof($arr)>1){
                unset($arr[array_search($friend_id,$arr)]);
                $string = implode(",",$arr);
                //update
                $sql = "UPDATE public.friends SET friend_id_list = '$string' WHERE user_id = '$user_id'";
                $dbconnection->execute($sql);
              } //sizeof($arr2)>1
              else {
                $sql = "DELETE FROM public.friends WHERE user_id = '$user_id'"; 
                $dbconnection->execute($sql);
              }

              $sql = "SELECT friend_id_list FROM public.friends WHERE user_id = '$friend_id'";
              $result2 = $dbconnection->select($sql);
              if($result2!==null){
                  if(pg_num_rows($result2)>0){
                      $friend_id_list = (pg_fetch_object($result2))->friend_id_list;
                      $arr2 = explode(",",$friend_id_list);
                      if (in_array($user_id, $arr2)){
                          if(sizeof($arr2)>1){
                            unset($arr2[array_search($user_id,$arr2)]);
                            $string2 = implode(",",$arr2);
                            //update
                            $sql = "UPDATE public.friends SET friend_id_list = '$string2' WHERE user_id = '$friend_id'";
                            $dbconnection->execute($sql);  
                          }//sizeof($arr2)>1
                          else{
                            $sql = "DELETE FROM public.friends WHERE user_id = '$friend_id'"; 
                            $dbconnection->execute($sql);
                          }
                        $res = new Result(Constant::SUCCESS, 'Operation complete successfully.');
                      }//in_array($user_id, $arr2)
                       else{
                          $res = new Result(Constant::INVALID_FRIEND, 'Friend is not exist.');
                       }
                  }//pg_num_rows($result2)>0
                  else{
                     $res = new Result(Constant::INVALID_FRIEND, 'Friend is not exist.');
                  }
              }//$result2!==null
              else{
                  $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
              }
              
          }//in_array($user_id, $arr)  
          else{
              $res = new Result(Constant::INVALID_FRIEND, 'Friend is not exist.');
           }  
      }//pg_num_rows($result)>0
      else{
        $res = new Result(Constant::INVALID_USER, 'User is not exist');
      }
      $dbconnection->closeResult($result);
    }//$result!==null
    else{
      $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
    }
  }//$dbconnection->isValid()
  else{
    $res = new Result(Constant::INVALID_DATABASE , 'Database is invalid.');  
  }
}//isset($_POST['user_id']))&&isset($_POST['user_request_id'])
else{
    $res = new Result(Constant::INVALID_PARAMETERS, 'Invalid parameters.');
}
echo (json_encode($res));
