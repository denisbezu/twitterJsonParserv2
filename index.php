<?php

use App\Classes\Parser;
use App\Classes\TwitterLogger;

require_once './vendor/autoload.php';
require_once './Config/config.php';

$parser = new Parser();
$parser->run(__DIR__ . '/Data/ImageDataset.TwitterFDL2015.json');