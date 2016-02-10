<?php

namespace src;


class table extends connection
{
    /**
     * TODO => check how to step back on db's repository
     */

    private $db = null;

    public function __construct()
    {
        $this->db = self::$db_config["db"];
    }


    public function getTables()
    {
        $query = "SHOW TABLES";
        try {
            $co = self::$conn
                ->query($query)
                ->fetchAll();
            connection::$setLogs->addLog(["sql" => connection::$userIp." executed : ".$query]);
            return $co;
        } catch (\PDOException $e) {
            connection::$setLogs->addLog(["error" => connection::$userIp." failed to execute : ".$query
                ." and got : ".$e->getMessage()]);
            throw new \PDOException($e->getMessage());
        }
    }

    public function getTable($table)
    {
        $query = "SELECT table_name FROM information_schema.tables WHERE table_type = 'base table'
                  AND table_schema='$this->db' AND table_name = '$table'";
        try {
            $co = self::$conn
                ->query($query)
                ->fetch();
                connection::$setLogs->addLog(["sql" => connection::$userIp." executed : ".$query]);
            return $co;
        } catch (\PDOException $e) {
            connection::$setLogs->addLog(["error" => connection::$userIp." failed to execute : ".$query
                ." and got : ".$e->getMessage()]);
            throw new \PDOException($e->getMessage());
        }
    }


    public function addTable($table, $cols, $types)
    {
        $query = "create table $table (";
        for ($i = 0; $i < count($cols); $i++) {
            if ($i < sizeof($cols) -1) {
                $query = $query." ".$cols[$i]." ".$types[$i].",";
            } else {
                $query = $query." ".$cols[$i]." ".$types[$i];
            }
        }
        $query = $query.")";
        try {
            $request = self::$conn
            ->query($query);
            if (is_object($request)) {
                $request->execute();
                connection::$setLogs->addLog(["sql" => connection::$userIp." executed : ".$query]);
            }
            else {
                connection::$setLogs->addLog(["error" => connection::$userIp." failed to execute ".$query]);
                throw new \PDOException;
            }
                //->execute();
            return $request;
        } catch (\PDOException $e) {
            connection::$setLogs->addLog(["error" => connection::$userIp." failed to execute : ".$query
                ." and got : ".$e->getMessage()]);
            throw new \PDOException($e->getMessage());
        }
    }

    public function removeTable($table)
    {

        $query = "DROP TABLE $table";
        try {
            $request = self::$conn
                ->query($query);
            if ( $request != false) {
                $request->execute();
                connection::$setLogs->addLog(["sql" => connection::$userIp." executed : ".$query]);
            } else {
                connection::$setLogs->addLog(["error" => connection::$userIp." failed to execute : ".$query]);
                throw new \PDOException;
            }
            return $request;
        } catch (\PDOException $e) {
            connection::$setLogs->addLog(["error" => connection::$userIp." failed to execute : ".$query
                ." and got : ".$e->getMessage()]);
            throw new \PDOException($e->getMessage());
        }
    }

    public function addColumn($table, $col, $type)
    {
        $query = "alter table `$table` add `$col` $type";
        //ALTER TABLE `user` ADD `toto` VARCHAR(255)

        try {
            $request = self::$conn
                ->query($query);

            if ($request != false) {
                $request->execute();
                connection::$setLogs->addLog(["sql" => connection::$userIp." executed : ".$query]);
            }
            else {
                connection::$setLogs->addLog(["error" => connection::$userIp." failed to execute ".$query
                    ." because the query has already been executed"]);
                throw new \PDOException;
            }
            return $request;
        } catch (\PDOException $e) {
            connection::$setLogs->addLog(["error" => connection::$userIp." failed to execute : ".$query
                ." and got : ".$e->getMessage()]);
            throw new \PDOException($e->getMessage());
        }

    }

    public function removeColumn($table, $col)
    {
        $query = "alter table ".$table." drop column ".$col;

        try {
            $request = self::$conn
                ->query($query);
            if ( $request != false ) {
                $request->execute();
                connection::$setLogs->addLog(["sql" => connection::$userIp." executed : ".$query]);
            } else {
                connection::$setLogs->addLog(["error" => connection::$userIp." failed to execute : ".$query]);
                throw new \PDOException;
            }
                //->execute();
            return $request;

        } catch (\PDOException $e) {
            connection::$setLogs->addLog(["error" => connection::$userIp." failed to execute : ".$query
                ." and got : ".$e->getMessage()]);
            throw new \PDOException($e->getMessage());
        }
    }

    public function modifyColumn($table, $col, $type)
    {
        $query = "alter table".$table;
    }

}