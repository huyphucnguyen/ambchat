<?php 
function GUID()
{
    if (function_exists('com_create_guid') === true)
    {
        return trim(com_create_guid(), '{}');
    }

    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}

function createJsonWebToken($data){
    $header = json_encode(['type' => 'JWT','alg' => 'SH256']);   
    $payload = json_encode($data);
    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload =  str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
    $signature = hash_hmac('sha256',$base64UrlHeader . "." . $base64UrlPayload, 'Ambchat', true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    return $jwt;
}

function sendMessageToFCM($session = "" , $message = ""){
    $url = "https://fcm.googleapis.com/fcm/send";
    $serverKey = "AIzaSyBdWHaZD_B4MqS7Ja6BEP_TQthafMZO8t8";

    $fields = array();
    $fields['data'] = $message;
    if (is_array($session)) {
        $fields['registration_ids'] = $session;
    } else {
        $fields['to'] = $session;
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
        die('FCM Send Error: '  .curl_error($curl));
    }
    curl_close($curl);
    return $result;
}
