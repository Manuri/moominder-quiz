<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//require_once '../../../config.php';
require_once '/var/www/moodle/blocks/moodleblock.class.php';
require_once '/var/www/moodle/blocks/quizsms/block_quizsms.php';

//class block_quizsms_test extends PHPUnit_Framework_TestCase{
class block_quizsms_test extends advanced_testcase{
    
    var $testBlock;
    
     function setUp() {
        $this->testBlock = new block_quizsms();
    }
    
    public function test_adding() {
         $this->assertEquals(3, 1+2);
     }
     
     public function test_sendSMS(){
         $this->testBlock->sendSMS('+94718010490','unit testing 1','+94711114843');
         
     }
     
     public function test_writeToFile(){
         $this->testBlock->writeToFile("unit testing");
     }
     
     
}

?>
