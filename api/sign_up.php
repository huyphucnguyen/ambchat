<?php
header('Content-Type: application/json');
include "../lib/data.php";
//Lấy data từ client
$res = null;
if(isset($_POST['userName'])&&isset($_POST['fullName'])&&isset($_POST['password']&&
                        isset($_POST['email'])&&isset($_POST['image'])&&isset($_POST['gender'])){
    $username = $_POST['userName'];
    $fullName = $_POST['fullName'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $image = $_POST['image'];
    $gender = $_POST['gender'];

    include ('../lib/db.php');
    $dbconn = getDatabase();
    //Kiểm tra tài khoản này có tồn tại chưa
    $sql_user = "select * from user where user_name = {'$username'}";
    $result = pg_query($dbconn,$sql_dk);
    if(pg_num_rows($sql_dk)>0){
        echo('{"value": "-1"}'); //Tài khoản đã tồn tại
        exit();
    }

    $sql_email = "select * from user where email = {'$email'}";
    $rs_email = pg_query($dbconn,$sql_email);
    if(pg_num_rows($rs_email)>0){
        echo('{"value": "-2"}'); //Email đã được đăng ký bởi tài khoản khác
        exit();
    }

    $sql_dk = "insert into public.user(user_name,pass_word,full_name,picture,email,date_create) values ({'$username'},{'$password'},{'$fullName'},{'$image'},{'$email'},CURRENT_DATE)";
    if(pg_query($dbconn,$sql_dk)){
        echo('{"value": "1"}');
    }
    else{
       echo('{"value": "-3"}');
        exit();
    }
}
?>
