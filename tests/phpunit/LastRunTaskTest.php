<?php


namespace Rundiz\PhpSchedule\Tests;


class LastRunTaskTest extends \PHPUnit\Framework\TestCase
{


    public function testLastRunTasks()
    {
        $PhpSchedule = new PhpSchedule(__DIR__ . DIRECTORY_SEPARATOR . 'logs');
        $PhpSchedule->debug = true;
        $PhpSchedule->DateTimeNow->setTime(00, 00, 00);// reset time to midnight.

        $result = $PhpSchedule->lastRunTask('name1', '01');
        $this->assertFalse($result);// task time is more than current hour

        $result = $PhpSchedule->lastRunTask('name1', '00');
        $this->assertTrue($result);// OK.

        $result = $PhpSchedule->lastRunTask('name2', '00');
        $this->assertTrue($result);// OK.

        $PhpSchedule->DateTimeNow->add(new \DateInterval('PT1H'));
        $PhpSchedule->logDebug('+ Added 1 Hour. Now is ' . $PhpSchedule->DateTimeNow->format('Y-m-d H:i:s') . '.', 'lastRunTask');// 01:00
        $result = $PhpSchedule->lastRunTask('name1', '00');
        $this->assertFalse($result);// already runned (day different is not enough).

        $result = $PhpSchedule->lastRunTask('name1', '01');
        $this->assertTrue($result);// OK.

        $result = $PhpSchedule->lastRunTask('name1', '01');
        $this->assertFalse($result);// already runned (day different is not enough).

        $PhpSchedule->DateTimeNow->add(new \DateInterval('PT1H'));
        $PhpSchedule->logDebug('+ Added 1 Hour. Now is ' . $PhpSchedule->DateTimeNow->format('Y-m-d H:i:s') . '.', 'lastRunTask');// 02:00
        $result = $PhpSchedule->lastRunTask('name1', '00');
        $this->assertFalse($result);// already runned (day different is not enough).

        $result = $PhpSchedule->lastRunTask('name1', '01');
        $this->assertFalse($result);// already runned (day different is not enough).

        $PhpSchedule->DateTimeNow->add(new \DateInterval('PT1H'));
        $PhpSchedule->logDebug('+ Added 1 Hour. Now is ' . $PhpSchedule->DateTimeNow->format('Y-m-d H:i:s') . '.', 'lastRunTask');// 03:00
        $result = $PhpSchedule->lastRunTask('name2', '05');
        $this->assertFalse($result);// task time is more than current hour

        $PhpSchedule->DateTimeNow->add(new \DateInterval('PT1H'));
        $PhpSchedule->logDebug('+ Added 1 Hour. Now is ' . $PhpSchedule->DateTimeNow->format('Y-m-d H:i:s') . '.', 'lastRunTask');// 04:00
        $result = $PhpSchedule->lastRunTask('name2', '03');
        $this->assertTrue($result);// OK.

        $PhpSchedule->DateTimeNow->add(new \DateInterval('PT19H'));
        $PhpSchedule->logDebug('+ Added 19 Hours. Now is ' . $PhpSchedule->DateTimeNow->format('Y-m-d H:i:s') . '.', 'lastRunTask');// 23:00
        $result = $PhpSchedule->lastRunTask('name1', '00');
        $this->assertFalse($result);// already runned (day different is not enough).

        $PhpSchedule->DateTimeNow->add(new \DateInterval('PT1H'));
        $PhpSchedule->logDebug('+ Added 1 Hour. Now is ' . $PhpSchedule->DateTimeNow->format('Y-m-d H:i:s') . '.', 'lastRunTask');// 00:00 next day.
        $result = $PhpSchedule->lastRunTask('name1', '00');
        $this->assertTrue($result);// OK.

        $result = $PhpSchedule->lastRunTask('name1', '01');
        $this->assertFalse($result);// task time is more than current hour

        $PhpSchedule->DateTimeNow->add(new \DateInterval('PT2H'));
        $PhpSchedule->logDebug('+ Added 2 Hours. Now is ' . $PhpSchedule->DateTimeNow->format('Y-m-d H:i:s') . '.', 'lastRunTask');// 02:00
        $result = $PhpSchedule->lastRunTask('name1', '00');
        $this->assertFalse($result);// already runned (day different is not enough).

        $result = $PhpSchedule->lastRunTask('name1', '01');
        $this->assertTrue($result);// OK.

        $PhpSchedule->DateTimeNow->add(new \DateInterval('PT1H'));
        $PhpSchedule->logDebug('+ Added 1 Hour. Now is ' . $PhpSchedule->DateTimeNow->format('Y-m-d H:i:s') . '.', 'lastRunTask');// 03:00
        $result = $PhpSchedule->lastRunTask('name2', '05');
        $this->assertFalse($result);// task time is more than current hour

        $PhpSchedule->DateTimeNow->add(new \DateInterval('PT1H'));
        $PhpSchedule->logDebug('+ Added 1 Hour. Now is ' . $PhpSchedule->DateTimeNow->format('Y-m-d H:i:s') . '.', 'lastRunTask');// 04:00
        $result = $PhpSchedule->lastRunTask('name2', '03');
        $this->assertTrue($result);// OK.

        $PhpSchedule->DateTimeNow->add(new \DateInterval('PT19H'));
        $PhpSchedule->logDebug('+ Added 19 Hours. Now is ' . $PhpSchedule->DateTimeNow->format('Y-m-d H:i:s') . '.', 'lastRunTask');// 23:00
        $result = $PhpSchedule->lastRunTask('name1', '00');
        $this->assertFalse($result);// already runned (day different is not enough).

        $PhpSchedule->DateTimeNow->add(new \DateInterval('PT1H'));
        $PhpSchedule->logDebug('+ Added 1 Hour. Now is ' . $PhpSchedule->DateTimeNow->format('Y-m-d H:i:s') . '.', 'lastRunTask');// 00:00 next day.
        $result = $PhpSchedule->lastRunTask('name1', '00');
        $this->assertTrue($result);// OK.

        $PhpSchedule->DateTimeNow->add(new \DateInterval('PT24H'));
        $PhpSchedule->logDebug('+ Added 24 Hours. Now is ' . $PhpSchedule->DateTimeNow->format('Y-m-d H:i:s') . '.', 'lastRunTask');// 00:00 next day. Yes, next day.
        $result = $PhpSchedule->lastRunTask('name1', '00');
        $this->assertTrue($result);// OK.

        $PhpSchedule->DateTimeNow->add(new \DateInterval('PT24H'));
        $PhpSchedule->logDebug('+ Added 24 Hours. Now is ' . $PhpSchedule->DateTimeNow->format('Y-m-d H:i:s') . '.', 'lastRunTask');// 00:00 next day. Yes, next day - again.
        $result = $PhpSchedule->lastRunTask('name1', '00');
        $this->assertTrue($result);// OK.

        $result = $PhpSchedule->lastRunTask('name1', '01');
        $this->assertFalse($result);// task time is more than current hour

        $PhpSchedule->DateTimeNow->add(new \DateInterval('PT2H'));
        $PhpSchedule->logDebug('+ Added 2 Hours. Now is ' . $PhpSchedule->DateTimeNow->format('Y-m-d H:i:s') . '.', 'lastRunTask');// 02:00
        $result = $PhpSchedule->lastRunTask('name1', '00');
        $this->assertFalse($result);// already runned (day different is not enough).

        $result = $PhpSchedule->lastRunTask('name1', '01');
        $this->assertTrue($result);// OK.

        $PhpSchedule->DateTimeNow->add(new \DateInterval('PT1H10M'));
        $PhpSchedule->logDebug('+ Added 1 Hour 10 minutes. Now is ' . $PhpSchedule->DateTimeNow->format('Y-m-d H:i:s') . '.', 'lastRunTask');// 03:10
         $result = $PhpSchedule->lastRunTask('name1', '03');
        $this->assertTrue($result);// OK.

        $PhpSchedule->DateTimeNow->add(new \DateInterval('PT21H'));
        $PhpSchedule->logDebug('+ Added 21 Hours. Now is ' . $PhpSchedule->DateTimeNow->format('Y-m-d H:i:s') . '.', 'lastRunTask');// 00:10 next day.
        $result = $PhpSchedule->lastRunTask('name1', '00');
        $this->assertTrue($result);// OK.

        $PhpSchedule->DateTimeNow->add(new \DateInterval('PT2H50M'));
        $PhpSchedule->logDebug('+ Added 2 Hours 50 minutes. Now is ' . $PhpSchedule->DateTimeNow->format('Y-m-d H:i:s') . '.', 'lastRunTask');// 03:00
         $result = $PhpSchedule->lastRunTask('name1', '03');
        $this->assertTrue($result);// OK.
    }// testLastRunTasks


}