<?php


namespace App\Classes\Models;


use App\Classes\TwitterLogger;

class Year extends AbstractModel implements Insertable, Selectable
{

    function insertLine($params)
    {
        $sql = 'INSERT INTO year (id, year) VALUES (DEFAULT, :year)';
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($params);

        if ($result) {
            TwitterLogger::log()->info('Insert year ' . $params['year']);
            return $this->pdo->lastInsertId();
        }
        TwitterLogger::log()->error($stmt->errorInfo());

        return false;
    }

    function selectLine($params)
    {
        $sql = 'SELECT id FROM year WHERE year = \'' . pg_escape_string($params['year']) . '\';';
        $result = $this->pdo->query($sql);
        $res = $result->fetchAll();
        if (!empty($res)) {
            TwitterLogger::log()->info('Found year \'' . $params['year'] . '\' with id ' . $res[0]['id']);
            return $res[0]['id'];
        }

        return false;
    }
}