<?php


namespace App\Classes;


use App\Classes\Models\Day;
use App\Classes\Models\Language;
use App\Classes\Models\Month;
use App\Classes\Models\Year;

class TweetJsonParser
{

    public function processInput($line)
    {
        $json = json_decode($line, true);
        $language = $this->processLanguage($json);
        $day = $this->processDate($json);

    }

    private function processLanguage($json)
    {
        if (isset($json['lang'])) {
            $lang = $json['lang'];
            $language = new Language();
            $select = $language->selectLine(array('lang' => $lang));
            if ($select === false) {
                $select = $language->insertLine(array('lang' => $lang));
            }

            return $select;
        }

        return null;
    }

    private function processDate($json)
    {
        if (isset($json['created_at'])) {
            $createdAt = new \DateTime($json['created_at']);
            $year = new Year();
            $selectYear = $year->selectLine(array('year' => $createdAt->format('Y')));
            if ($selectYear === false) {
                $selectYear = $year->insertLine(array('year' => $createdAt->format('Y')));
            }

            $month = new Month();
            $selectMonth = $month->selectLine(array(
                    'id_year' => $selectYear,
                    'month' => $createdAt->format('m')
                )
            );

            if ($selectMonth === false) {
                $selectMonth = $month->insertLine(array(
                        'id_year' => $selectYear,
                        'month' => $createdAt->format('m')
                    )
                );
            }

            $day = new Day();
            $selectDay = $day->selectLine(array(
                    'id_month' => $selectMonth,
                    'day' => $createdAt->format('d')
                )
            );

            if ($selectDay === false) {
                $selectDay = $day->insertLine(array(
                        'id_month' => $selectMonth,
                        'day' => $createdAt->format('d')
                    )
                );
            }

            return $selectDay;
        }
    }

}