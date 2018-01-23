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
        $PhpSchedule->add('localhost-secure', 'https://localhost', ['01']);
        include_once 'functionForTest.php';
        $PhpSchedule->add('callback-function', 'testCallbackFunction', ['00']);
        $PhpSchedule->add('callback-method', [$this, 'callbackMethod'], ['02']);

        $PhpSchedule->run();// localhost, callback-function 

        $PhpSchedule->DateTimeNow->add(new \DateInterval('PT1H'));
        $PhpSchedule->run();// localhost-secure

        $PhpSchedule->DateTimeNow->add(new \DateInterval('PT1H'));
        $PhpSchedule->run();// callback-method
    }// testRunTheTasks


}