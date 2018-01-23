<?php


namespace Rundiz\PhpSchedule\Tests;


class AddTaskTest extends \PHPUnit\Framework\TestCase
{


    /**
     * @expectedException \Exception
     */
    public function testAllowedCharactersNameException()
    {
        $PhpSchedule = new \Rundiz\PhpSchedule\PhpSchdule(__DIR__ . DIRECTORY_SEPARATOR . 'logs');
        $PhpSchedule->add('name.1', 'http://localhost/', ['00']);
    }// testAllowedCharactersNameException


    /**
     * @expectedException \Exception
     */
    public function testDuplicatedNameException()
    {
        $PhpSchedule = new \Rundiz\PhpSchedule\PhpSchdule(__DIR__ . DIRECTORY_SEPARATOR . 'logs');
        $PhpSchedule->add('name1', 'http://localhost', ['00']);
        $PhpSchedule->add('name1', 'http://localhost', ['00']);
    }// testDuplicatedNameException


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNameStringException()
    {
        $PhpSchedule = new \Rundiz\PhpSchedule\PhpSchdule(__DIR__ . DIRECTORY_SEPARATOR . 'logs');
        $PhpSchedule->add(100, 'http://localhost/', ['00']);
    }// testNameStringException


    /**
     * @expectedException \Exception
     */
    public function testTimeDigitsException()
    {
        $PhpSchedule = new \Rundiz\PhpSchedule\PhpSchdule(__DIR__ . DIRECTORY_SEPARATOR . 'logs');
        $PhpSchedule->add('name1', 'http://localhost', ['0.1']);
    }// testTimeDigitsException


    public function testCorrectAdd()
    {
        $PhpSchedule = new \Rundiz\PhpSchedule\PhpSchdule(__DIR__ . DIRECTORY_SEPARATOR . 'logs');
        $PhpSchedule->add('name1', 'http://localhost', ['00', '03', '12', '15']);
        $PhpSchedule->add('name2', 'http://localhost', ['00', '03', '12', '15']);
    }// testCorrectAdd


}