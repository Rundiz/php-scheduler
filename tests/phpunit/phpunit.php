<?php


require __DIR__.'/Autoload.php';

$Autoload = new \Rundiz\PhpSchedule\Tests\Autoload();
$Autoload->addNamespace('Rundiz\\PhpSchedule\\Tests', __DIR__);
$Autoload->addNamespace('Rundiz\\PhpSchedule', dirname(dirname(__DIR__)).'/Rundiz/PhpSchedule');
$Autoload->register();