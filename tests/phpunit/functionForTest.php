<?php


function testCallbackFunction()
{
    file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'functionForTests.log', 'Called from ' . __FILE__ . ':' . __LINE__);
}