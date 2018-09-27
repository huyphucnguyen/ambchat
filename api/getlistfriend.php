<?php 
$dbconn = pg_connect("host=ec2-54-227-241-179.compute-1.amazonaws.com
  port=5432 dbname=d4ieg9ce7qihnf user=doirzncaoefasd password=0955cadb61b87148265f253f9b11a740c24b806bbb7d9b24c2b992da74861a99");
$sql = "SELECT * FROM \"public\".\"user\" ";
//$data = $dbconnection->select($sql);
$data=pg_query($dbconn,$sql);
class User{
      function User($user_name){
           // $this->User_ID=$id;
            $this->User_Name=$user_name;
           // $this->FullName=$fullname;
      }
 }
   // tao mangA
     $arrUser=array();
   //Them phan tu vao amng
     while ($row=pg_fetch_array($data)) 
     {
        array_push($arrUser, new User($row['user_name']));
        //echo $row['user_name'];  
     }      
    // Chuyen dinh dang cua mang thanh JSON
     echo json_encode(mangUser);
     //echo count($arrUser);
?>
