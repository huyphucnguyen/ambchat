<?php 

$result = null;
$res = null;

if(isset($_POST['device_id']) && isset($_POST['message'])){
    $device_id = $_POST['device_id'];
    $message = $_POST['message'];
    
    include "../lib/PushMessage.php";
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
    }
}
else{
    $res = new Result(Constant::INVALID_PARAMETERS, 'Invalid parameters.');
}
echo $res;
