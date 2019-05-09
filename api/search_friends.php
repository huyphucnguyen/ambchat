<?php
header('Content-Type: application/json');
$res = null;
include "../lib/data.php";
if(isset($_POST['keysearch'])&&isset($_POST['user_id'])){
   $re = '/^\s*(^0|^(\(?\+?[1-9]{2}\)?))([-. ]*[1-9]{2}[-. ]*)\d{3}[-. ]*\d{4}?/m';
   $str = $_POST['keysearch'];
   $user_id = $_POST['user_id'];
   $sql=null;
   $matches = null;
    if(preg_match_all($re, $str, $matches, PREG_PATTERN_ORDER)){
      //$phone = convertPhoneNumber($matches[0][0]);
      $sql = "SELECT full_name,picture,email,gender,user_id,phone FROM public.user WHERE phone LIKE '%$phone%'";
    } //preg_match_all($re, $str, $matches, PREG_PATTERN_ORDER , 0
   else{
      $sql = "SELECT full_name,picture,email,gender,user_id,phone FROM public.user WHERE full_name LIKE '%$str%'";
   }
      
   if($sql!=null){
      include "../lib/db.php";
      include "../lib/functions.php";
      $dbconnection = new postgresql("");
      if($dbconnection->isValid()){
        $result = $dbconnection->select($sql);
        if($result !==null){
          $list_search = array();
          if(pg_num_rows($result)>0){
              while($data = pg_fetch_object($result)){
                 $user_id_found = $data->user_id;
                 if($user_id_found!=$user_id){
//                     $a_friend_b = isFriend($dbconnection,$user_id,$user_id_found);
//                     $b_friend_a = isFriend($dbconnection,$user_id_found,$user_id);

//                     if($a_friend_b == true && $b_friend_a == true){
//                       $data->friend_status = 1;
//                     }
//                     else if($a_friend_b == true || $b_friend_a == true){
//                       $data->friend_status = 0;
//                     }
//                     else {
//                       $data->friend_status = -1;
//                     }
                     
                     array_push($list_search,$data);
                     
                  }
              } 
              $res = new Result(Constant::SUCCESS, 'Operation complete successfully.');
              $res->data = $list_search;
             //Xử lý trạng thái bạn bè 
          } 
          else{
            $res = new Result(Constant::INVALID_USER, 'User is not exist');
          }
        }
        else{
           $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
        }
      } 
      else{
            $res = new Result(Constant::INVALID_DATABASE , 'Database is invalid.');  
        }
   } 
   else{
      $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
   }
}
else {
  $res = new Result(Constant::INVALID_PARAMETERS, 'Invalid parameters.');
}
echo (json_encode($res));
