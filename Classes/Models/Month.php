<?php


namespace App\Classes\Models;


use App\Classes\TwitterLogger;

class Month extends AbstractModel implements Insertable, Selectable
{

    function insertLine($params)
    {
        $sql = 'INSERT INTO month (id, month, id_year) VALUES (DEFAULT, :month, :id_year)';
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($params);

        if ($result) {
            return $this->pdo->lastInsertId();
        }

        return false;
    }

    function selectLine($params)
    {
        $sql = 'SELECT id FROM month 
                WHERE month = \'' . $params['month'] . '\' 
                AND id_year = \'' . $params['id_year'] . '\';';
        $result = $this->pdo->query($sql);
        $res = $result->fetchAll();
        if (!empty($res)) {
            TwitterLogger::log()->info('Found month \'' . $params['month'] . '\' with id ' . $res[0]['id']);
            return $res[0]['id'];
        }

        return false;
    }
}