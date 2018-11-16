<?php
class Result
{
    public $error;
    public $message;
    public $data;
    public $sesson_key;
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
class Constant
{
    const INVALID_PARAMETERS = -2;

    const GENERAL_ERROR = -1;
    const SUCCESS = 0;

    const INVALID_USER = 1;
    const INVALID_PASSWORD = 2;

    const USER_EXIST = 3;
    const EMAIL_EXIST = 4;
    
    const INVALID_DATABASE = -3;
}
