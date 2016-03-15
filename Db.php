<?php
/**
 * @refer http://codereview.stackexchange.com/questions/29362/class-for-reducing-development-time
 */
class Db{

    public function __construct($config) {
        $dsn = "mysql:host=".$config['host'].';port='.$config['port'].';dbname='.$config['dbname'];
        $options = array(
            \PDO::ATTR_PERSISTENT => false,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_EMULATE_PREPARES => true,
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8" ,
        );

		$this->pdo = new \PDO($dsn, $config['user'], $config['password'], $options);
		//$this->pdo = new \PDO('mysql:host=127.0.0.1;dbname=test', 'root', '123456', $options);
		$this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
    }
	/**
	 * @return static
	 */
	static function in($config){
		return new static($config);
	}

    /**
     *
     * @param $table string
     * @param $info array
     */
    public function insert($table, $info) {
        $fields = array_keys($info);
        $sql = "INSERT INTO " . $table . " (" . implode($fields, ", ") . ") VALUES (:" . implode($fields, ", :") . ");";
        $bind = array();
        foreach ($fields as $field) {
            $bind[":$field"] = $info[$field];
        }
        return $this->run($sql, $bind, 'insert');
    }

    public function insertBatch($table, $infos, $duplicate = false) {
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
           $sql .= " ON DUPLICATE KEY UPDATE id=id";
       }

        $bind = array();
        foreach ($infos as $info) {
            $bind = array_merge($bind, array_values($info));
        }
        $rtn = $this->run($sql, $bind, 'insert');
        return $rtn;
    }
    public function queryVar($sql, $bind = array()){
        $rtn = $this->queryRow($sql, $bind);
        if(is_array($rtn) && !empty($rtn)){
            list(,$var) = each($rtn);
            return $var;
        }
    }
    public function queryRow($sql, $bind = array()){
        $rtn = $this->run($sql, $bind);
        if(is_array($rtn) && !empty($rtn)){
            return $rtn[0];
        }
    }
	/**
	 *
	 */
    public function run($sql, $bind = array()) {
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
                    $return = $pdoStmt->rowCount();
                }
            } else {
                //self::log('trace', 'execute failed', 0, $log);
            }
            return $return;
        } catch (\PDOException $e) {
            if(isset($_SERVER['DEBUG'])){
                var_dump(['log'=>$log, 'sql'=>$sql, 'file'=> __FILE__, 'msg'=>$e->getMessage()]);
                die;
            }
                var_dump(['log'=>$log, 'sql'=>$sql, 'file'=> __FILE__, 'msg'=>$e->getMessage()]);
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
