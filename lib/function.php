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

function isFriend($dbconnection, $user_id1, $user_id2){
  $sql = "SELECT user_id FROM public.friends WHERE user_id = '$user_id1' AND (friend_id_list LIKE '%,$user_id2,%' OR friend_id_list LIKE '%,$user_id2' OR friend_id_list LIKE '$user_id2,%' OR friend_id_list LIKE '$user_id2')";
  $result = $dbconnection->select($sql);
  if($result!==null){
    if(pg_num_rows($result)>0){
      return true;
    }
  }
  return false;
}

function convertPhoneNumber($phoneNumber){
  //Remove all space in phone number string
  $phoneNumber = str_replace(' ', '', $phoneNumber);
  //get the last 9 numbers
  $phoneNumber = substr($phoneNumber,-9);
  return $phoneNumber;
}

function addFriendToList($dbconnection,$str_friends,$friend_id,$user_id){
  $arr = explode(",",$str_friends);
    if(!in_array($friend_id,$arr)){
      if(strlen($str_friends)!=0){
        $str_friends.=',';
      } 
      $str_friends.=$friend_id;
      $sql_update = "UPDATE public.friends SET friend_id_list = '$str_friends' WHERE user_id = '$user_id'";
      $dbconnection->execute($sql_update);
    }
}

function removeFriend($dbconnection,$user_id,$friend_id){
  $sql = "SELECT friend_id_list FROM public.friends WHERE user_id = '$friend_id'";
  $result = $dbconnection->select($sql);
  if($result!==null){
    if(pg_num_rows($result)>0){
      $friend_id_list = (pg_fetch_object($result))->friend_id_list;
      $arr = explode(",",$friend_id_list);
      if (in_array($user_id, $arr)){
        if(sizeof($arr)>1){
          unset($arr[array_search($user_id,$arr)]);
          $string2 = implode(",",$arr);
          //update
          $sql = "UPDATE public.friends SET friend_id_list = '$string2' WHERE user_id = '$friend_id'";
          $dbconnection->execute($sql);  
        }
        else{
          $sql = "DELETE FROM public.friends WHERE user_id = '$friend_id'"; 
          $dbconnection->execute($sql);
        }
        return true;
      }
    }
  }
  return false;
}
