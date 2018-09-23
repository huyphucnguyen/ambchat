<?php 
function GUID()
{
    if (function_exists('com_create_guid') === true)
    {
        return trim(com_create_guid(), '{}');
    }

    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}

function sendMessageToFCM($device_id = "" , $message = "", $push_data = array()){
    $url = "https://fcm.googleapis.com/fcm/send";
    $serverKey = "AIzaSyBdWHaZD_B4MqS7Ja6BEP_TQthafMZO8t8";
    
    $msg = array(
        'message' = $message;
        'data' = $push_data;
    );
    $fields = array();
    $fields['data'] = $msg;
    if (is_array($device_id)) {
        $fields['registration_ids'] = $device_id;
    } else {
        $fields['to'] = $device_id;
    }
    $headers = array(
        'Content-Type:application/json',
        'Authorization:key=' . $serverKey
    ); 
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($curl);
    if ($result === FALSE) {
        die('FCM Send Error: '  .  curl_error($ch));
    }
    curl_close($ch);
    return $result;
}
