<?php


namespace App\Classes\Models;


use App\Classes\TwitterLogger;

class City extends AbstractModel implements Selectable, Insertable
{

    function insertLine($params)
    {
        $sql = 'INSERT INTO city (id, city, id_region) VALUES (DEFAULT, :city, :id_region)';
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($params);

        if ($result) {
            TwitterLogger::log()->info('Insert city ' . $params['city']);
            return $this->pdo->lastInsertId();
        }
        TwitterLogger::log()->error($stmt->errorInfo());

        return false;
    }

    function selectLine($params)
    {
        $sql = 'SELECT id FROM city 
                WHERE city = \'' . pg_escape_string($params['city']) . '\' 
                AND id_region = \'' . $params['id_region'] . '\';';
        $result = $this->pdo->query($sql);
        $res = $result->fetchAll();
        if (!empty($res)) {
            TwitterLogger::log()->info('Found city \'' . $params['city'] . '\' with id ' . $res[0]['id']);
            return $res[0]['id'];
        }

        return false;
    }
}