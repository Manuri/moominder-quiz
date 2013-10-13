<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//require_once '../../../config.php';
    require_once '/var/www/moodle/blocks/moodleblock.class.php';
    require_once '/var/www/moodle/blocks/quizsms/block_quizsms.php';

    class block_quizsms_test extends advanced_testcase{

        var $testBlock;

         function setUp() {
            $this->testBlock = new block_quizsms();
         }


         public function test_sendSMS(){
            // $this->testBlock->send_sms('+94718010490','unit%20testing%201','+94711114843');
               $this->testBlock->send_sms('+94718010490','unit%20testing%201','+94720728002');

         }

         public function test_writeToFile(){
             $this->testBlock->write_to_file("unit testing");
         }

         public function test_db_connect(){
             $this->assertNotNull($this->testBlock->db_connect());
         }
         
         public function test_quizsms_service_subscribe(){
             global $DB;
             $this->resetAfterTest(true);
             $this->testBlock->quizsms_service_subscribe(10,'9999999999');
             //$this->assertEquals(4, $DB->count_records('quizsms_subscriptions',array()));
             $this->assertEquals(1, $DB->count_records('quizsms_subscriptions', array('userid'=>10)));
         }
         
         public function test_quizsms_service_unsubscribe(){
             global $DB;
             
             $this->resetAfterTest(true);
             $this->testBlock->quizsms_service_subscribe(10,'9999999999');
             $this->assertTrue($this->testBlock->quizsms_service_unsubscribe(10),'Un-subscription is not working');
             
         }
         
         public function test_check_courses_and_subscribed_users(){
             
         }

         public function test_api_function(){
             global $DB;
             
             //$this->assertEmpty($DB->get_records('quizsms_subscriptions'),'not empty');
             // $this->assertEmpty($DB->get_records('course'),'not empty');
         }
    }

?>
