<?php


namespace App\Classes\Models;


use PDO;

class AbstractModel
{
    protected $pdo;

    public function __construct()
    {
        $this->pdo = new PDO('pgsql:host='. HOST. ';dbname=' . DB_NAME, USER, PASSWORD);
    }
}