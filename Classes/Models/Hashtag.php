<?php


namespace App\Classes\Models;


use App\Classes\TwitterLogger;

class Hashtag extends AbstractModel implements Insertable, Selectable
{
    function insertLine($params)
    {
        $sql = 'INSERT INTO hashtag (id, hashtag, id_topic) VALUES (DEFAULT, :hashtag, :id_topic)';
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($params);

        if ($result) {
            TwitterLogger::log()->info('Insert hashtag ' . $params['hashtag']);
            return $this->pdo->lastInsertId();
        }
        TwitterLogger::log()->error($stmt->errorInfo());

        return false;
    }

    function selectLine($params)
    {
        $sql = 'SELECT id FROM hashtag 
                WHERE hashtag = \'' . pg_escape_string($params['hashtag']) . '\';';
        $result = $this->pdo->query($sql);
        $res = $result->fetchAll();
        if (!empty($res)) {
            TwitterLogger::log()->info('Found hashtag ' . $params['hashtag'] . ' with id ' . $res[0]['id']);
            return $res[0]['id'];
        }

        return false;
    }

    public function addTweetHashtag($hashtagId, $tweetId)
    {
        $sql = "INSERT INTO tweet_hashtag VALUES ($tweetId, $hashtagId)";
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute();

        if ($result) {
            TwitterLogger::log()->info("Insert hashtag with id $hashtagId and tweetId $tweetId");
            return $this->pdo->lastInsertId();
        }
        var_dump($stmt->errorInfo());

        return false;
    }
}