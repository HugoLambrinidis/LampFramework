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





    /*
    private $query;
    private $baseQuery;
    private $where = [];
    private $join = [];
    private $orderBy = [];
    private $fields = [];
    private $type;


    public function select($tablename, $column = '*')
    {
        if (is_array($column))
            $column = implode(',', $column);
        $this->type = 'select';
        $this->baseQuery = 'SELECT ' . $column . ' FROM ' . $tablename;
        return $this;
    }


    public function insert($tablename)
    {
        $this->type = 'insert';
        $this->baseQuery = 'INSERT INTO ' . $tablename;
        return $this;
    }


    public function addField($name, $value)
    {
        if (empty($name) || empty($value))
            throw new QueryManagerException('Field name or value can\'t be empty');
        $this->fields[$name] = $value;
        return $this;
    }


    public function delete($tablename)
    {
        $this->type = 'delete';
        $this->baseQuery = 'DELETE FROM ' . $tablename;
        return $this;
    }


    public function update($tablename)
    {
        $this->type = 'update';
        $this->baseQuery = 'UPDATE ' . $tablename . ' SET ';
        return $this;
    }

    public function count($tablename)
    {
        $query = 'SELECT COUNT(id) FROM '.$tablename;
        $result = null;
        try {
            $result = connection::$conn->prepare($query);
            $result->execute();
            $result = $result->fetch(\PDO::FETCH_COLUMN);
        } catch (\Exception $e) {
            connection::$setLogs->addLog(["error" => $e]);
        }
        connection::$setLogs->addLog(["sql" => $query]);
        return $result;
    }


    public function exist($tablename, $field, $value)
    {
        $query = 'SELECT ' . $field . ' FROM '.$tablename. ' WHERE ' . $field . ' = \'' . $value . '\'';
        $result = null;
        try {
            $result = connection::$conn->prepare($query);
            $result->execute();
            $result = ($result->fetch(\PDO::FETCH_COLUMN)) ? true : false;
        } catch (\Exception $e) {
            connection::$setLogs->addLog(["error" => $query, $e]);
        }
        connection::$setLogs->addLog(["sql" => $query]);
        return $result;
    }

    public function join($join)
    {
        $this->join = $join;
        return $this;
    }
    public function orderBy($orderBy)
    {
        $this->orderBy = $orderBy;
        return $this;
    }
    public function where($where)
    {
        $this->where = $where;
        return $this;
    }

    private function build()
    {
        switch ($this->type) {
            case 'select':
                $where = (!empty($this->where)) ? ' WHERE ' . $this->where : '';
                $join = (!empty($this->join)) ? ' ' . $this->join : '';
                $orderBy = (!empty($this->orderBy)) ? ' ORDER BY ' . $this->orderBy : '';
                $this->where = $this->join = $this->orderBy = [];
                $this->query = $this->baseQuery . $join . $where . $orderBy;
                break;
            case 'insert':
                $this->query = $this->baseQuery . $this->buildInsertFieldsAndValues();
                break;
            case 'delete':
                if (empty($this->where))
                    throw new \Exception('Where clause can\'t be empty');
                $where = ' WHERE ' . $this->where;
                $this->query = $this->baseQuery . $where;
                break;
            case 'update':
                if (empty($this->where))
                    throw new \Exception('Where clause can\'t be empty');
                $where = ' WHERE ' . $this->where;
                $this->query = $this->baseQuery . $this->buildUpdateFieldsAndValues() . $where;
                break;
            default:
                throw new \Exception('Unknown query type');
                break;
        }
        return $this;
    }
    public function execute()
    {
        $this->build();
        $result = null;
        try {
            switch ($this->type) {
                case 'select':
                    $result = connection::$conn->prepare($this->query);
                    $result->execute();
                    $result =  $result->fetchAll(\PDO::FETCH_ASSOC);
                    break;
                case 'insert':
                case 'delete':
                case 'update':
                    $result = connection::$conn->prepare($this->query)->execute();
                    break;
                default:
                    throw new \Exception('Unknown query type');
                    break;
            }
        } catch (\Exception $e) {
            connection::$setLogs->addLog(["error" => $this->query, $e]);
        }
        connection::$setLogs->addLog(["sql" => $this->query]);
        return $result;
    }
    public function buildInsertFieldsAndValues()
    {
        $nameFields = [];
        $valueFields = [];
        foreach ($this->fields as $i => $v) {
            $nameFields[] = $i;
            $valueFields[] = '\''.$v.'\'';
        }
        $result = '(' . implode(', ',$nameFields) . ') VALUES (' . implode(', ',$valueFields) . ')';
        $this->fields = [];
        return $result;
    }
    public function buildUpdateFieldsAndValues()
    {
        $update = [];
        foreach ($this->fields as $i => $v) {
            $update[] = $i.' = \'' . $v . '\'';
        }
        $result = implode(', ', $update);
        return $result;
    }

    public function persist($object)
    {
        $className = (new \ReflectionClass($object))->getShortName();
        $columns = $this->getTableColumns($className);
        $callable = [];
        $uniqueField = $object->getIsUnique();
        $uniqueMethod = 'get'.ucfirst($uniqueField);
        $exist = $this->exist($className, $uniqueField, $object->$uniqueMethod());
        foreach ($columns as $field) {
            $method = 'get' . ucfirst($field);
            if (method_exists($object, $method))
                $callable[$field] = $method;
        }
        if (!$exist) {
            $this->insert($className);
            foreach ($columns as $field) {
                if (in_array($field, ['id', 'Id', 'ID', 'iD']))
                    continue;
                if (empty($object->$callable[$field]()))
                    throw new QueryManagerException('Vous devez renseigner tout les champs de la table');
                else
                    $this->addField($field, $object->$callable[$field]());
            }
            $result = $this->execute();
        } elseif($exist) {
            $this->update($className);
            foreach ($columns as $field) {
                if (in_array($field, ['id', 'Id', 'ID', 'iD']))
                    continue;
                if (!empty($object->$callable[$field]()))
                    $this->addField($field, $object->$callable[$field]());
            }
            $where = $uniqueField.' = \''.$object->$callable[$uniqueField]().'\'';
            $this->where('id = '.$this->getItemById($className, $where));
            $result = $this->execute();
        }
        return $result;
    }
    public function getItemById($table, $where)
    {
        $query = 'SELECT id FROM '.$table.' WHERE ' . $where;
        $result = null;
        try {
            $result = Orm::getConnexion()->prepare($query);
            $result->execute();
            $result = $result->fetch(\PDO::FETCH_ASSOC)['id'];
        } catch (\Exception $e) {
            Orm::logError($query, $e);
        }
        Orm::logSql($query);
        return $result;
    }
    */
}