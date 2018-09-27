<?php 
header('Content-Type: application/json');
include "../lib/data.php";
$dbconnection = new postgresql("");
$sql = "SELECT * FROM \"public\".\"user\" ";
$data = $dbconnection->select($sql);

   // tao mang
   
   //Them phan tu vao amng
     while ($row=pg_fetch_array($data)) 
     {
        //array_push($arrUser, new User("aaaaa"));
        echo "So phan tu cua mang la ";
       // echo "So phan tu cua mang la ";  
     }      
    // Chuyen dinh dang cua mang thanh JSON
     //echo json_encode(mangUser);
     //echo count($arrUser);
?>
