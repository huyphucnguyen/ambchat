<?php 
 header('Content-Type: application/json');
$res = null;
if(isset($_GET['user_id'])&&isset($_GET['friend_id'])){
  $user_id = $_GET['user_id'];
  $friend_id = $_GET['friend_id'];
 

  //Connect to database 
  include '../lib/db.php';
  $dbconnection = new postgresql("");
  if($dbconnection->isValid()){
    $sql = "SELECT user_id FROM public.friends where user_id = '$user_id'";
    $result = $dbconnection->select($sql);
  
    if($result !==null){
      //TH1: User is exits
      if(pg_num_rows($result)<=0){
        //ex: "'1','444','0545'"
        $sql_i = "INSERT INTO public.friends VALUES('$user_id','friend_id')";
        $dbconnection->execute($sql_i);
        $res = new Result(Constant::SUCCESS, 'Operation complete successfully.');
      } //pg_num_rows($result)>1
      //TH2: User is not exits
      else{
        $sql1 = "SELECT friend_id_list FROM public.friends WHERE user_id = '$user_id'";
        $result1 = $dbconnection->select($sql1);
        $str_frients = (pg_fetch_object($result1))->friend_id_list;
        if($str_friends!=null){
          $str_friends.= ',';
        }
        $str_friends.='$friend_id';
        $res = new Result(Constant::SUCCESS, 'Operation complete successfully.');
      }
    
    $dbconnection->closeResult($result);
    } //$result !==null
    else {
      $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
    }
    $dbconnection->close();
  
 }//$dbconnection->isValid()
  else{
    $res = new Result(Constant::INVALID_DATABASE , 'Database is invalid.');  
  }
  
}  //isset($_GET['user_id'])&&isset($_GET['friend_id'])
else {
  $res = new Result(Constant::INVALID_PARAMETERS, 'Invalid parameters.');
}
echo (json_encode($res));
