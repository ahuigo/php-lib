<?php
/**
 * @refer http://codereview.stackexchange.com/questions/29362/class-for-reducing-development-time
 */
class Db{

    public function __construct($dsn, $user, $password, $options = array()) {
        //$dsn = "mysql:host=".$config['host'].';port='.$config['port'].';dbname='.$config['dbname'];
        $options = $options + array(
                \PDO::ATTR_PERSISTENT => false,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES => true,
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8" ,
            );

        $this->pdo = new \PDO($dsn, $user, $password, $options);
        $this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
    }
    /**
     * @return static
     */
    static function in($dsn, $user, $password, $options = array()){
        return new static($dsn, $user, $password, $options);
    }

    function getCondition($condition){
        $bind_keys = array_map(function($k){
            return "`$k`=?";
        },array_keys($condition));
        $where = implode(' and ', $bind_keys);
        return array(
            $where,
            array_values($condition),
        );
    }
    function checkSqlInjection($condition){
        foreach(array_keys($condition) as $key){
            if(!preg_match('#^\w+$#', $key)){
                die('sql injection!');
            }
        }
    }

    /**
     *
     * @param $table string
     * @param $duplicate bool
     * @param $info array
     */
    public function insert($table, $info, $duplicate = false) {
        if(isset($this->shardIndexKey)){
            $table = $this->getTable($info[$this->shardIndexKey]);
        }
        if(empty($info)){
            throw new \InvalidArgumentException('Empty insert data!', -1);
        }
        $fields = array_keys($info);
        $sql = "INSERT INTO " . $table . " (" . implode($fields, ", ") . ") VALUES (:" . implode($fields, ", :") . ")";
        $bind = array();
        foreach ($fields as $field) {
            $bind[":$field"] = $info[$field];
        }
        if($duplicate){
            $sql .= " ON DUPLICATE KEY UPDATE ";
            foreach($info as $k=>$v){
                $sql .= "{$k} = VALUES($k),";
            }
            $sql .= 'id=last_insert_id(id)';
        }
        return $this->query($sql, $bind, 'insert');
    }

    public function insertBatch($table, $infos, $duplicate = false, $id='id') {
        if (!is_array($infos[0])) {
            throw new \InvalidArgumentException('invalid infos');
        }
        $info = $infos[0];

        $fields = array_keys($infos[0]);
        $sql = "INSERT INTO " . $table . " (" . implode($fields, ", ") . ") VALUES ";
        $val_format = '(' .str_repeat('?,', count($info)-1). '?)';
        $vals_format = str_repeat($val_format . ',', count($infos) -1). $val_format;
        $sql .= $vals_format;

        if($duplicate){
            $sql .= " ON DUPLICATE KEY UPDATE $id=$id";
        }

        $bind = array();
        foreach ($infos as $info) {
            $bind = array_merge($bind, array_values($info));
        }
        $rtn = $this->query($sql, $bind, 'insert');
        return $rtn;
    }

    public function update($table, $info, $where) {
        if (!is_string($table)) {
            throw new \InvalidArgumentException(self::INVALIDATE_ARGUMENTS . ':table');
        }

        if (!is_string($where)) {
            throw new \InvalidArgumentException(self::INVALIDATE_ARGUMENTS . ':where');
        }

        if (!is_array($info)) {
            throw new \InvalidArgumentException(self::INVALIDATE_ARGUMENTS . ':info');
        }

        if (!is_array($bind)) {
            throw new \InvalidArgumentException(self::INVALIDATE_ARGUMENTS . ':bind');
        }

        $fields = array_keys($info);
        $fieldSize = sizeof($fields);

        $sql = "UPDATE " . $table . " SET ";
        for ($f = 0; $f < $fieldSize; ++$f) {
            if ($f > 0) {
                $sql .= ", ";
            }
            $sql .= $fields[$f] . " = :update_" . $fields[$f];
        }
        $sql .= " WHERE " . $where . ';';

        $bind = array();
        foreach ($fields as $field) {
            $bind[":update_$field"] = $info[$field];
        }

        return $this->query($sql, $bind, 'update');
    }

    public function queryVar($sql, $bind = array()){
        $rtn = $this->queryRow($sql, $bind);
        if(is_array($rtn) && !empty($rtn)){
            list(,$var) = each($rtn);
            return $var;
        }
    }
    public function queryRow($sql, $bind = array()){
        $rtn = $this->query($sql, $bind);
        if(is_array($rtn) && !empty($rtn)){
            return $rtn[0];
        }
    }
    /**
     *
     */
    public function query($sql, $bind = array()) {
        $sql = trim($sql);
        $arr = explode(' ', $sql);
        $action = strtolower($arr[0]);

        $log = array(
            'sql' => $sql,
            'bind' => $bind,
        );

        $return = false;
        try {
            $pdoStmt = $this->pdo->prepare($sql);
            $t1 = microtime(true);
            $result = $pdoStmt->execute($bind);
            $t2 = microtime(true);
            $log['duration'] = $t2 - $t1;
            if ($result !== false) {
                if (preg_match("/^(" . implode("|", array("select", "describe", "pragma")) . ") /i", $sql)) {
                    $return = $pdoStmt->fetchAll(\PDO::FETCH_ASSOC);
                } elseif (preg_match("/^(" . implode("|", array("delete", "insert", "update")) . ") /i", $sql)) {
                    //$return = $pdoStmt->rowCount();
                    $return = $result;
                }
            } else {
                //self::log('trace', 'execute failed', 0, $log);
            }
            return $return;
        } catch (\PDOException $e) {
            if(isset($_SERVER['DEBUG'])){
                throw new \PDOException("$sql :".$e->getMessage(), $e->getCode());
            }
            if ($pdoStmt) {
                $log['errInfo'] = $pdoStmt->errorInfo();
            } else {
                $log['errInfo'] = $this->pdo->errorInfo();
            }
            throw new \PDOException("sql $action err, {$log['errInfo'][2]}", $log['errInfo'][1]);
        }
    }
    /**
     *  Returns the last inserted id.
     *  @return string
     */
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    /**
     * Starts the transaction
     * @return boolean, true on success or false on failure
     */
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    /**
     *  Execute Transaction
     *  @return boolean, true on success or false on failure
     */
    public function commit() {
        return $this->pdo->commit();
    }

    /**
     *  Rollback of Transaction
     *  @return boolean, true on success or false on failure
     */
    public function rollBack() {
        return $this->pdo->rollBack();
    }

}
