<?php

    header('Content-Type: application/json');
    include "../lib/data.php"; 
    include "../lib/db.php";
    include "../lib/function.php";
    $res = null;
    $sql = "SELECT * FROM \"public\".\"user\" ";
    $dbconnection = new postgresql("");

    if($dbconnection->isValid()) {
   	$result = $dbconnection->select($sql);
	if($result!==null){
		// create array user
                $arrUser=array();
		$data=null;
                 //add element to arrUser
                while ($row=pg_fetch_array($result)) 
                {
			$user_id =  $row['user_id'];
			$user_name = $row['user_name'];
			$full_name =  $row['full_name'];
			
			$status = "offline";
			if(getStatusUser($dbconnection,$user_id) == 1){
				$status = "online";
			}
			
                      array_push($arrUser, new User($user_id,$user_name,$full_name,$status));
                }
			  
		$res = new Result(Constant::SUCCESS, 'Operation complete successfully.');     
                $res->data = $arrUser;	
	}  else{
		$res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
	}
	$dbconnection->close();
         	 
	} else{
	        $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
	}
    // Chuyen dinh dang cua mang thanh JSON
    echo (json_encode($res));
?>
