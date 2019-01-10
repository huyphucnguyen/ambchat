<?php 
header('Content-Type: application/json');
include '../lib/data.php';
$res = null;

if(isset($_GET['current_id']) && isset($_GET['friend_id'])&&isset($_GET['time_send'])&&isset($_GET['message'])){
    $current_id = $_GET['current_id'];
    $friend_id = $_GET['friend_id'];
    $time_send = $_GET['time_send'];
    $message = $_GET['message'];
    
    $sql = "SELECT * FROM public.conversation WHERE user_from = '$current_id' AND user_to = '$friend_id'";
    
    $dbconnection = new postgresql("");
    if($dbconnection->isValid()){
        $result = $dbconnection->select($sql);
        if($result!==null){
            
            if(pg_num_rows($result) > 0){
                $conversation_id = null;
                while ($data = pg_fetch_object($result)) {
                    $conversation_id = $data->conversation_id;
                    break;
                }
                $sql1 = "INSERT INTO public.conversation_detail(conversation_id,context_body,time_date)
                VALUES('$conversation_id','$message','$time_send')";
                $dbconnection->execute($sql1);
                $res = new Result(Constant::SUCCESS,'Save message successfully');
            }else{
                $sql2 = "INSERT INTO public.conversation(user_form,user_to,date_create)
                VALUES('$current_id','$friend_id','$time_send')";
                $dbconnection->execute($sql2);
                
                $sql3 = "SELECT conversation_id FROM public.conversation WHERE user_from = '$current_id' AND user_to = '$friend_id'";
                $result_sql3 = $dbconnection->select($sql3);
                if($result_sql3!==null){
                    if(pg_num_rows($result) > 0){
                        $data = pg_fetch_object($result);
                        $conversation_id = $data->conversation_id;
                        
                        $sql4 = "INSERT INTO public.conversation_detail(conversation_id,context_body,time_date)
                VALUES('$conversation_id','$message','$time_send')";
                        $dbconnection->execute($sql4);
                        $res = new Result(Constant::SUCCESS,'Save message successfully');
                    }
                    else{
                        $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
                    }
                    $dbconnection->closeResult($result_sql3);
                }
                else{
                    $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
                }
            }
            $dbconnection->closeResult($result);
        }else{
            $res = new Result(Constant::GENERAL_ERROR, 'There was an error while processing request. Please try again later.');
        }
        $dbconnection->close();
    }
    else{
        $res = new Result(Constant::INVALID_DATABASE , 'Database is invalid.');  
    }
}
else{
    $res = new Result(Constant::INVALID_PARAMETERS, 'Invalid parameters.');
}
echo (json_encode($res));
