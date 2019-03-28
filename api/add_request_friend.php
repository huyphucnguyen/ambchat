<?php
header('Content-Type: application/json');
include "../lib/data.php";
$result = null;
$res = null;
if(isset($_POST['user_id'])&&isset($_POST['others_id'])){
  $user_id = $_POST['user_id'];
  $others_id = $_POST['others_id'];
  include "../lib/db.php";
  $dbconnection = new postgresql("");
  if($dbconnection->isValid()){
    //Xoa trong bang request
    $sql = "SELECT request_id FROM public.request_friends WHERE user_id = '$others_id'";
    $result = $dbconnection->select($sql);
    if($result!==null){
      if(pg_num_rows($result)>0){
        $request_list = (pg_fetch_object($result))->request_id;
        $arr = explode(",",$request_list);
        if (!in_array($user_id, $arr)) 
        {
            array_push($arr,$user_id);
            $string = implode(",",$arr);
            //update
            $sql = "UPDATE public.request_friends SET request_id = '$string' WHERE user_id = '$others_id'";
            $dbconnection->execute($sql);
         }//in_array($user_request_id, $arr)
       }//pg_num_rows($result)>0
      else{
        $sql = "INSERT INTO public.request_friends VALUES('$others_id','$user_id')";
        $dbconnection->execute($sql);
      }
        //Them trong bang friends
      $sql = "SELECT friend_id_list FROM public.friends WHERE user_id = '$user_id'";
      $result2 = $dbconnection->select($sql);
      if($result2!==null){
        if(pg_num_rows($result2)>0){
            $friend_id_list = (pg_fetch_object($result2))->friend_id_list;
            $arr2 = explode(",",$friend_id_list);
            if (!in_array($user_id, $arr2)) 
            {
                array_push($arr2,$others_id);
                $string2 = implode(",",$arr2);
                //update
                $sql = "UPDATE public.friends SET friend_id_list = '$string2' WHERE user_id = '$user_id'";
                $dbconnection->execute($sql);
            }//in_array($user_id, $arr2) 
        }//pg_num_rows($result2)>0
        else{
              $sql = "INSERT INTO public.friends VALUES('$user_id','$others_id')";
              $dbconnection->execute($sql);
        }
        $res = new Result(Constant::SUCCESS, 'Operation complete successfully.');
        $dbconnection->closeResult($result2); 
      }//$result2!==null
      else{
        $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
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
