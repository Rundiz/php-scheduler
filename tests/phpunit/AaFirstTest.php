<?php


namespace Rundiz\PhpSchedule\Tests;


class AaFirstTest extends \PHPUnit\Framework\TestCase
{


    public function testResetEverything()
    {
        $PhpSchedule = new PhpSchedule(__DIR__ . DIRECTORY_SEPARATOR . 'logs');
        $PhpSchedule->rrmdir(__DIR__ . DIRECTORY_SEPARATOR . 'logs');
        unset($PhpSchedule);

        usleep(200);

        $PhpSchedule = new PhpSchedule(__DIR__ . DIRECTORY_SEPARATOR . 'logs');
        $PhpSchedule->debug = true;
        $PhpSchedule->logDebug('Reset everything, clear log folder.');
    }// testResetEverything


}