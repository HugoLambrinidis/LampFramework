<?php

namespace src;


class queryBuilder extends connection
{

    /**
     * TODO => check how to get lowercase characters
     */

    private $type;
    private $query;
    private $join;
    private $where;
    private $groupBy;
    private $orderby;
    private $limit;
    private $cols;
    private $values;

    public function select($table, $cols = null)
    {
        if (is_array($cols)) {
            $this->query = "select ";
            for ($i = 0; $i < sizeof($cols); $i++) {
                if ( $i < sizeof($cols) - 1 ) {
                    $this->query .= "$cols[$i], ";
                } else {
                    $this->query .= "$cols[$i] ";
                }
            }
            $this->query .= "from ".$table;
        } else if (!is_array($cols) && $cols != null) {
          $this->query = "select '$cols' from $table";
        } else {
            $this->query = "select * from $table";
        }
        $this->type = "select";
        return $this;
    }

    public function insert($table)
    {
        $this->type = "insert";
        $this->query = " INSERT INTO $table";
        return $this;
    }

    public function delete($table)
    {
        $this->type = "delete";
        $this->query = " DELETE FROM $table";
        return $this;

    }

    public function update($table)
    {
        $this->type = "update";
        $this->query = " UPDATE $table SET ";
        return $this;

    }

    public function limit($limit) {
        $this->limit = "limit  $limit";
        return $this;

    }

    public function join($type, $table, $on)
    {
        switch($type) {
            case "inner":
                $this->join = " inner join ".$table." on ".$on;
                break;
            case "left":
                $this->join = "left join ".$table." on ".$on;
                break;
            case "right":
                $this->join = "right join ".$table." on ".$on;
                break;
            default:
                connection::$setLogs->addLog(["error" => "wrong argument on join method"]);
                throw new \PDOException;
        }
        return $this;

    }

    public function where($condition, $type = null)
    {
        $this->where = " where ";
        if ( is_array($condition)){
            switch($type){
                case "and":
                    foreach($condition as $cond) {
                        $this->where .= $cond." and ";
                    }
                    break;
                case "or":
                    foreach($condition as $cond) {
                        $this->where.= $cond." or ";
                    }
                    break;
                default:
                    connection::$setLogs->addLog(["error" => "wrong argument on where condition"]);
            }
        } else {
            $this->where .= $condition;
        }
        return $this;

    }

    public function groupBy($condition, $type)
    {
        switch ($type) {
            case "asc" :
                $this->groupBy = "group by $condition asc";
                break;
            case "desc" :
                $this->groupBy = "group by $condition desc";
                break;
            default:
                connection::$setLogs->addLog(["error" => "missing type on group by condition"]);
        }
        return $this;

    }

    public function orderBy($condition, $type)
    {
        switch ($type) {
            case "asc" :
                $this->orderBy = "order by $condition asc";
                break;
            case "desc" :
                $this->orderBy = "order by $condition desc";
                break;
            default:
                connection::$setLogs->addLog(["error" => "missing type on group by condition"]);
        }
        return $this;

    }

    public function cols($cols)
    {
        $this->cols = $cols;
        var_dump($this->cols);
        return $this;

    }

    public function bindValues($values)
    {
        $this->values = $values;
        var_dump($this->values);
        return $this;

    }

    public function sendQuery()
    {
        switch($this->type) {
            case "select":
                 if (!empty($this->join) ) $this->query.= " ".$this->join;
                 if (!empty($this->where) ) $this->query.= " ".$this->where;
                 if (!empty($this->orderby) ) $this->query.= " ".$this->orderby;
                 if (!empty($this->groupBy) ) $this->query.= " ".$this->groupBy;
                 if (!empty($this->limit) ) $this->query.= " ".$this->limit;
                break;
            case "update":
                if (empty($this->cols)) {
                    connection::$setLogs->addLog(["error" => "cols shoud have been specified"]);
                    throw new \PDOException;
                }
                if (empty($this->values)) {
                    connection::$setLogs->addLog(["error" => "cols shoud have been specified"]);
                    throw new \PDOException;
                }
                if (!is_array($this->values) && !is_array($this->cols)) {
                    $this->query .= "$this->cols = '$this->values'";
                } else if (is_array($this->values) && is_array($this->cols)
                    && sizeof($this->values) == sizeof($this->cols)) {
                        for ($i = 0; $i < sizeof($this->values); $i++) {
                            if ($i < sizeof($this->values) - 1) {
                                if ( !is_numeric($this->values[$i])) {
                                    $this->query .= $this->cols[$i]." = '".$this->values[$i]."', ";
                                } else {
                                    $this->query .= $this->cols[$i]." = ".$this->values[$i].", ";
                                }
                            } else {
                                if ( !is_numeric($this->values[$i]) ) {
                                    $this->query .= $this->cols[$i]." =  '".$this->values[$i]."'";
                                } else {
                                    $this->query .= $this->cols[$i]." =  ".$this->values[$i];
                                }
                            }
                        }
                } else if (is_array($this->values) && is_array($this->cols)
                    && sizeof($this->values) != sizeof($this->cols)) {
                    connection::$setLogs->addLog(["error" => "cols and values are not the same size"]);
                    throw new \PDOException;
                }
                if (!empty($this->where) ) $this->query.= " ".$this->where;
                if (!empty($this->orderby) ) $this->query.= " ".$this->orderby;
                if (!empty($this->groupBy) ) $this->query.= " ".$this->groupBy;
                if (!empty($this->limit) ) $this->query.= " ".$this->limit;
                break;
            case "insert":
                if (empty($this->cols)) {
                    connection::$setLogs->addLog(["error" => "cols shoud have been specified"]);
                    throw new \PDOException;
                }
                if (empty($this->values)) {
                    connection::$setLogs->addLog(["error" => "cols shoud have been specified"]);
                    throw new \PDOException;
                }
                if (!is_array($this->values) && !is_array($this->cols)) {
                    $this->query .= "($this->cols) values ($this->values)";
                } else if (is_array($this->values) && is_array($this->cols)
                    && sizeof($this->values) == sizeof($this->cols)) {
                    $this->query .= " (";
                    for ($i = 0; $i < sizeof($this->values); $i++) {
                        if ($i < sizeof($this->cols) - 1) {
                            $this->query .= $this->cols[$i].", ";
                        } else {
                            $this->query .= $this->cols[$i]." ) ";
                        }
                    }
                    $this->query .= " values (";
                    for ($i = 0; $i < sizeof($this->values); $i++) {
                        if ($i < sizeof($this->cols) - 1) {
                            if (!is_numeric($this->values[$i])) {
                                $this->query .= "'".$this->values[$i]."', ";
                            } else {
                                $this->query .= $this->values[$i].", ";
                            }
                        } else {
                            if (!is_numeric($this->values[$i])) {
                                $this->query .= "'".$this->values[$i]."' ) ";
                            } else {
                                $this->query .= $this->values[$i]." ) ";
                            }
                        }
                    }
                } else if (is_array($this->values) && is_array($this->cols)
                    && sizeof($this->values) != sizeof($this->cols)) {
                    connection::$setLogs->addLog(["error" => "cols and values are not the same size"]);
                    throw new \PDOException;
                }
                var_dump($this->query);
                break;
            case "delete":
                if ( $this->where != null ) {
                    $this->query .= $this->where;
                } else {
                    connection::$setLogs->addLog(["error" => "where should have been defined"]);
                    throw new \PDOException;
                }
                if (!empty($this->orderby) ) $this->query.= " ".$this->orderby;
                if (!empty($this->limit) ) $this->query.= " ".$this->limit;
                var_dump($this->query);
                break;
            default:
                connection::$setLogs->addLog(["error" => "something went wrong with your query"]);
                throw new \PDOException;
        }
        $co = self::$conn
            ->query($this->query);
        connection::$setLogs->addLog(["sql" => connection::$userIp." executed : ".$this->query]);
        $this->type = null;
        $this->query = null;
        $this->join = null;
        $this->where = null;
        $this->groupBy = null;
        $this->orderby = null;
        $this->limit = null;
        $this->cols = null;
        $this->values = null;
        return $co->fetchAll();
    }

}