<?php 
function GUID()
{
    if (function_exists('com_create_guid') === true)
    {
        return trim(com_create_guid(), '{}');
    }

    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
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

//function get status online or ofline
function getStatusUser($dbconnection,$user_id){
    $sql = "SELECT * FROM public.user_online WHERE user_id = '$user_id'";
    $result = $dbconnection->select($sql);
    if($sql!==null){
        if(pg_num_rows($result) > 0){
            date_default_timezone_set("Asia/Ho_Chi_Minh"); 
            $time_now = time();
            while ($data = pg_fetch_object($result)) {
                $time_start = $data->time_start;
                $time_life = $data->time_life;
                
                if($time_start+$time_life >= $time_now){
                    return 1;
                }
            }
            return 0;
        }
        else{
            return 0;
        }
    }
    else{
        return 0;
    }
}
