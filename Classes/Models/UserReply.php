<?php


namespace App\Classes\Models;


use App\Classes\TwitterLogger;

class UserReply extends AbstractModel implements Insertable
{
    function insertLine($params)
    {
        $sql = 'INSERT INTO tweet_reply VALUES (:id_tweet, :id_user_reply)';
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($params);

        if ($result) {
            TwitterLogger::log()->info("Insert reply with id " . $params['id_user_reply'] . " and tweetId " . $params['id_tweet']);
            return $this->pdo->lastInsertId();
        }
        TwitterLogger::log()->error($stmt->errorInfo());

        return false;
    }
}