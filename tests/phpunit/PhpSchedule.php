<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\PhpSchedule\Tests;


/**
 * Extend PhpSchedule class for testing, mostly for access protected properties, methods.
 *
 * @author vee
 */
class PhpSchedule extends \Rundiz\PhpSchedule\PhpSchdule
{


    public $DateTimeNow;


    public function __construct($logLocation = null)
    {
        if ($logLocation !== null && strpos($logLocation, dirname(dirname(__DIR__))) !== false && !is_dir($logLocation)) {
            $oldmask = umask(0);
            mkdir($logLocation, 0777, true);
            umask($oldmask);
        }

        return parent::__construct($logLocation);
    }// __construct


    public function lastRun()
    {
        return parent::lastRun();
    }// lastRun


    public function lastRunTask($name, $time)
    {
        return parent::lastRunTask($name, $time);
    }// lastRunTask


    public function logDebug($message, $file = '')
    {
        return parent::logDebug($message, $file);
    }// logDebug


    public function resetDateTimeNow()
    {
        return parent::resetDateTimeNow();
    }// resetDateTimeNow


    public function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") {
                        $this->rrmdir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }


}