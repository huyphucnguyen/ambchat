<?php 
header('ContentType : application/json');
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
        $arr = explode(",",str);
        
      }//pg_num_rows($result)>0
      else{
        $res = new Result(Constant::INVALID_USER, 'User is not exist');
      }
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
