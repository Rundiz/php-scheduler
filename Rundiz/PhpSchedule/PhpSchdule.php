<?php
/**
 * Schedule class.
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\PhpSchedule;


/**
 * Run the schedule.
 *
 * @author vee
 */
class PhpSchdule
{


    /**
     * Current date/time in PHP DateTime object for check with last run tasks. It can be change if class was extended and add the time to this object.
     * 
     * @var \DateTime 
     */
    protected $DateTimeNow;


    /**
     * Debug message?
     * 
     * @var boolean
     */
    public $debug = false;


    /**
     * Debug log folder. Do not contain trailing slash.
     * 
     * @var string
     */
    protected $debugLogFolder;


    /**
    * Contain tasks to run.
     * 
    * @var array $tasks
    */
    protected $tasks = [];


    /**
     * PHP Time zone. This is useful for run task log and debug log.
     * 
     * @var string $timeZone
     */
    public $timeZone = 'Asia/Bangkok';


    /**
     * PHP Schedule class constructor.
     * 
     * @param string $logLocation The full path to log folder location. Do not contain trailing slash. Set to null to use default. You must create folder before use.
     */
    public function __construct($logLocation = null)
    {
        if ($logLocation !== null) {
            $this->debugLogFolder = $logLocation;
        } else {
            $DateTime = new \DateTime('now', new \DateTimeZone($this->timeZone));
            $this->debugLogFolder = __DIR__ . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . $DateTime->format('Y-m');
            unset($DateTime);

            if (!is_dir($this->debugLogFolder)) {
                $oldmask = umask(0);
                mkdir($this->debugLogFolder, 0777, true);
                umask($oldmask);
            }
        }

        $this->resetDateTimeNow();
    }// __construct


    /**
     * Add a task.
     *
     * @param string $name The task name must be unique. English characters, numbers, -, _, +, =, [, ], (, ) are allowed otherwise will throw the error.
     * @param string|array $url The URL or callback function.
     * @param array $time The hour to run the task in two digit numbers. Example: 00 is midnight, 12 is midday, 13 is 1 pm, 01 is 1 am after midnight.
     * @throws \InvalidArgumentException|\Exception
     */
    public function add($name, $url, array $time)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('The argument $name must be string.');
        }

        if (preg_match('#[^a-zA-Z0-9\-\_\+\=\[\]\(\)]+#iu', $name)) {
            throw new \Exception('The argument $name must be allowed characters.');
        }

        if (is_array($this->tasks) && array_key_exists($name, $this->tasks)) {
            throw new \Exception('The task name ' . $name . ' is already exists. Please name the task unique.');
        }

        if (!is_string($url) && !is_array($url)) {
            throw new \InvalidArgumentException('The argument $url must be string or array of callback function.');
        }

        if (!is_array($time)) {
            throw new \InvalidArgumentException('The argument $time must be array.');
        } else {
            foreach ($time as $a_time) {
                if (!preg_match('#^[0-9]{2}$#', $a_time)) {
                    throw new \Exception('The time must be two digit numbers as string in each array value (' . $a_time . ').');
                }
            }// endforeach;
            unset($a_time);
        }

        $this->tasks[$name] = [
            'name' => $name,
            'url' => $url,
            'time' => $time,
        ];
    }// add


    /**
     * Run the task URL.
     * 
     * @param string $url
     * @return mixed Return HTTP status code that was response or error message.
     */
    protected function doRunTaskUrl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Rundiz PHP Schedule. Fork me on Github!');

        curl_exec($ch);

        if (!curl_errno($ch)) {
            $info = curl_getinfo($ch);
            if (is_array($info) && array_key_exists('http_code', $info)) {
                $info = $info['http_code'];
            }
        } else {
            $info = curl_error($ch);
        }

        curl_close($ch);
        unset($ch);

        return $info;
    }// doRunTaskUrl


    /**
     * Get exact current system date&time without modification allowed.
     *
     * @param string $format PHP date/time format.
     * @return string
     */
    protected function getCurrentDateTime($format = 'Y-m-d H:i:s')
    {
        $DateTime = new \DateTime('now', new \DateTimeZone($this->timeZone));
        return $DateTime->format($format);
    }// getCurrentDateTime


    /**
     * Check for last run is equal or over 50 minutes.
     *
     * @return boolean Return true if condition is met, false for otherwise.
     */
    protected function lastRun()
    {
        $lastRunLog = $this->debugLogFolder . DIRECTORY_SEPARATOR . 'last-run.log';

        if (!is_file($lastRunLog)) {
            file_put_contents($lastRunLog, $this->DateTimeNow->getTimestamp());
            $this->logDebug('√ Set last run log to timestamp ' . $this->DateTimeNow->getTimestamp() . ' or date/time: ' . $this->DateTimeNow->format('Y-m-d H:i:s'), 'lastRun');
            return true;
        } else {
            $timeStamp = intval(file_get_contents($lastRunLog));
            $DateTime1 = new \DateTime('@' . $timeStamp);
            $DateTime1->setTimezone(new \DateTimeZone($this->timeZone));
            $interval= $DateTime1->diff($this->DateTimeNow);
            $minuteDiff = (($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i);

            $this->logDebug('Date/time had run: ' . $DateTime1->format('Y-m-d H:i:s') . PHP_EOL . 'Current date/time: ' . $this->DateTimeNow->format('Y-m-d H:i:s') . PHP_EOL . 'Difference in minutes: ' . $minuteDiff, 'lastRun');
            unset($DateTime1, $interval, $timeStamp);


            if ($minuteDiff >= 60) {
                file_put_contents($lastRunLog, $this->DateTimeNow->getTimestamp());
                $this->logDebug('√ Set last run log to timestamp ' . $this->DateTimeNow->getTimestamp() . ' or date/time: ' . $this->DateTimeNow->format('Y-m-d H:i:s'), 'lastRun');
                return true;
            }

            $this->logDebug('¿ Minute diffrent is not enough (' . $minuteDiff . ').' . PHP_EOL . __FILE__ . ':' . __LINE__, 'lastRun');
            return false;
        }
    }// lastRun


    /**
    * Check last run for a specific task.
    *
    * @param string $name
    * @param string $time
    * @return boolean Return true if task was run and the next time is met. Return false for otherwise.
    */
    protected function lastRunTask($name, $time)
    {
        $lastRunTimeLog = $this->debugLogFolder . DIRECTORY_SEPARATOR . '[' . $name . ']-[' . $time . ']' . '-last-run.log';
        $currentHour = $this->DateTimeNow->format('H');
        $debugMsg = 'Task name: ' . $name . ', Task time: ' . $time . ', Current hour: ' . $currentHour . PHP_EOL;

        if ($time <= $currentHour) {
            // if time is 01 and current hour is 01 = OK.
            // if time is 04 and current hour is 01 = NOT OK.
            // if time is 02 and current hour is 03 = OK. this means that the cron job runs late.
            if (!is_file($lastRunTimeLog)) {
                // if was never run before.
                file_put_contents($lastRunTimeLog, $this->DateTimeNow->getTimestamp());
                $debugMsg .= 'Task had never run before.' . PHP_EOL;
                $debugMsg .= 'Set run task: ' . $this->DateTimeNow->format('Y-m-d H:i:s') . PHP_EOL . 'Result: passed.';
                $this->logDebug($debugMsg, 'lastRunTask');
                unset($debugMsg);
                return true;
            } else {
                // if it had run before.
                $debugMsg .= 'Task had run before.' . PHP_EOL;

                $timeStamp = intval(file_get_contents($lastRunTimeLog));
                $DateTime1 = new \DateTime('@' . $timeStamp);
                $DateTime1->setTimezone(new \DateTimeZone($this->timeZone));
                $DateTime1->setTime(0, 0, 0);// for ignore hours different.
                $CurrentDateTime = new \DateTime($this->DateTimeNow->format('Y-m-d'), new \DateTimeZone($this->timeZone));// for ignore hours different.
                $interval= $DateTime1->diff($CurrentDateTime);
                $dayDiff = $interval->days;

                $debugMsg .= 'Last ran task: ' . $DateTime1->format('Y-m-d H:i:s') . PHP_EOL;
                $debugMsg .= 'Current date/time: ' . $this->DateTimeNow->format('Y-m-d H:i:s') . PHP_EOL;
                $debugMsg .= 'Difference in days: ' . $dayDiff . PHP_EOL;
                unset($CurrentDateTime, $DateTime1, $interval, $timeStamp);

                if ($dayDiff >= 1) {
                    file_put_contents($lastRunTimeLog, $this->DateTimeNow->getTimestamp());
                    $debugMsg .= 'Set run task: ' . $this->DateTimeNow->format('Y-m-d H:i:s') . PHP_EOL . 'Result: passed.';
                    $this->logDebug($debugMsg, 'lastRunTask');
                    unset($debugMsg);
                    return true;
                }

                $debugMsg .= 'Day different is not enough.' . PHP_EOL . 'Result: FAILED.';
                $this->logDebug($debugMsg, 'lastRunTask');
                unset($debugMsg);
                return false;
            }
        } else {
            // time is more than current hour.
            // if time is 02 and current hour is 01 = NOT OK.
            $debugMsg .= 'The task time is more than current hour.' . PHP_EOL . 'Result: FAILED.';
            $this->logDebug($debugMsg, 'lastRunTask');
            unset($debugMsg);
            return false;
        }
    }// lastRunTask


    /**
     * Log debug message.
     * 
     * @param mixed $message
     * @param string $file
     */
    protected function logDebug($message, $file = '')
    {
        if ($this->debug !== true) {
            return ;
        }

        if (is_scalar($file) && !empty($file)) {
            $file .= '-';
        }

        if (!is_scalar($message)) {
            $message = var_export($message, true);
        }

        $message .= PHP_EOL . 'Date/time: ' . $this->getCurrentDateTime() . PHP_EOL . PHP_EOL;
        file_put_contents($this->debugLogFolder . DIRECTORY_SEPARATOR . 'debug-' . $file . $this->getCurrentDateTime('Y-m-d') . '.log', $message, FILE_APPEND);
    }// logDebug


    /**
     * Log the task result after run.
     * 
     * @param string $name
     * @param string $time
     * @param mixed $message
     */
    protected function logTask($name, $time, $message)
    {
        if ($this->debug !== true) {
            return ;
        }

        if (!is_string($name)) {
            throw new \InvalidArgumentException('The argument $name must be string.');
        }

        if (!is_string($time)) {
            throw new \InvalidArgumentException('The argument $time must be string.');
        }

        if (!is_scalar($message)) {
            $message = var_export($message, true);
        }

        $messageLog = 'Date/time: ' . $this->DateTimeNow->format('Y-m-d H:i:s') . PHP_EOL;
        $messageLog .= 'Scheduled time: ' . $time . PHP_EOL;
        $messageLog .= $message;

        file_put_contents($this->debugLogFolder . DIRECTORY_SEPARATOR . '[' . $name . ']-task-results.log', $messageLog . PHP_EOL . PHP_EOL, FILE_APPEND);

        unset($messageLog);
    }// logTask


    /**
     * Prepare to run a task.
     *
     * @param string $name The task name.
     * @param string|array $url The URL or callback function.
     * @param array $times Time to run the task.
     */
    protected function prepareRunATask($name, $url, array $times)
    {
        if (is_array($times)) {
            foreach ($times as $time) {
                if ($this->lastRunTask($name, $time) === true) {
                    if (is_string($url) && strpos($url, '://') !== false) {
                        // if the task target is URL.
                        $result = $this->doRunTaskUrl($url);
                        $this->logTask($name, $time, $result);
                    } else {
                        // if task target is callback function.
                        call_user_func($url);
                        $this->logTask($name, $time, 'The task is callback function was already called.');
                    }
                }
            }// endforeach;
            unset($time);
        }
    }// prepareRunATask


    /**
     * Reset date/time to now.
     */
    protected function resetDateTimeNow()
    {
        $this->DateTimeNow = null;
        $this->DateTimeNow = new \DateTime('now', new \DateTimeZone($this->timeZone));
        $this->logDebug('Reset current date/time to ' . $this->DateTimeNow->format('Y-m-d H:i:s'));
    }// resetDateTimeNow


    /**
     * Run the schedule tasks.
     */
    public function run()
    {
        if ($this->lastRun() === true) {
            if (is_array($this->tasks)) {
                foreach ($this->tasks as $name => $item) {
                    if (is_array($item) && array_key_exists('url', $item) && array_key_exists('time', $item)) {
                        $this->prepareRunATask($name, $item['url'], $item['time']);
                    }
                }// endforeach;
                unset($item, $name);
            }
        }
    }// run


}