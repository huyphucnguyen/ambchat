<?php
function getDatabase(){
  $dbconn = pg_connect("host=ec2-54-227-241-179.compute-1.amazonaws.com 
  port=5432 dbname=d4ieg9ce7qihnf user=doirzncaoefasd password=0955cadb61b87148265f253f9b11a740c24b806bbb7d9b24c2b992da74861a99")
  or die("Connect falsed");
  
  return $dbconn;
}
?>
