<?php
header('Content-Type: application/json');
$res = null;
include "../lib/data.php";
if(isset($_POST['keysearch'])&&isset($_POST['user_id'])){
   $re = '/^\s*(^0|^(\(?\+?[1-9]{1,3}\)?))?([-. ]*[1-9]\d{2}[-. ]*)?\d{2}[-. ]*\d{4}?/m';
   //https://regex101.com/r/HfydMF/2
   $str = $_POST['keysearch'];
   $user_id = $_POST['user_id'];
   $sql=null;
   $matches = null;
    if(preg_match_all($re, $str, $matches, PREG_PATTERN_ORDER)){
       $sql = "SELECT full_name,picture,email,gender,user_id,phone FROM public.user WHERE phone LIKE '$str%'";
    } //preg_match_all($re, $str, $matches, PREG_PATTERN_ORDER , 0
   else{
      $sql = "SELECT full_name,picture,email,gender,user_id,phone FROM public.user WHERE full_name LIKE '%$str%'";
   }
      
   if($sql!=null){
      include "../lib/db.php";
      $dbconnection = new postgresql("");
      if($dbconnection->isValid()){
        $result = $dbconnection->select($sql);
        if($result !==null){
          $list_search = array();
          if(pg_num_rows($result)>0){
              while($data = pg_fetch_object($result)){
                 $user_id_found = $data->user_id;
                 $sql = "SELECT * FROM public.friends WHERE user_id = '$user_id'";
                 $result_fr = $dbconnection->select($sql);
                 if($result_fr!==null){
                    if(pg_num_rows($result_fr)>0){
                        $friend_list = pg_fetch_row($result_fr);
                        $friend_list = '('.$friend_list.')';
                        $arr = explode(",",$friend_list);
                        if(in_array($user_id_found,$arr)){
                           $sql2 = "SELECT * FROM public.friends WHERE user_id = '$user_id_found'";
                           $result_fr2 = $dbconnection->select($sql2);
                           if($result_fr2!==null){
                                if(pg_num_rows($result_fr2)>0){
                                   $friend_list_found = (pg_fetch_object($result_fr2))->friend_id_list;
                                   $arr1 = explode(",",$friend_list_found);
                                   if(in_array($user_id,$arr1)){
                                      $data->friend_status = 1;
                                   }//in_array($user_id,$arr1)
                                   else{
                                       $data->friend_status = 0;
                                   }
                                    $sql3 = "SELECT * FROM public.friends WHERE user_id = '$user_id_found'";
                                }//pg_num_rows($result_fr2)>0
                                 else{
                                    $data->friend_status = 0;
                                 }
                           }//$result_fr2!==null
                           else{
                              $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
                           }
                        }//in_array($user_id_found,$arr
                        else {
                           $data->friend_status = -1;
                        }
                        
                    }//pg_num_rows($result_fr)>0
                    else{
                        $data->friend_status = -1;
                    }
                    
                 }//$result_fr!==null
                 else {
                     $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
                 }
                 array_push($list_search,$data);
              } //while
              $res = new Result(Constant::SUCCESS, 'Operation complete successfully.');
              $res->data = $list_search;
             //Xử lý trạng thái bạn bè
             
          } //pg_num_rows($result)>0
          else{
            $res = new Result(Constant::INVALID_USER, 'User is not exist');
          }
        }//$result !==null
        else{
           $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
        }
      } //$dbconnection->isValid()
      else{
            $res = new Result(Constant::INVALID_DATABASE , 'Database is invalid.');  
        }
   } //$sql!=null
   else{
      $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
   }
} //isset($_GET['keysearch'])
else {
  $res = new Result(Constant::INVALID_PARAMETERS, 'Invalid parameters.');
}
echo (json_encode($res));
