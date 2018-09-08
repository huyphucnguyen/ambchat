<?php
header('Content-Type: application/json');
include "../lib/data.php";
//Lấy data từ client
$res = null;
if(isset($_POST['username'])&&isset($_POST['fullname'])&&isset($_POST['password'])&&
                        isset($_POST['email'])&&isset($_POST['gender'])){
  $username = $_POST['username'];
  $fullName = $_POST['fullname'];
  $password = $_POST['password'];
  $email = $_POST['email'];
  $gender = $_POST['gender'];

  //Kết nối database
  include ('../lib/db.php');
  //Kiểm tra tài khoản này có tồn tại chưa
  $sql_user = "select * from \"public\".\"user\" where user_name = '$username'";

  $dbconnection = new postgresql("");
  $result = $dbconnection->select($sql_user);
  
  if($result!=null){
    if(pg_num_rows($result)==0){
      /*Kiểm tra email có người đăng ký chưa. Email của user là duy nhất*/
      $sql_email = "select * from \"public\".\"user\" where email = '$email'";
      $rs_email = $dbconnection->select($sql_email);
      if(pg_num_rows($rs_email)==0){
        $sql_dk = "INSERT INTO public.user(user_name,pass_word,full_name,email,date_create,gender)
        values ('$username','$password','$fullName','$email',CURRENT_DATE,'$gender')";
        $dbconnection->execute($sql_dk);
        $dbconnection->closeResult($rs_email);
        $res = new Result(Constant::SUCCESS,'Registered successfully');
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
