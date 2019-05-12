<?php


namespace App\Classes\Models;


use App\Classes\TwitterLogger;

class UserMention extends AbstractModel implements Insertable
{
    function insertLine($params)
    {
        $sql = 'INSERT INTO tweet_mention VALUES (:id_tweet, :id_mention)';
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($params);

        if ($result) {
            TwitterLogger::log()->info("Insert mention with id " . $params['id_mention'] . " and tweetId " . $params['id_tweet']);
            return $this->pdo->lastInsertId();
        }
        TwitterLogger::log()->error($stmt->errorInfo());

        return false;
    }
}