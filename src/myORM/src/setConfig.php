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

    public static function setConfigs($db_config = null, $logs_config = null)
    {
        if (is_array($db_config) && is_array($logs_config)){
            $config = [
                "db_config" => $db_config,
                "logs_config" => $logs_config
            ];
            file_put_contents(__DIR__."/config.json", json_encode($config));
        }
    }
}