<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class block_quizsms extends block_base{
    public function init(){
        $this->title=  get_string('quizsms','block_quizsms');
    }
    
    public function get_content() {
         if ($this->content !== null) {
      return $this->content;
    }
 
    $this->content         =  new stdClass;
    $this->content->text   = 'The content of our quizSMS block!';
    $this->content->footer = 'Footer here...';
 
    return $this->content;
    }
    
    public function cron() {
        
        global $DB;
        
        $now = time();
        echo 'time';
        echo $now;
        
       // $instances = $DB->get_records('quiz',array('name'=> 'Defence quiz'));
        $instances = $DB->get_records_sql('select * from mdl_quiz');
        
                
        foreach ($instances as $record){
            echo 'here I am';
            echo $cid = $record->id;
            echo $quizName = $record->name;
            echo $startTime = $record->timeopen;
            
        
         $this->send_quiz_SMS($cid,$quizName,$startTime);
            
        }

        $difference = $now - $startTime;
        echo 'difference'.$difference;
        
        echo $difference;
        
        $con = mysql_connect("localhost", "root","");
        
        if(!$con){
            die("no connection!!!!!!!!11");
        }
        else {
            echo "connection established!!!!!!!1";
        }
        mysql_select_db("amaya_moodle",$con);
        
        if($difference>=0 && $difference <=1){
            echo 'inside if';
           $result = mysql_query("select shortname from mdl_course where id = $cid");
          
          if (!$result) { // add this check.
             die('Invalid query: ' . mysql_error());
          }
          
            $rows =  mysql_fetch_array($result);
            echo $rows['shortname']; 
        }
        else{
             echo 'inside else';
          $result = mysql_query("select shortname from mdl_course where id = $cid");
          
          if (!$result) { // add this check.
             die('Invalid query: ' . mysql_error());
          }
          
            $rows =  mysql_fetch_array($result);
            echo $rows['shortname'];  
        }

        mtrace( "Hey, my cron script is running" );
 
    
 
    return true;
}


public function send_quiz_SMS($courseid,$quizname,$startTime){
    
    $message = $this->create_SMS($courseid, $quizname, $startTime);
         $this->writeToFile($message);
    $this->sendSMS('+94718010490',$message,'+94711114843');
    
    
}

 function create_SMS($courseid,$quizname,$startTime){
     $SMS = 'Quiz:'.$quizname.' of '.$courseid.' started at '.$startTime;
     return $SMS;
 } 
 
 public function  writeToFile($message){
     $fp = fopen("/home/amaya/Desktop/myText1.txt","a");


            if($fp==false){
                echo 'oh fp is false'; 
            }
            else{
                fwrite($fp, $message);
                fclose($fp);
            }
 }


 public function sendSMS($in_number,$in_msg,$from)
 {

    $url = "/cgi-bin/sendsms?username=kannelUser&password=123&from={$from}&to={$in_number}&text={$in_msg}";

    $results = file('http://localhost:13013'.$url);
    
}


    
}
?>
