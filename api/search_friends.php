<?php
header('Content-Type: application/json');
$res = null;
include "../lib/data.php";
if(isset($_GET['keysearch'])){
   $re = '/^\s*(\(?\+?\d{2}\)?|0)([-. ]*(\d{2,3})[-. ]*)?((\d{3})[-. ]*(\d{3,4}))?\s*$';
   //https://regex101.com/r/HfydMF/1
   $str = $_GET['keysearch'];
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
          if(pg_num_rows($result)>0){
              $arr = pg_fetch_all($result);
              $res = new Result(Constant::SUCCESS, 'Operation complete successfully.');
              $res->data = $arr;
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
