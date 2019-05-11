<?php


namespace App\Classes\Models;


use App\Classes\TwitterLogger;

class Hashtag extends AbstractModel implements Insertable, Selectable
{
    function insertLine($params)
    {
        $sql = 'INSERT INTO hashtag (id, hashtag, id_subject) VALUES (DEFAULT, :hashtag, :id_subject)';
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
        $sql = 'SELECT id FROM hashtag 
                WHERE hashtag = \'' . $params['hashtag'] . '\';';
        $result = $this->pdo->query($sql);
        $res = $result->fetchAll();
        if (!empty($res)) {
            TwitterLogger::log()->info('Found hashtag ' . $params['hashtag'] . ' with id ' . $res[0]['id']);
            return $res[0]['id'];
        }

        return false;
    }
}