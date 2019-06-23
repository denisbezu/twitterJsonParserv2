<?php

namespace App\Classes;

class Parser
{
    protected $tweetJsonParser;

    public function __construct()
    {
        $this->tweetJsonParser = new TweetJsonParser();
    }


    public function run($path)
    {
        $file = fopen($path, 'r');
        $counter = 0;
        while (!feof($file)) {
            TwitterLogger::log()->warn($counter);
            $this->tweetJsonParser->processInput(fgets($file));
            $counter++;
        }
    }
}