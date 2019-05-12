<?php


namespace App\Classes\Models;


use App\Classes\TwitterLogger;

class Region extends AbstractModel implements Insertable, Selectable
{

    function insertLine($params)
    {
        $sql = 'INSERT INTO region (id, region, id_country) VALUES (DEFAULT, :region, :id_country)';
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($params);

        if ($result) {
            TwitterLogger::log()->info('Insert region ' . $params['region']);
            return $this->pdo->lastInsertId();
        }
        TwitterLogger::log()->error($stmt->errorInfo());

        return false;
    }

    function selectLine($params)
    {
        $sql = 'SELECT id FROM region 
                WHERE region = \'' . pg_escape_string($params['region']) . '\' 
                AND id_country = \'' . $params['id_country'] . '\';';
        $result = $this->pdo->query($sql);
        $res = $result->fetchAll();
        if (!empty($res)) {
            TwitterLogger::log()->info('Found region \'' . $params['region'] . '\' with id ' . $res[0]['id']);
            return $res[0]['id'];
        }

        return false;
    }
}