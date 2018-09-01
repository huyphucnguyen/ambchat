<?php
header('Content-Type: application/json');
include "../lib/data.php";
//Lấy data từ client
$res = null;
if(isset($_POST['username'])&&isset($_POST['fullname'])&&isset($_POST['password'])&&
                        isset($_POST['email'])&&isset($_POST['image'])&&isset($_POST['gender'])){
  $username = $_POST['userName'];
  $fullName = $_POST['fullName'];
  $password = $_POST['password'];
  $email = $_POST['email'];
  $image = $_POST['image'];
  $gender = $_POST['gender'];

  //Kết nối database
  include ('../lib/db.php');
  //Kiểm tra tài khoản này có tồn tại chưa
  $sql_user = "select * from \"public\".\"user\" where user_name = {'$username'}";

  $dbconnection = new postgresql("");
  $result = $dbconnection->select($sql_user);
  
  if($result!=null){
    if(pg_num_rows($sql_dk)==0){
      /*Kiểm tra email có người đăng ký chưa. Email của user là duy nhất*/
      $sql_email = "select * from user where email = {'$email'}";
      $rs_email = $dbconnection->select($sql_email);
      if(pg_num_rows($rs_email)==0){
        $sql_dk = "insert into \"public\".\"user(user_name,pass_word,full_name,picture,email,date_create) 
        values ({'$username'},{'$password'},{'$fullName'},{'$image'},{'$email'},CURRENT_DATE)";
      }
      else{
        $res = new Result(Constant::EMAIL_EXIST, 'Email is already registered by another account');
      }
    }
    else{
       $res = new Result(Constant::USER_EXIST , 'User is exist');
    }
    $dbconnection->closeResult($result);
  }
  else{
    $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
  }
  $dbconnection->close();
 } else {
    $res = new Result(Constant::INVALID_PARAMETERS, 'Invalid parameters.');
}
echo (json_encode($res));
?>
