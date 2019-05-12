<?php


namespace App\Classes;


use App\Classes\Models\City;
use App\Classes\Models\Country;
use App\Classes\Models\Day;
use App\Classes\Models\Hashtag;
use App\Classes\Models\Language;
use App\Classes\Models\Media;
use App\Classes\Models\Month;
use App\Classes\Models\Region;
use App\Classes\Models\Tweet;
use App\Classes\Models\User;
use App\Classes\Models\UserMention;
use App\Classes\Models\UserReply;
use App\Classes\Models\UserRetweet;
use App\Classes\Models\Year;
use App\Classes\TwitterLogger;

class TweetJsonParser
{

    public function processInput($line)
    {
        TwitterLogger::log()->info($line);
        $json = json_decode($line, true);
        $language = $this->processLanguage($json);
        $day = $this->processDate($json);
        $city = $this->processPlace($json);
        $user = $this->processUser($json);
        $tweet = $this->processTweet($json, $language, $day, $city, $user);
        $medias = $this->processMedia($json);
        $hashtags = $this->processHashtag($json);
        $mentions = $this->processMentions($json);
        $this->addMultiTables($tweet, $medias, $hashtags, $mentions);
        $this->processReply($json, $tweet);
        $this->processRetweet($json, $user);
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
            $selectUser = $user->selectLine(array('id' => $json['user']['id_str']));
            if ($selectUser === false) {
                $selectUser = $user->insertLine(array(
                    'id' => $json['user']['id_str'],
                    'name' => $json['user']['name'],
                    'screen_name' => $json['user']['screen_name'],
                    'followers_count' => $json['user']['followers_count'],
                    'friends_count' => $json['user']['friends_count'],
                    'location' => $json['user']['location'],
                ));
            }

            return $selectUser;
        }
    }

    private function processMedia($json)
    {
        $medias = array();
        if (isset($json['entities']) && isset($json['entities']['media'])) {
            foreach ($json['entities']['media'] as $mediaIndex => $mediaData) {
                $media = new Media();
                $selectMedia = $media->selectLine(array('url' => $mediaData['url']));
                if ($selectMedia === false) {
                    $selectMedia = $media->insertLine(array(
                        'type' => $mediaData['type'],
                        'url' => $mediaData['url']
                    ));
                }
                $medias[] = $selectMedia;
            }
        }

        return $medias;
    }

    private function processHashtag($json)
    {
        $hashtags = array();
        if (isset($json['entities']) && isset($json['entities']['hashtags'])) {
            foreach ($json['entities']['hashtags'] as $hashtagIndex => $hashtagData) {
                $hashtag = new Hashtag();
                $selectHashtag = $hashtag->selectLine(array('hashtag' => $hashtagData['text']));
                if ($selectHashtag === false) {
                    $selectHashtag = $hashtag->insertLine(array(
                        'hashtag' => $hashtagData['text'],
                        'id_subject' => null
                    ));
                }

                $hashtags[] = $selectHashtag;
            }
        }

        return $hashtags;
    }

    private function processMentions($json)
    {
        $mentions = array();
        if (isset($json['entities']) && isset($json['entities']['user_mentions'])) {
            foreach ($json['entities']['user_mentions'] as $userMentionIndex => $userMentionData) {
                $user = new User();
                $selectUser = $user->selectLine(array('id' => $userMentionData['id_str']));
                if ($selectUser === false) {
                    $selectUser = $user->insertLine(array(
                        'id' => $userMentionData['id_str'],
                        'name' => $userMentionData['name'],
                        'screen_name' => $userMentionData['screen_name'],
                        'followers_count' => null,
                        'friends_count' => null,
                        'location' => null,
                    ));
                }
                $mentions[] = $selectUser;
            }
        }

        return $mentions;
    }

    private function processTweet($json, $language, $day, $city, $user)
    {
        if (isset($json['id_str'])) {
            $tweet = new Tweet();
            $selectTweet = $tweet->selectLine(array('id' => $json['id_str']));
            if ($selectTweet === false) {
                $selectTweet = $tweet->insertLine(array(
                    'id' => $json['id_str'],
                    'tweet_text' => $json['text'],
                    'favourite_count' => $json['favorite_count'],
                    'retweet_count' => $json['retweet_count'],
                    'source' => $json['source'],
                    'link' => $json['link'][0],
                    'id_language' => $language,
                    'id_day' => $day,
                    'id_city' => $city,
                    'id_tweet_user' => $user
                ));
            }

            return $selectTweet;
        }
    }

    private function processReply($json, $tweet)
    {
        if (isset($json['in_reply_to_user_id_str']) && $json['in_reply_to_user_id_str'] != null) {
            $userReply = new UserReply();
            $userReply->insertLine(array(
                'id_tweet' => $tweet,
                'id_user_reply' => $json['in_reply_to_user_id_str']
            ));
        }
    }

    private function processRetweet($json, $user)
    {
        if (isset($json['retweeted_status'])) {
            $userRetweet = new UserRetweet();
            $userRetweet->insertLine(array(
                'id_user_retweet' => $user,
                'id_tweet' => $json['retweeted_status']['id_str']
            ));
        }
    }

    private function addMultiTables($tweet, $medias, $hashtags, $mentions)
    {
        foreach ($medias as $mediaId) {
            $media = new Media();
            $media->addTweetMedia($mediaId, $tweet);
        }

        foreach ($hashtags as $hashtagId) {
            $hashtag = new Hashtag();
            $hashtag->addTweetHashtag($hashtagId, $tweet);
        }

        foreach ($mentions as $mentionId) {
            $userMention = new UserMention();
            $userMention->insertLine(array(
                'id_tweet' => $tweet,
                'id_mention' => $mentionId
            ));
        }
    }
}