<?php 
include ('../lib/db.php');
    $dbconn = getDatabase();
    
    $sql_dk = "insert into public.user values (12,'shokichi2','123456','Sho2','sho2.png','shokichi2@ovi.com',CURRENT_DATE)";
    pg_query($dbconn,$sql_dk);
?>
