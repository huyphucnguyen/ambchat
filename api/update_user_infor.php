<?php
header('Content-Type: application/json');
include "../lib/data.php";

$res = null;
if (isset($_POST["username"]) && isset($_POST["fullname"]) && isset($_POST["email"]) && isset($_POST["gender"]) && isset($_POST["password"]) )  {
  $username = $_POST["username"];
  $fullname = $_POST["fullname"];
  $email = $_POST["email"];
  $gender = $_POST["gender"];
  $password = $_POST["password"];
  //Có cần xác nhận mật khẩu cũ mới cho cập nhật không?
  $sql = "UPDATE \"public\".\"user\" SET full_name='$username',email = '$email', pass_word = '$password',gender = 'gender' WHERE user_name = '$username'";
  
  $dbconnection = new postgresql("");
  
  $dbconnection->execute($sql);
  
  $res = new Result(Constant::SUCCESS,'Update user information successfully');
  
} else {
    $res = new Result(Constant::INVALID_PARAMETERS, 'Invalid parameters.');
}
echo (json_encode($res));}
