<?php


namespace Rundiz\PhpSchedule\Tests;


class LastRunTest extends \PHPUnit\Framework\TestCase
{


    public function testLastRunPastXMinutes()
    {
        $PhpSchedule = new PhpSchedule(__DIR__ . DIRECTORY_SEPARATOR . 'logs');
        $PhpSchedule->debug = true;
        $PhpSchedule->lastRun();

        $result = $PhpSchedule->lastRun();
        $this->assertFalse($result);

        $PhpSchedule->logDebug('+ Adding 50 minutes.', 'lastRun');
        $PhpSchedule->DateTimeNow->add(new \DateInterval('PT50M'));
        $result = $PhpSchedule->lastRun();
        $this->assertFalse($result);

        $PhpSchedule->logDebug('+ Adding 60 minutes.', 'lastRun');
        $PhpSchedule->DateTimeNow->add(new \DateInterval('PT60M'));
        $result = $PhpSchedule->lastRun();
        $this->assertTrue($result);

        $PhpSchedule->logDebug('+ Adding 120 minutes.', 'lastRun');
        $PhpSchedule->DateTimeNow->add(new \DateInterval('PT120M'));
        $result = $PhpSchedule->lastRun();
        $this->assertTrue($result);

        $PhpSchedule->logDebug('+ Adding 59 minutes.', 'lastRun');
        $PhpSchedule->DateTimeNow->add(new \DateInterval('PT59M'));
        $result = $PhpSchedule->lastRun();
        $this->assertFalse($result);

        $PhpSchedule->resetDateTimeNow();
    }// testLastRunPastXMinutes


}