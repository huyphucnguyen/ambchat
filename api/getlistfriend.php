<?php

    header('Content-Type: application/json');
    include "../lib/data.php"; 
    include "../lib/db.php";
	$res = null;
	$sql = "SELECT * FROM \"public\".\"user\" ";
    $dbconnection = new postgresql("");
	echo "aaaa2";
	if($dbconnection->isValid())
	  {
		  	echo "bbbb";
		   $result = $dbconnection->select($sql);
		   ////
		   class User{
                 function User($user_id,$user_name,$full_name){
                             $this->User_ID=$user_id;
                             $this->User_Name=$user_name;
                             $this->Full_Name=$full_name;
                        }
                     }
		  if($result!==null){
			  echo "cccc";
			     // create array user
                $arrUser=array();
				$data=null;
                 //add element to arrUser
                while ($row=pg_fetch_array($result)) 
                {
					echo  $row['user_id'];
                      array_push($arrUser, 
					             new User(
								 $row['user_id'],
								 $row['user_name'],
								 $row['full_name']));
                }
			  $guid = GUID();
			  $res = new Result(Constant::SUCCESS, 'Operation complete successfully.',$guid);     
              $res->data = $arrUser;	
		  }
		  else{
			    $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
			  }
			  
	     $dbconnection->close();
         	 
	  }
	  else{
	        $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
	  }
    // Chuyen dinh dang cua mang thanh JSON
    echo (json_encode($res));
?>
