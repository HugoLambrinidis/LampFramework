<?php

namespace logs;

use src\connection;

class logs extends connection
{

    public static $errorLogPath = null;
    public static $sqlLogPath = null;


    /*
     * take an array in parameter =>
     * $log = [
     *      "type" => "message"
     * ];
     *
     * ex: $log = [
     *      "sql" => "wrong user"
     * ];
     */



    public function addLog($log)
    {
        if (self::$sqlLogPath != null && self::$errorLogPath != null) {

            foreach($log as $type => $message) {

                switch ($type) {
                    case "error" :
                        if ( file_exists(self::$errorLogPath)) {
                            $content = file_get_contents(self::$errorLogPath);
                            $content = $content."\n".date("d-m-Y H:i:s")." : ".$message;
                            file_put_contents(self::$errorLogPath, $content);
                        } else {
                            file_put_contents(self::$errorLogPath, date("d-m-Y H:i:s")." : ".$message);
                        }
                        break;

                    case "sql" :
                        if ( file_exists(self::$sqlLogPath)) {
                            $content = file_get_contents(self::$sqlLogPath);
                            $content = $content."\n".date("d-m-Y H:i:s")." : ".$message;
                            file_put_contents(self::$sqlLogPath, $content);
                        } else {
                            file_put_contents(self::$sqlLogPath, date("d-m-Y H:i:s")." : ".$message);
                        }
                        break;
                }
            }
        } else {
            throw new \Exception("define log's files path");
        }
    }




    /*
     * take an array of params =>
     * $param = [
     *      $type => $path
     * ]
     *
     * ex : $param = [
     *      "sql" => __DIR__."/logs/sqlLogs.log"
     * ]
     */

    public static function setLogsPath($param)
    {
        foreach ( $param as $type => $path ) {
            switch($type) {
                case "error":
                    self::$errorLogPath = __DIR__."/..".$path;
                    break;
                case "sql":
                    self::$sqlLogPath = __DIR__."/..".$path;
                    break;
            }
        }
    }
}