<?php


namespace App\Classes\Models;


use App\Classes\TwitterLogger;

class User extends AbstractModel implements Selectable, Insertable
{

    function insertLine($params)
    {
        $sql = 'INSERT INTO tweeter_user (id, name, screen_name, followers_count, friends_count, location) 
                VALUES (:id, :name, :screen_name, :followers_count, :friends_count, :location)';

        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($params);
        if ($result) {
            TwitterLogger::log()->info('Insert user ' . $params['name']);
            return $params['id'];
        }
        TwitterLogger::log()->error($stmt->errorInfo());

        return false;
    }

    function selectLine($params)
    {
        $sql = 'SELECT id FROM tweeter_user 
                WHERE id = \'' . $params['id'] . '\';';
        $result = $this->pdo->query($sql);
        $res = $result->fetchAll();
        if (!empty($res)) {
            TwitterLogger::log()->info('Found user \'' . $params['id'] . '\' with id ' . $res[0]['id']);
            return $res[0]['id'];
        }

        return false;
    }
}