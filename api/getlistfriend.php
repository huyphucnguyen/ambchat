<?php 
header('Content-Type: application/json');
include "../lib/data.php";
$dbconnection = new postgresql("");
$sql = "SELECT * FROM \"public\".\"user\" ";
$data = $dbconnection->select($sql);

   // tao mangA
     $arrUser=array();
   //Them phan tu vao amng
      
    // Chuyen dinh dang cua mang thanh JSON
     //echo json_encode(mangUser);
     echo "so phan tu cua mang la". count($arrUser);
?>
