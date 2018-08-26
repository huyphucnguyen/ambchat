<?php 
include ('../lib/db.php');
    $dbconn = getDatabase();
    
    $sql_dk = "insert into user value ('111','shokichi','123456','Sho','sho.png','shokichi@ovi.com',CURRENT_DATE";
    pg_query($dbconn,$sql_dk);
?>
