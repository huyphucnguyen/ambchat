<?php 
include "../lib/function.php";
$result = null;
$res = null;

if(isset($_POST['session']) && isset($_POST['message'])){
    $session = $_POST['session'];
    $message = $_POST['message'];

    $resutl = sendMessageToFCM($session,$message);
    if($result!=null){
        $res = new Result(Constant::SUCCESS,$message);
    }
    else{
        $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
    }
}
else{
    $res = new Result(Constant::INVALID_PARAMETERS, 'Invalid parameters.');
}
echo $res;
