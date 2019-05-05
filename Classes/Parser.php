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
        while (!feof($file)) {
            $this->tweetJsonParser->processInput(fgets($file));
        }
    }
}