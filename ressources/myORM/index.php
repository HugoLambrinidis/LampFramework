<?php

use src\connection;
use Generator\TableGenerator;

connection::getConnection();

switch ($argv[1]) {
    case "config" :
        switch ($argv[2]) {
            case "db" :
                $db_config = [
                    "host" => $argv[3],
                    "user" => $argv[4],
                    "passwd" => $argv[5],
                    "driver" => $argv[6],
                    "db" => $argv[7]
                ];
                connection::setConfigs($db_config);
                break;
            case "logs" :
                $logs_config = [
                    "sql" => $argv[3],
                    "error" => $argv[4]
                ];
                connection::setConfigs(null, $logs_config);
                break;
            case "model_path" :
                $model_path = $argv[3];
                connection::setConfigs(null, null, $model_path);
                break;
            default :
                echo "you should choose a parameter to edit !";
                break;
        }
        break;
    case "generate" :
        $cols = [];
        $type = [];
        for ($i = 3; $i < sizeof($argv); $i++) {
            if ($i % 2 != 0) {
                $cols[] = $argv[$i];
            } else {
                $type[] = $argv[$i];
            }
        }
        new TableGenerator($argv[2], $cols, $type);
        break;
    default:
        echo "you meant generate or db, don't you ?";
        break;
}
