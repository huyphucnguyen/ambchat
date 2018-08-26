
<?php
//Test 

    if(isset($_POST["username"])){

         $username=$_POST["username"];
         $password=$_POST["password"];
         $querry1="select * from user where user_name='$username' ";
         // ket noi database
        include ("../lib/db.php");
        $dbconnection=getDatabase();
         $result=pg_query($dbconnection,$querry1);
         if(pg_num_rows($result)==0){
            echo ('{"values":-1,"message":"User không tồn tại"}');
            exit();
         }
         $querry2="select * from user where (user_name='$username' AND pass_word='$password')";
         $result=pg_query($dbconnection,$querry2);
         if(pg_num_rows($result)==0){
             echo ('{"values":-2,"message":"Sai password"}');
         }

         echo ('{"values": 1,"message": "đăng nhập thành công" }');
?>
