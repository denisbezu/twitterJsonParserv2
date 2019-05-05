<?php


namespace App\Classes;


use App\Classes\Models\Language;

class TweetJsonParser
{

    public function processInput($line)
    {
        $json = json_decode($line, true);
        $language = $this->processLanguage($json);

    }

    private function processLanguage($json)
    {
        if (isset($json['lang'])) {
            $lang = $json['lang'];
            $language = new Language();
            $select = $language->selectLine(array('lang' => $lang));
            if ($select === false) {
                $result = $language->insertLine(array('lang' => $lang));
                return $result;
            }

            return $select;
        }

        return null;
    }

}