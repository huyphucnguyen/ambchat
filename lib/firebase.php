<?php
class Firebase{
  public function send($to, $message){
    $fields = array(
      'to' => $to,
      'data' => $message,
    );
    return $this->sendPushNotification($fields);
  }
}

/**function makes curl request to firebase servers
*@param $fields is data with message and destination*/
  private function sendPushNotification($fields){
    define('FIREBASE_API_KEY','');
    
    $url = 'https://fcm.googleapis.com/fcm/send';
    $headers = array(
      'Authorization: key=' .FIREBASE_API_KEY,
      'Content-Type: application/json'
    );
    //Opent connection
    $ch = curl_init();
    
    //Set the url, number POST vars, POST data
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_POST,true);
    curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    
    //Disabling SSL Certificate support temporarly
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($fields));
    
    //Execute
    $result = curl_exec($ch);
    if($result === FALSE){
      die('Curl failed: '.curl_error($ch));
    }
    
    //close connection
    curl_close();
    
    return $result; // TRUE
  }
