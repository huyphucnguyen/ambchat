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

function encryptData($data,$key){
    $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext_raw = openssl_encrypt($data, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
    $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
    $ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );
    return $ciphertext;
}

function dencryptData($ciphertext,$key){
    $c = base64_decode($ciphertext);
    $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
    $iv = substr($c, 0, $ivlen);
    $hmac = substr($c, $ivlen, $sha2len=32);
    $ciphertext_raw = substr($c, $ivlen+$sha2len);
    $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
    $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
    if (hash_equals($hmac, $calcmac))//PHP 5.6+ timing attack safe comparison
    {
        return $original_plaintext;
    }
    return null;
}



function setOnline($dbconnection,$user_id,$device_id){
    //Tiến hành ghi bảng user_history và xóa bỏ các records có guid của bảng user_online
    //trùng với guid trong bảng user_history có user_id và device_id trùng khớp
    $sql_getRe = "SELECT * FROM public.user_online WHERE user_id = '$user_id' and device_id = '$device_id'";
    $result_getRe = $dbconnection->select($sql_getRe);
    if($result_getRe !== null){
        date_default_timezone_set("Asia/Ho_Chi_Minh"); 
        $time = time();
        if (pg_num_rows($result_getRe) > 0){
            //Có tồn tại thì cập nhật
            $sql_update = "UPDATE public.user_online SET time_start = '$time' WHERE user_id = '$user_id' and device_id = '$device_id'";
            $dbconnection->execute($sql_update);
        } else{
            //Không tồn tại thì insert vô | time_life = 5 phút: 300
            $sql_insert_hi = "INSERT INTO public.user_online VALUES('$user_id','$device_id','$time',300)";
            $dbconnection->execute($sql_insert_hi);
        }
        $dbconnection->closeResult($result_getRe);
    } else{
        //Trả về thông báo lỗi => Đã đăng nhập thành công thì có thông báo thành công => có cần thông báo không?
    }    
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
