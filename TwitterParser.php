<?php

require_once 'config.php';

$myPDO = new PDO('pgsql:host='. HOST. ';dbname=' . DB_NAME, USER, PASSWORD);
var_dump($myPDO);
$result = $myPDO->query("SELECT year FROM year");
var_dump($result->fetchAll());
die;

echo $result;