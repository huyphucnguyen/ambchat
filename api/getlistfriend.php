<?php 
header('Content-Type: application/json');
include "../lib/data.php";
  $sql = "SELECT * FROM \"public\".\"user\" ";
     //$dbconnection=getDatabase();
    $dbconnection = new postgresql("");
    $data = $dbconnection->select($sql);
 
 class User{
      function User($user_id,$user_name,$fullname){
            $this->User_ID=$id;
            $this->User_Name=$user_name;
            $this->FullName=$fullname;
      }
 }
   // tao mang
      $mangUser=array();
   //Them phan tu vao amng
     while ($row=pg_fetch_assoc($data)) {
 	   array_push($mangUser, new User( $row['user_id'],$row['user_name'],$row['fullname']));
 	# code...
 }     
    // Chuyen dinh dang cua mang thanh JSON
      echo json_encode($mangUser);	
?>