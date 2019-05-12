<?php


namespace App\Classes\Models;


use App\Classes\TwitterLogger;

class Tweet extends AbstractModel implements Insertable, Selectable
{
    function insertLine($params)
    {
        $sql = 'INSERT INTO tweet (id, id_city, id_tweet_user, id_day, id_language, source, link, retweet_count, favourite_count, tweet_text) 
                VALUES (:id, :id_city, :id_tweet_user, :id_day, :id_language, :source, :link, :retweet_count, :favourite_count, :tweet_text)';
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($params);

        if ($result) {
            TwitterLogger::log()->info('Insert tweet ' . $params['id']);
            return $params['id'];
        }
        TwitterLogger::log()->error($stmt->errorInfo());

        return false;
    }

    function selectLine($params)
    {
        $sql = 'SELECT id FROM tweet 
                WHERE id = \'' . $params['id'] . '\';';
        $result = $this->pdo->query($sql);
        $res = $result->fetchAll();
        if (!empty($res)) {
            TwitterLogger::log()->info('Found tweet ' . $params['id'] . ' with id ' . $res[0]['id']);
            return $res[0]['id'];
        }

        return false;
    }
}