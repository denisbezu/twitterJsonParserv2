<?php


namespace App\Classes\Models;


use App\Classes\TwitterLogger;

class User extends AbstractModel implements Selectable, Insertable
{

    function insertLine($params)
    {
        $sql = 'INSERT INTO tweeter_user (id, name, screen_name, followers_count, friends_count, location, uid) 
                VALUES (DEFAULT, :name, :screen_name, :followers_count, :friends_count, :location, :uid)';

        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($params);
//        var_dump($stmt->queryString);
//        die;
        if ($result) {
            TwitterLogger::log()->info('Insert user \'' . $params['name']);
            return $this->pdo->lastInsertId();
        }

        return false;
    }

    function selectLine($params)
    {
        $sql = 'SELECT id FROM tweeter_user 
                WHERE uid = \'' . $params['uid'] . '\';';
        $result = $this->pdo->query($sql);
        $res = $result->fetchAll();
        if (!empty($res)) {
            TwitterLogger::log()->info('Found user \'' . $params['uid'] . '\' with id ' . $res[0]['id']);
            return $res[0]['id'];
        }

        return false;
    }
}