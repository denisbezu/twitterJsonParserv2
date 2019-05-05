<?php


namespace App\Classes\Models;


use App\Classes\TwitterLogger;

class Language extends AbstractModel implements Insertable, Selectable
{

    function insertLine($params)
    {
        $sql = 'INSERT INTO language (id, lang) VALUES (DEFAULT, :lang)';
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($params);

        if ($result) {
            return $this->pdo->lastInsertId();
        }

        return false;
    }

    function selectLine($params)
    {
        $sql = 'SELECT id FROM language WHERE lang = \'' . $params['lang'] . '\';';
        $result = $this->pdo->query($sql);
        $res = $result->fetchAll();
        if (!empty($res)) {
            TwitterLogger::log()->info('Found lang \'' . $params['lang'] . '\' with id ' . $res[0]['id']);
            return $res[0]['id'];
        }

        return false;
    }
}