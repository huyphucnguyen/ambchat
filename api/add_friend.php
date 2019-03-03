<?php 
 header('Content-Type: application/json');
$res = null;
include "../lib/data.php";
if(isset($_GET['user_id'])&&isset($_GET['friend_id'])){
  $user_id = $_GET['user_id'];
  $friend_id = $_GET['friend_id'];
 

  //Connect to database 
  include '../lib/db.php';
  $dbconnection = new postgresql("");
  if($dbconnection->isValid()){
     $sql = "SELECT * FROM public.friends WHERE user_id = '$user_id'";
     $result = $dbconnection->select($sql);
  
    if($result !==null){
      //TH1: User is exits
       if(pg_num_rows($result)==0){
        //ex: "'1','444','0545'"
        echo "why?";
        $sql_i = "INSERT INTO public.friends VALUES('$user_id','$friend_id')";
        $dbconnection->execute($sql_i);
         $res = new Result(Constant::SUCCESS, 'Operation complete successfully.');
      } //pg_num_rows($result)>1
      //TH2: User is not exits
      else{
        $data = pg_fetch_object($result);
        $str_frients = $data->friend_id_list;
        $arr = explode(',',$str_frients,-1);
//         if($str_friends.strlen()!=0){
//           $str_friends.= ',';
//         }
       $str_friends = implode(',',$arr);
        $str_friends=$str_friends.$friend_id;
       echo $str_friends;
       $sql_update = "UPDATE public.friends SET friend_id_list = '$str_friends' WHERE user_id = '$user_id'";
       $dbconnection->execute($sql_update);
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
