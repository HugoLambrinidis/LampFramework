<?php

namespace src;

class setConfigs
{
    private static $userIp;

    public static function get_client_ip()
    {
        if (getenv('HTTP_CLIENT_IP'))
            self::$userIp = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            self::$userIp = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            self::$userIp = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            self::$userIp = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            self::$userIp = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            self::$userIp = getenv('REMOTE_ADDR');
        else
            self::$userIp = 'UNKNOWN';

        return self::$userIp;
    }

    public static function setConfigs($db_config = null, $logs_config = null, $model_path = null)
    {

        $conf = json_decode(file_get_contents(__DIR__."/config.json"));

        if ($db_config) $conf->db_config = $db_config;
        if ($logs_config) $conf->logs_config = $logs_config;
        if ($model_path) $conf->model_path = $model_path;
        if (!is_dir($model_path) && $model_path != null) mkdir($model_path);
        file_put_contents(__DIR__."/config.json", json_encode($conf));
        /*
        if (is_array($db_config) && is_array($logs_config) && $model_path != null){

            $config = [
                "db_config" => $db_config,
                "logs_config" => $logs_config,
                "model_path" => $model_path
            ];
            file_put_contents(__DIR__."/config.json", json_encode($config));

            if ( !is_dir($model_path) ) {
                mkdir($model_path);
            }

        } else {
            connection::$setLogs->addLog(["error" => "un paramètre n'a pas été spécifié."]);
        }*/
    }
}