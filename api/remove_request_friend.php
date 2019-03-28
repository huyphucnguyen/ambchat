<?php 
//error_reporting(E_ALL);
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
    $sql = "SELECT request_id FROM public.request_friends WHERE user_id = '$user_id'";
    $result = $dbconnection->select($sql);
    if($result!==null){
      if(pg_num_rows($result)>0){
        $request_list = (pg_fetch_object($result))->request_id;
        $arr = explode(",",$request_list);
        if (in_array($user_request_id, $arr)) 
        {
            if(sizeof($arr)>1){
                unset($arr[array_search($user_request_id,$arr)]);
                $string = implode(",",$arr);
                //update
                $sql = "UPDATE public.request_friends SET request_id = '$string' WHERE user_id = '$user_id'";
                $dbconnection->execute($sql);
            }//sizeof($arr)>1
            else {
                $sql = "DELETE FROM public.request_friends WHERE user_id = '$user_id'"; 
                $dbconnection->execute($sql);
            }
          
            //Xoa trong bang friends
            $sql = "SELECT friend_id_list FROM public.friends WHERE user_id = '$user_request_id'";
            $result2 = $dbconnection->select($sql);
            if($result2!==null){
              $friend_id_list = (pg_fetch_object($result2))->friend_id_list;
              $arr2 = explode(",",$friend_id_list);
              if (in_array($user_id, $arr2)) 
              {
                  if(sizeof($arr2)>1){
                    unset($arr[array_search($user_id,$arr2)]);
                    $string2 = implode(",",$arr2);
                    //update
                    $sql = "UPDATE public.friends SET friend_id_list = '$string2' WHERE user_id = '$user_request_id'";
                    $dbconnection->execute($sql);
                  } //sizeof($arr2)>1
                  else {
                    $sql = "DELETE FROM public.friends WHERE user_id = '$user_request_id'"; 
                    $dbconnection->execute($sql);
                  }  
                  $dbconnection->closeResult($result2); 
                  $res = new Result(Constant::SUCCESS, 'Operation complete successfully.');
              }//in_array($user_id, $arr2)  
              else{
                  $res = new Result(Constant::INVALID_FRIEND, 'Friend is not exist.');
               }  
            }//$result2!==null
            else{
              $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
            }  
          }//in_array($user_request_id, $arr)
          else{
              $res = new Result(Constant::INVALID_FRIEND_REQUEST, 'Friend request id is not exist.');
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
