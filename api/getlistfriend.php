<?php

    header('Content-Type: application/json');
    include "../lib/data.php"; 
    include "../lib/db.php";
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
			echo $row['user_id'];
			echo $row['user_name'];
			echo $row['full_name'];
                      array_push($arrUser, new User(
						 $row['user_id'],
						 $row['user_name'],
					     	 $row['full_name']));
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
