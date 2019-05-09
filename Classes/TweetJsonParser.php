<?php


namespace App\Classes;


use App\Classes\Models\City;
use App\Classes\Models\Country;
use App\Classes\Models\Day;
use App\Classes\Models\Language;
use App\Classes\Models\Month;
use App\Classes\Models\Region;
use App\Classes\Models\User;
use App\Classes\Models\Year;

class TweetJsonParser
{

    public function processInput($line)
    {
        $json = json_decode($line, true);
        $language = $this->processLanguage($json);
        $day = $this->processDate($json);
        $city = $this->processPlace($json);
        $user = $this->processUser($json);
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

    private function processPlace($json)
    {
        if (isset($json['place'])) {
            $country = new Country();
            $selectCountry = $country->selectLine(array('country' => $json['place']['country']));
            if ($selectCountry === false) {
                $selectCountry = $country->insertLine(array('country' => $json['place']['country']));
            }

            $region = new Region();
            if ($json['place']['place_type'] == 'country' || $json['place']['place_type'] == 'admin') {
                $regionName = 'none';
                $cityName = 'none';
            } else {
                $cityName = $json['place']['name'];
                $regionName = trim(substr($json['place']['full_name'], strlen($cityName) + 1));
            }

            $selectRegion = $region->selectLine(array(
                'region' => $regionName,
                'id_country' => $selectCountry
            ));

            if ($selectRegion === false) {
                $selectRegion = $region->insertLine(array(
                    'region' => $regionName,
                    'id_country' => $selectCountry
                ));
            }

            $city = new City();
            $selectCity = $city->selectLine(array(
                'city' => $cityName,
                'id_region' => $selectRegion
            ));

            if ($selectCity === false) {
                $selectCity = $city->insertLine(array(
                    'city' => $cityName,
                    'id_region' => $selectRegion
                ));
            }

            return $selectCity;
        }
    }

    private function processUser($json)
    {
        if (isset($json['user'])) {
            $user = new User();
            $selectUser = $user->selectLine(array('uid' => $json['user']['id_str']));
            if ($selectUser === false) {
                $selectUser = $user->insertLine(array(
                    'name' => $json['user']['name'],
                    'screen_name' => $json['user']['screen_name'],
                    'followers_count' => $json['user']['followers_count'],
                    'friends_count' => $json['user']['friends_count'],
                    'location' => $json['user']['location'],
                    'uid' => (int)$json['user']['id_str'],
                ));
            }

            return $selectUser;
        }
    }
}