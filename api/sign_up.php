<?php
header('Content-Type: application/json');
//Lấy data từ client
if(isset($_POST['userName'])){
    $username = $_POST['userName'];
    $fullName = $_POST['fullName'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $image = $_POST['image'];
    $sex = $_POST['sex'];

    include ('../lib/db.php');
    $dbconn = getDatabase();
    //Kiểm tra tài khoản này có tồn tại chưa
    $sql_user = "select * from user where user_name = {'$username'}";
    $result = pg_query($dbconn,$sql_dk);
    if(pg_num_rows($sql_dk)>0){
        echo('{"value": -1, "message": "Tài khoản đã tồn tại!"}'); //Tài khoản đã tồn tại
        exit();
    }

    $sql_email = "select * from user where email = {'$email'}";
    $rs_email = pg_query($dbconn,$sql_email);
    if(pg_num_rows($rs_email)>0){
        echo('{"value": -2, "message": "Email đã được đăng ký!"}'); //Email đã được đăng ký bởi tài khoản khác
        exit();
    }

    $sql_dk = "insert into user(user_name,pass_word,full_name,picture,email,date_create) value ({'$username'},{'$password'},{'$fullName'},{'$image'},{'$email'}),{CURRENT_DATE}";
    pg_query($dbconn,$sql_email);

    echo('{"value": 1, "message": "Đăng ký thành công!"}');
}
?>
