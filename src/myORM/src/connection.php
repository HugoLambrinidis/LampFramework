<?php

namespace src;

use logs\logs;

class connection extends setConfigs
{
    public static $conn = null;
    public static $setLogs = null;
    public static $userIp = null;
    public static $db_config = null;
    public static $logs_config = null;
    protected static $model_path;


    private static function connect($config)
    {
        self::$userIp = self::get_client_ip();
        self::$setLogs = new logs();
        try {
            self::$setLogs->addLog(["sql" => self::$userIp." is connected to ".$config["db"]."@".$config["host"]]);
            self::$conn = new \PDO($config['driver'] . ":dbname=" . $config["db"] . ";host="
                . $config["host"], $config["user"], $config["passwd"]);
        } catch (\PDOException $e) {
            try {
                self::$conn = new \PDO($config["driver"] . ":host=" . $config["host"], $config["user"],
                    $config["passwd"]);

                if (self::$conn) {
                    try {
                        self::$conn->exec("CREATE DATABASE `" . $config["db"] . "`;
                        CREATE USER '" . $config["user"] . "'@'" . $config["host"] . "' IDENTIFIED BY '" . $config['passwd'] . "';
                        GRANT ALL ON `" . $config["db"] . "`.* TO '" . $config['user'] . "'@'localhost';
                        FLUSH PRIVILEGES;");
                        self::$conn = new \PDO($config["driver"] . ":dbname=" . $config["db"] . ";host=" .
                            $config["host"], $config["user"], $config["passwd"]);
                        self::$setLogs->addLog(["sql" => self::$userIp." created ".$config["db"]."@".$config["host"]
                            ." and get connected to it"]);
                    } catch (\PDOException $d) {
                        self::$setLogs->addLog(["error" =>
                            self::$userIp." tried to create a db and connect, and got error : => ".$d->getMessage()]);
                        throw new \Exception($d->getMessage());
                    }
                } else {
                    self::$setLogs->addLog(["error" => self::$userIp." couldn't connect to ".$config["host"]]);
                    throw new \Exception($e->getMessage());
                }
            } catch (\PDOException $f) {
                self::$setLogs->addLog(["error" => self::$userIp." couldn't connect to ".$config["host"]]);
                throw new \Exception($f->getMessage());
            }
        }
    }

    public static function getConnection()
    {
        self::parseConfig();
        self::connect(self::$db_config);
        return self::$conn;
    }

    private static function parseConfig()
    {
        $configs = json_decode(file_get_contents(__DIR__."/config.json"), true);
        self::$db_config = $configs['db_config'];
        self::$logs_config = $configs['logs_config'];
        self::$model_path = $configs['model_path'];
        logs::setLogsPath(self::$logs_config);
    }

    protected  function getModelPath()
    {
        return self::$model_path;
    }

}