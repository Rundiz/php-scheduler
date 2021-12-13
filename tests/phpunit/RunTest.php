<?php


namespace Rundiz\PhpSchedule\Tests;


class RunTest extends \PHPUnit\Framework\TestCase
{


    public function callbackMethod()
    {
        file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'runTest.log', 'Called from ' . __FILE__ . ':' . __LINE__);
    }// callbackMethod


    public function testRunTheTasks()
    {
        $PhpSchedule = new PhpSchedule(__DIR__ . DIRECTORY_SEPARATOR . 'logs');
        $PhpSchedule->debug = true;
        $PhpSchedule->DateTimeNow->setTime(00, 00, 00);// reset time to midnight.

        $PhpSchedule->add('localhost', 'http://localhost', ['00']);
        $PhpSchedule->add('localhost-secure', 'https://localhost', ['01']);// time is future (01) while current test is 00. can't run.
        include_once 'functionForTest.php';
        $PhpSchedule->add('callback-function', 'testCallbackFunction', ['00']);
        $PhpSchedule->add('callback-method', [$this, 'callbackMethod'], ['02']);// time is future (02) while current test is 00. can't run.

        $PhpSchedule->run();// localhost, callback-function 
        $this->assertFileExists($PhpSchedule->debugLogFolder . DIRECTORY_SEPARATOR . '[localhost]-task-results.log');
        $this->assertFileDoesNotExist($PhpSchedule->debugLogFolder . DIRECTORY_SEPARATOR . '[localhost-secure]-task-results.log');
        $this->assertFileExists($PhpSchedule->debugLogFolder . DIRECTORY_SEPARATOR . '[callback-function]-task-results.log');
        $this->assertFileDoesNotExist($PhpSchedule->debugLogFolder . DIRECTORY_SEPARATOR . '[callback-method]-task-results.log');

        $PhpSchedule->DateTimeNow->add(new \DateInterval('PT1H'));// add 1 hour past.
        $PhpSchedule->run();// localhost-secure can be run now
        $this->assertFileExists($PhpSchedule->debugLogFolder . DIRECTORY_SEPARATOR . '[localhost-secure]-task-results.log');

        $PhpSchedule->DateTimeNow->add(new \DateInterval('PT1H'));// add 1 hour past.
        $PhpSchedule->run();// callback-method can be run now
        $this->assertFileExists($PhpSchedule->debugLogFolder . DIRECTORY_SEPARATOR . '[callback-method]-task-results.log');
    }// testRunTheTasks


}