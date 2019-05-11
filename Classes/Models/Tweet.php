<?php


namespace App\Classes\Models;


use App\Classes\TwitterLogger;

class Tweet extends AbstractModel implements Insertable, Selectable
{
    function insertLine($params)
    {
        $sql = 'INSERT INTO tweet (id, oid, id_city, id_tweet_user, id_day, id_language, source, link, retweet_count, favourite_count, tweet_text) 
                VALUES (DEFAULT, :oid, :id_city, :id_tweet_user, :id_day, :id_language, :source, :link, :retweet_count, :favourite_count, :tweet_text)';
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($params);

        if ($result) {
            return $this->pdo->lastInsertId();
        }
        var_dump($stmt->errorInfo());

        return false;
    }

    function selectLine($params)
    {
        $sql = 'SELECT id FROM tweet 
                WHERE oid = \'' . $params['oid'] . '\';';
        $result = $this->pdo->query($sql);
        $res = $result->fetchAll();
        if (!empty($res)) {
            TwitterLogger::log()->info('Found tweet ' . $params['oid'] . ' with id ' . $res[0]['id']);
            return $res[0]['id'];
        }

        return false;
    }
}