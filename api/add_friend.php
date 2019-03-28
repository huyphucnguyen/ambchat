<?php 
 header('Content-Type: application/json');
$res = null;
include "../lib/data.php";
if(isset($_POST['user_id'])&&isset($_POST['friend_id'])){
  $user_id = $_POST['user_id'];
  $friend_id = $_POST['friend_id'];
 

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
          $sql_i = "INSERT INTO \"public\".\"friends\" VALUES('$user_id','$friend_id')";
          $dbconnection->execute($sql_i);
       } //pg_num_rows($result)==0
       //TH2: User is not exits
       else{
          $data = pg_fetch_object($result);
          $str_friends = $data->friend_id_list;

          //Check friend_id is exits
          $arr = explode(",",$str_friends);
          if(!in_array($friend_id,$arr)){
            if(strlen($str_friends)!=0){
              $str_friends.=',';
            } //strlen($str_friends)!=0
            $str_friends.=$friend_id;
            $sql_update = "UPDATE public.friends SET friend_id_list = '$str_friends' WHERE user_id = '$user_id'";
            $dbconnection->execute($sql_update);
          }//!in_array($friend_id,$arr)
          
        }
     
     //Xoa request friend neu co
     $sql = "SELECT request_id FROM public.request_friends WHERE user_id = '$user_id'";
     $result2 = $dbconnection->select($sql);
     if($result2!==null){
        if(pg_num_rows($result2)>0){
            $request_list = (pg_fetch_object($result))->request_id;
            $arr = explode(",",$request_list);
            if (in_array($friend_id, $arr)) {
                if(sizeof($arr)>1){
                    unset($arr[array_search($friend_id,$arr)]);
                    $string = implode(",",$arr);
                    //update
                    $sql = "UPDATE public.request_friends SET request_id = '$string' WHERE user_id = '$user_id'";
                    $dbconnection->execute($sql);
                }//sizeof($arr)>1
                else {
                    $sql = "DELETE FROM public.request_friends WHERE user_id = '$user_id'"; 
                    $dbconnection->execute($sql);
                }
            }//in_array($friend_id, $arr)
        }//pg_num_rows($result2)>0
     }//$result2!==null
     else{
        $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
     }
    $res = new Result(Constant::SUCCESS, 'Operation complete successfully.');
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
