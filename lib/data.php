<?php
class Result
{
    public $error;
    public $message;
    public $data;
    public $sesson_key;
    public $token;
    public function __construct($error, $message)
    {
        $this->error = $error;
        $this->message = $message;
    }
    
    public function __construct1($error, $message, $sesson_key){
        $this->error = $error;
        $this->message = $message;
        $this->sesson_key = $sesson_key;
    }
}
class Token{
    public $user_id;
    public $device_id;
    public $time_start;
    public $time_life;
    
    public function __construct($user_id, $device_id,$time_start,$time_life)
    {
        $this->user_id = $user_id;
        $this->device_id = $device_id;
        $this->time_start = $time_start;
        $this->time_life = $time_life;
    }
}
class User{
    public $User_ID;
    public $User_Name;
    public $Full_Name;
    public $status;
    public function __construct($user_id,$user_name,$full_name,$status){
         $this->User_ID=$user_id;
         $this->User_Name=$user_name;
         $this->Full_Name=$full_name;
         $this->status=$status;
    }
 }

class Constant
{
    const INVALID_PARAMETERS = -2;

    const GENERAL_ERROR = -1;
    const SUCCESS = 0;

    const INVALID_USER = 1;
    const INVALID_PASSWORD = 2;

    const USER_EXIST = 3;
    const EMAIL_EXIST = 4;
    
    const INVALID_FRIEND_REQUEST = -5;
    const INVALID_FRIEND = -6;
    
    const INVALID_DATABASE = -3;
    const INVALID_TOKEN = -4;
    
    const KEY_ENCRYPT = "dBchm5yOuJxzL7oKVQWf";
    const TIME_LIFE = 300; //5 phút
    
 
}
