<?php


namespace App\Classes\Models;


use App\Classes\TwitterLogger;

class Day extends AbstractModel implements Insertable, Selectable
{
    function insertLine($params)
    {
        $sql = 'INSERT INTO day (id, day, id_month) 
                VALUES (DEFAULT, :day, :id_month)';
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($params);

        if ($result) {
            TwitterLogger::log()->info('Insert day ' . $params['day']);
            return $this->pdo->lastInsertId();
        }
        TwitterLogger::log()->error($stmt->errorInfo());

        return false;
    }

    function selectLine($params)
    {
        $sql = 'SELECT id FROM day 
                WHERE day = \'' . pg_escape_string($params['day']) . '\' 
                AND id_month = \'' . $params['id_month'] . '\';';
        $result = $this->pdo->query($sql);
        $res = $result->fetchAll();
        if (!empty($res)) {
            TwitterLogger::log()->info('Found day \'' . $params['day'] . '\' with id ' . $res[0]['id']);
            return $res[0]['id'];
        }

        return false;
    }
}