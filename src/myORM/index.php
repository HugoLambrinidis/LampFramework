<?php

require_once "vendor/autoload.php";

use src\setConfigs;
use src\connection;
use src\table;
use src\queryBuilder;

connection::getConnection();

$toto = connection::$conn;
setConfigs::setConfigs(["host" => "localhost",
      "user" => "kora",
      "passwd" => "trofie2502",
      "driver" => "mysql",
      "db" => "ass-mat"], [
      "sql" => "/logs/access.log",
      "error" => "/logs/error.log"
]);
$tables = new table();
var_dump($tables->getTables());
$query = new queryBuilder();
$query
    ->update($tables->getTable("user")['table_name'])
    ->cols("user_name")
    ->bindValues("polo")
    ->where("user_id = 1")
    ->sendQuery();

$query
    ->insert($tables->getTable("user")['table_name'])
    ->cols(["user_name", "user_surname", "user_password"])
    ->bindValues(["pol", "polo", "toto"])
    ->sendQuery();

$query
    ->delete($tables->getTable("user")['table_name'])
    ->where("user_name = 'pol'")
    ->sendQuery();

var_dump($query->select($tables->getTable("user")['table_name'], ['user_name', 'user_id'])->sendQuery());
