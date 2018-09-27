<?php 
header('Content-Type: application/json');
include "../lib/data.php";
$dbconnection = new postgresql("");
$sql = "SELECT * FROM \"public\".\"user\" ";
$data = $dbconnection->select($sql);
class user{
      function User($user_name){
           // $this->User_ID=$id;
            $this->User_Name=$user_name;
           // $this->FullName=$fullname;
      }
 }
   // tao mang
     $arrUser=array();
   //Them phan tu vao amng
     while ($row=pg_fetch_array($data)) 
     {
        array_push($arrUser, new User("aaaaa");
       // echo $row['user_name'];  
     }      
    // Chuyen dinh dang cua mang thanh JSON
     //echo json_encode(mangUser);
     echo count($arrUser);
?>
