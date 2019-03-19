<?php 
header('Content-Type : application/json');
$res = null;
include "../lib/data.php";
if(isset($_POST['user_id']))&&isset($_POST['user_request_id'])){
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
        $request_list = pg_fetch_object($result);
        $arr = explode(",",$request_list);
        if (in_array($user_request_id, $arr)) 
        {
            unset($arr[array_search($user_request_id,$arr)]);
        }
        
        $string = implode(",",$arr);
        //update
        $sql = "UPDATE public.request_friends SET request_id = '$string' WHERE user_id = '$user_id'";
        $dbconnection->execute($sql);
      }//pg_num_rows($result)>0
      else{
        $res = new Result(Constant::INVALID_USER, 'User is not exist');
      }
      $dbconnection->closeResult($result);
    }//$result!==null
    else{
      $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
    }
    
    //Xoa trong bang friends
    $sql = "SELECT friend_id_list FROM public.friends WHERE user_id = '$user_request_id'";
    $result = $dbconnection->select($sql);
    if($result!==null){
      $friend_id_list = pg_fetch_object($result);
      $arr2 = explode(",",$friend_id_list);
      if (in_array($user_id, $arr2)) 
      {
            unset($arr[array_search($user_id,$arr2)]);
      }
        
      $string2 = implode(",",$arr2);
      //update
      $sql = "UPDATE public.friends SET friend_id_list = '$string2' WHERE user_id = '$user_request_id'";
      $dbconnection->execute($sql);
      $dbconnection->closeResult($result); 
      $res = new Result(Constant::SUCCESS, 'Operation complete successfully.');

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
?>
