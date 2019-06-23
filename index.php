<?php

use App\Classes\Parser;
use App\Classes\TwitterLogger;

require_once './vendor/autoload.php';
require_once './Config/config.php';

$parser = new Parser();
$parser->run(__DIR__ . '/Data/xae');
$parser->run(__DIR__ . '/Data/xaf');
$parser->run(__DIR__ . '/Data/xag');
$parser->run(__DIR__ . '/Data/xah');
$parser->run(__DIR__ . '/Data/xai');
$parser->run(__DIR__ . '/Data/xaj');