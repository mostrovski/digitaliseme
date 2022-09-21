<?php
namespace Core;

class Database {

    protected $host = DB_HOST;
    protected $user = DB_USER;
    protected $password = DB_PASSWORD;
    protected $name = DB_NAME;
    protected $charset = DB_CHARSET;
    protected $handler;
    protected $statement;
    protected $error;

    public function __construct() {
        // Set the data source name and options
        $dsn = 'mysql:host='.$this->host;
        $dsn .= ';dbname='.$this->name;
        $dsn .= ';charset='.$this->charset;
        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ
        ];
        // Connect to the database
        try {
            $this->handler = new \PDO(
                $dsn,
                $this->user,
                $this->password,
                $options
            );
        } catch(\PDOException $e){
            $this->error = $e->getMessage();
            echo $this->error;
        }
    }

    public function __destruct() {
        // Close the connection
        $this->statement = null;
        $this->handler = null;
    }
    /************************************************************************
     *
     *                             PREPARE
     *
     ***********************************************************************/
    protected function prepareQuery($sql) {
        $this->statement = $this->handler->prepare($sql);
    }
    /************************************************************************
     *
     *                             BIND
     *
     ***********************************************************************/
    protected function bind($param, $value) {
        if (is_int($value)) {
            $type = \PDO::PARAM_INT;
        } else if (is_bool($value)) {
            $type = \PDO::PARAM_BOOL;
        } else if (is_null($value)) {
            $type = \PDO::PARAM_NULL;
        } else {
            $type = \PDO::PARAM_STR;
        }
        $this->statement->bindValue($param, $value, $type);
    }
    /************************************************************************
     *
     *                             EXECUTE
     *
     ***********************************************************************/
    protected function execute($sql, $params, $values) {
        $this->prepareQuery($sql);
        if (is_array($params)) {
            $len = count($params);
            for ($i=0; $i<$len; $i++) {
                $this->bind($params[$i], $values[$i]);
            }
        } else {
            $this->bind($params, $values);
        }
        $this->statement->execute();
    }
    /************************************************************************
     *
     *                             SELECT
     *
     ***********************************************************************/
    public function fetchWithoutParams($sql) {
        // For selecting without parameters
        $this->statement = $this->handler->query($sql);
        return $this->statement->fetchAll();
    }

    public function fetchSingleRow($sql, $params, $values) {
        // For selecting a single row
        $this->execute($sql, $params, $values);
        return $this->statement->fetch();
    }

    public function fetchSingleField($sql, $params, $values) {
        // For selecting a single field => (i) use with count <=
        $this->execute($sql, $params, $values);
        return $this->statement->fetchColumn();
    }

    public function fetchMultipleRows($sql, $params, $values) {
        //  For selecting multiple rows
        $this->execute($sql, $params, $values);
        return $this->statement->fetchAll();
    }
    /************************************************************************
     *
     *                             INSERT
     *
     ***********************************************************************/
    public function insertIntoTable($sql, $params, $values) {
        $this->execute($sql, $params, $values);
        return $this->handler->lastInsertId();
    }
    /************************************************************************
     *
     *                             UPDATE
     *
     ***********************************************************************/
    public function updateTable($sql, $params, $values) {
        $this->execute($sql, $params, $values);
        return $this->statement->rowCount();
    }
    /************************************************************************
     *
     *                             DELETE
     *
     ***********************************************************************/
    public function deleteFromTable($sql, $params, $values) {
        $this->execute($sql, $params, $values);
        return $this->statement->rowCount();
    }
}
?>