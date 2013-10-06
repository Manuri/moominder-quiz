<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class block_quizsms extends block_base {

    public function init() {
        $this->title = get_string('quizsms', 'block_quizsms');
    }

    public function get_content() {
        
        global $DB, $USER;

        if ($this->content !== null) {
            return $this->content;
        }

        /* $this->content         =  new stdClass;
          $this->content->text   = 'The content of our quizSMS block!';
          $this->content->footer = 'Footer here...'; */
        $this->content = new stdClass;
        $this->content->text = get_string('wantservice', 'block_quizsms');
        $this->content->text .= '<form id="form1" name="form1" method="post" action="">';
        $this->content->text .= '<table width="180" border="0"><tr>';
        $this->content->text .= '<td width="60"><input type="submit" name="ok" id="button" value="' . get_string('yes', 'block_quizsms') . '" a align="left"/></td>';
        $this->content->text .= '<td width="60"><input type="submit" name="no" id="button" value="' . get_string('no', 'block_quizsms') . '" a align="right"/></td>';
        $this->content->text .= '</tr> </table>';
        $this->content->text .= '</form>';
        
        echo 'checking....';
       // $this->get_input_from_interace();
        /////////////////////
        if (isset($_POST['ok'])) {  //if someone wants to subscribe for the SMS Forums Service
            $userid = $USER->id;
            $telno = $USER->phone2;
            $this->write_to_file($userid);

            if ($DB->record_exists('quizsms_subscriptions', array('userid' => $userid))) { //Check whether the user has already subscribed
                $this->content->text .= get_string('have_subscribed', 'block_quizsms');
            } else {
                if (strlen($telno) != 0) { //User should have enter his/her mobile phone no
                    $prefix_telno = get_string('prefix_telno', 'block_quizsms');
                    if (strpos($telno, $prefix_telno) !== false) { //The mobile phone no should be in the international format
                        $this->quizsms_service_subscribe($userid, $telno);
                        $this->content->text .= get_string('enabled', 'block_quizsms');
                    } else {
                        $this->content->text .= get_string('error_wrong_format', 'block_quizsms');
                    }
                } else {
                    $this->content->text .= get_string('error_no_telno', 'block_quizsms');
                }
            }
        }
        if (isset($_POST['no'])) {  //if someone doesn't want subcribe for the SMS Forums Service
            $userid = $USER->id;
            $this->quizsms_service_unsubscribe($userid);
            $this->content->text .= get_string('disabled', 'block_quizsms');
        }
        //////////////////////

        return $this->content;
    }

    //cron runs periodically according to the period that is set
    public function cron() {

        global $DB;

        $now = time();
        echo 'time';
        echo $now;

        $instances = $DB->get_records_sql('select * from mdl_quiz');


        foreach ($instances as $record) {
            echo $cid = $record->id;
            echo $quizName = $record->name;
            echo $startTime = $record->timeopen;
        }

        $difference = $now - $startTime;

        $this->db_connect();

        if ($difference >= 0 && $difference <= 1) {
            $result = mysql_query("select shortname from mdl_course where id = $cid");

            if (!$result) { // add this check.
                die('Invalid query: ' . mysql_error());
            }

            $rows = mysql_fetch_array($result);
            echo $rows['shortname'];
            $this->send_quiz_sms($cid, $rows['shortname'], $quizName, date("Y-m-d H:i:s", $startTime));
        } else {
            echo 'inside else';
            $result = mysql_query("select shortname from mdl_course where id = $cid");

            if (!$result) { // add this check.
                die('Invalid query: ' . mysql_error());
            }

            $rows = mysql_fetch_array($result);
            echo $rows['shortname'];
            $this->send_quiz_sms($cid, $rows['shortname'], $quizName, date("Y-m-d H:i:s", $startTime));
        }

        mtrace("Hey, my cron script is running");



        return true;
    }

    function send_quiz_sms($courseid, $coursename, $quizname, $startTime) {

        $message = $this->create_sms($courseid, $coursename, $quizname, $startTime);
       // $this->write_to_file($message);
        //$this->send_sms('+94718010490',$message,'+94711114843');
    }

    function create_sms($courseid, $coursename, $quizname, $startTime) {
        $sms = 'Quiz:' . $quizname . ' of ' . $courseid . ' ' . $coursename . ' started at ' . $startTime;
        return $sms;
    }

    //to test when sms gateway is not connected
    function write_to_file($message) {
        $fp = fopen("/home/amaya/Desktop/myText3.txt", "a");


        if ($fp == false) {
            echo 'oh fp is false';
        } else {
            fwrite($fp, $message);
            fclose($fp);
        }
    }

    //this function is to send SMS from gateway
    function send_sms($in_number, $in_msg, $from) {

        $url = "/cgi-bin/sendsms?username=kannelUser&password=123&from={$from}&to={$in_number}&text={$in_msg}";
        $url = str_replace(" ", "%20", $url);

        $results = file('http://localhost:13013' . $url);
    }

    //to connect with the database
    function db_connect() {
        $con = mysql_connect("localhost", "root", "");

        if (!$con) {
            die("no connection!!!!!!!!11");
        } else {
            echo "connection established!!!!!!!1";
        }
        mysql_select_db("amaya_moodle", $con);

        return $con;
    }

    //to get input from interface and subdcribe or unsubscribe
    function get_input_from_interace() {

        global $DB, $USER;

        if (isset($_POST['ok'])) {  //if someone wants to subscribe for the SMS Forums Service
            $userid = $USER->id;
            $telno = $USER->phone2;
            $this->write_to_file($userid." ".$telno);

            if ($DB->record_exists('quizsms_subscriptions', array('userid' => $userid))) { //Check whether the user has already subscribed
                $this->content->text .= get_string('have_subscribed', 'block_quizsms');
            } else {
                if (strlen($telno) != 0) { //User should have enter his/her mobile phone no
                    $prefix_telno = get_string('prefix_telno', 'block_quizsms');
                    if (strpos($telno, $prefix_telno) !== false) { //The mobile phone no should be in the international format
                        $this->quizsms_service_subscribe($userid, $telno);
                        $this->content->text .= get_string('enabled', 'block_quizsms');
                    } else {
                        $this->content->text .= get_string('error_wrong_format', 'block_quizsms');
                    }
                } else {
                    $this->content->text .= get_string('error_no_telno', 'block_quizsms');
                }
            }
        }
        if (isset($_POST['no'])) {  //if someone doesn't want subcribe for the SMS Forums Service
            $userid = $USER->id;
            $this->quizsms_service_unsubscribe($userid);
            $this->content->text .= get_string('disabled', 'block_quizsms');
        }
    }

    function quizsms_service_subscribe($userid, $telno) {

        global $DB;
        if ($DB->record_exists('quizsms_subscriptions', array("userid" => $userid))) {
            return true;
        }

        if ($userid != null && $telno != null) {

            $sub = new stdClass();
            $sub->userid = $userid;
            $sub->telno = $telno;
            return $DB->insert_record('quizsms_subscriptions', $sub);
        } else {
            mtrace("Userid or Telephone no is Null");
        }
    }

    function quizsms_service_unsubscribe($userid) {
        global $DB;
        if ($DB->record_exists('quizsms_subcriptions', array("userid" => $userid))) {
            $DB->delete_records('quizsms_subcriptions', array("userid" => $userid));
            return true;
        }
        return true;
    }

}

?>
