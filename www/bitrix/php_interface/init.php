<?php
/**
 * Created by PhpStorm.
 * User: shara
 * Date: 08.04.2016
 * Time: 9:57
 */
function test_dump($arg)
{
    global $USER;
    if ($USER->IsAdmin()) {
        echo "<pre>";
        var_dump($arg);
        echo "</pre>";
    }
}