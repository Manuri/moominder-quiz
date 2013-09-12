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

            /* $this->content         =  new stdClass;
             $this->content->text   = 'The content of our quizSMS block!';
             $this->content->footer = 'Footer here...';*/
              $this->content         =  new stdClass;
              $this->content->text   = get_string('wantservice','block_quizsms');
              $this->content->text   .= '<form id="form1" name="form1" method="post" action="">';
              $this->content->text	.= '<table width="180" border="0"><tr>';
              $this->content->text	.= '<td width="60"><input type="submit" name="ok" id="button" value="'.get_string('yes' , 'block_quizsms').'" a align="left"/></td>';
              $this->content->text	.= '<td width="60"><input type="submit" name="no" id="button" value="'.get_string('no' , 'block_quizsms').'" a align="right"/></td>';
              $this->content->text	.= '</tr> </table>';
              $this->content->text	.= '</form>';

            return $this->content;
        }

        public function cron() {

            global $DB;

            $now = time();
            echo 'time';
            echo $now;

            $instances = $DB->get_records_sql('select * from mdl_quiz');


            foreach ($instances as $record){
                echo $cid = $record->id;
                echo $quizName = $record->name;
                echo $startTime = $record->timeopen;          
            }

            $difference = $now - $startTime;

            $this->db_connect();

            if($difference>=0 && $difference <=1){
                $result = mysql_query("select shortname from mdl_course where id = $cid");          

                if (!$result) { // add this check.
                    die('Invalid query: ' . mysql_error());
                }

                $rows =  mysql_fetch_array($result);
                echo $rows['shortname']; 
                $this->send_quiz_sms($cid,$rows['shortname'],$quizName,date("Y-m-d H:i:s",$startTime));
            }
            else{
                 echo 'inside else';
                 $result = mysql_query("select shortname from mdl_course where id = $cid");

                 if (!$result) { // add this check.
                    die('Invalid query: ' . mysql_error());
                 }

                 $rows =  mysql_fetch_array($result);
                 echo $rows['shortname'];  
                 $this->send_quiz_sms($cid,$rows['shortname'],$quizName,date("Y-m-d H:i:s",$startTime));
            }

            mtrace( "Hey, my cron script is running" );



            return true;
        }


        function send_quiz_sms($courseid,$coursename,$quizname,$startTime){

            $message = $this->create_sms($courseid,$coursename, $quizname, $startTime);
            $this->write_to_file($message);
            //$this->send_sms('+94718010490',$message,'+94711114843');
         }

        function create_sms($courseid,$coursename, $quizname,$startTime){
            $sms = 'Quiz:'.$quizname.' of '.$courseid.' '.$coursename.' started at '.$startTime;
            return $sms;
        } 

        function  write_to_file($message){
            $fp = fopen("/home/amaya/Desktop/myText1.txt","a");


            if($fp==false){
                echo 'oh fp is false'; 
            }
            else{
                fwrite($fp, $message);
                fclose($fp);
            }
        }


        function send_sms($in_number,$in_msg,$from){

            $url = "/cgi-bin/sendsms?username=kannelUser&password=123&from={$from}&to={$in_number}&text={$in_msg}";
            $url = str_replace(" ", "%20", $url);

            $results = file('http://localhost:13013'.$url);

        }


        function db_connect(){
            $con = mysql_connect("localhost", "root","");

            if(!$con){
                die("no connection!!!!!!!!11");
            }
            else {
                echo "connection established!!!!!!!1";
            }
            mysql_select_db("amaya_moodle",$con);
            
            return $con;
        }

    }
?>
