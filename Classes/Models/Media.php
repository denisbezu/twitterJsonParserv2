<?php


namespace App\Classes\Models;


use App\Classes\TwitterLogger;

class Media extends AbstractModel implements Insertable, Selectable
{
    function insertLine($params)
    {
        $sql = 'INSERT INTO media (id, type, url) VALUES (DEFAULT, :type, :url)';
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($params);

        if ($result) {
            TwitterLogger::log()->info('Insert media ' . $params['url']);
            return $this->pdo->lastInsertId();
        }
        TwitterLogger::log()->error($stmt->errorInfo());

        return false;
    }

    function selectLine($params)
    {
        $sql = 'SELECT id FROM media 
                WHERE url = \'' . pg_escape_string($params['url']) . '\';';
        $result = $this->pdo->query($sql);
        $res = $result->fetchAll();
        if (!empty($res)) {
            TwitterLogger::log()->info('Found media with id ' . $res[0]['id']);
            return $res[0]['id'];
        }

        return false;
    }

    public function addTweetMedia($mediaId, $tweetId)
    {
        $sql = "INSERT INTO tweet_media VALUES ($tweetId, $mediaId)";
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute();

        if ($result) {
            TwitterLogger::log()->info("Insert media with id $mediaId and tweetId $tweetId");
            return $this->pdo->lastInsertId();
        }
        var_dump($stmt->errorInfo());

        return false;
    }
}