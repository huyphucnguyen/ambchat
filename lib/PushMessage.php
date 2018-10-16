<?php
class PushMessage{
  private $title;
  private $message;
  private $data;
  
  function __construct(){
  }
  
  public function setTitle($title){
    $this->title = $title;
  }
  
  public function setMessage($message){
    $this->message = $message;
  }
  
  public function setData($data){
    $this->data = $data;
  }
  
  /**
  *get data contain message
  *@return a array contain data*/
  public function getPush(){
    $res = array();
    $res['data']['title'] = $this->title;
    $res['data']['message'] = $this->message;
    $res['data']['category']=$this->data;
    $res['data'][timestamp'] = date('d-m-Y G:i:s');
    return $res;
  }
  
}
