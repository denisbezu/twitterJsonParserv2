<?php


namespace App\Classes\Models;


use App\Classes\TwitterLogger;

class UserRetweet extends AbstractModel implements Insertable
{
    function insertLine($params)
    {
        $sql = 'INSERT INTO tweet_retweet VALUES (:id_tweet, :id_user_retweet)';
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($params);

        if ($result) {
            TwitterLogger::log()->info("Insert retweet with id " . $params['id_user_retweet'] . " and tweetId " . $params['id_tweet']);
            return $this->pdo->lastInsertId();
        }
        TwitterLogger::log()->error($stmt->errorInfo());

        return false;
    }
}