<?php 
header('Content-Type: application/json');
$res = null;
$res = new Result(Constant::INVALID_PARAMETERS, 'Invalid parameters.');
if(isset($_GET['device_id']) && isset($_GET['message'])){
    $device_id = $_GET['device_id'];
    $message = $_GET['message'];
    
    /*include "../lib/PushMessage.php";
    include "../lib/firebase.php";
    
    $firebase = new Firebase();
    $push = new PushMessage();
    
    $push->setMessage($message);
    $json = '';
    $response = '';
    
    $json = $push -> getPush();
    $response = $firebase->send($device_id,$message);
    
    
    if($response==TRUE){
        $res = new Result(Constant::SUCCESS,$json);
    }
    else{
        $res = new Result(Constant::GENERAL_ERROR, 'Can not send the message. Please try again later.');
    }*/
}
else{
    $res = new Result(Constant::INVALID_PARAMETERS, 'Invalid parameters.');
}
echo (json_encode($res));
