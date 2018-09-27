<?php 
header('Content-Type: application/json');
include "../lib/data.php";
$dbconnection = new postgresql("");
$sql = "SELECT * FROM \"public\".\"user\" ";
$data = $dbconnection->select($sql);
//$data = pg_query($dbconn,$sql);
 
 /*class User{
      function User($user_id,$user_name,$fullname){
            $this->User_ID=$id;
            $this->User_Name=$user_name;
            $this->FullName=$fullname;
      }
 }*/
   // tao mang
   //   $mangUser=array();
   //Them phan tu vao amng
     while ($row=pg_fetch_array($data)) 
     {
       // array_push($mangUser, new User( $row['user_id'],$row['user_name'],$row['full_name']));
      echo $row['user_name'];
     
     }      
    // Chuyen dinh dang cua mang thanh JSON
     //echo count(mangUser);
?>
