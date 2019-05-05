<?php


namespace App\Classes;


use Logger;
Logger::configure('config.xml');

class TwitterLogger
{
    public static function log()
    {
        return $log = Logger::getLogger('TwitterLogger');
    }
}