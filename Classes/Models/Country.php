<?php


namespace App\Classes\Models;


use App\Classes\TwitterLogger;

class Country extends AbstractModel implements Selectable, Insertable
{

    function insertLine($params)
    {
        $sql = 'INSERT INTO country (id, name) VALUES (DEFAULT, :country)';
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($params);

        if ($result) {
            return $this->pdo->lastInsertId();
        }

        return false;
    }

    function selectLine($params)
    {
        $sql = 'SELECT id FROM country WHERE name = \'' . $params['country'] . '\';';
        $result = $this->pdo->query($sql);
        $res = $result->fetchAll();
        if (!empty($res)) {
            TwitterLogger::log()->info('Found country \'' . $params['country'] . '\' with id ' . $res[0]['id']);
            return $res[0]['id'];
        }

        return false;
    }
}