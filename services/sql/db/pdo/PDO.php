<?php
namespace sql\db\pdo;

use sql\db\interfaces\PdoInterface;
use sql\db\sqldebug\SqlDebug;
use sql\db\language\En;

// For PHP 5.6
error_reporting(E_ALL & ~E_DEPRECATED);

/**
 * Class PDO
 * @package sql\db\pdo
 */
class PDO extends \PDO implements PdoInterface
{
    public $error;
    public $prefix;    
    public $test  = false;
    public $html  = false;

    protected $debugger;
    
    private $connect = false;

    public function __construct(array $config = [])
    {
        if (!empty($config)) {
            $this->setMode($config);
        }
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->setMode($config);
    }

    /**
     * @param array $config
     */
    public function newConnect($config = [])
    {
        if (false === $this->connect) {
            $this->setMode($config);
            $this->connector();
            $this->connect = true;
        }
    }

    /**
     * @param false $html
     * @return $this
     */
    public function test($html = false)
    {
        $this->html = $html;
        $this->test = true;
        return $this;
    }

    /**
     * @param string $sql
     * @param null $fetchMode
     * @param mixed ...$fetchModeArgs
     * @return false|\PDOStatement
     */
    public function query($sql, $fetchMode = null, ...$fetchModeArgs)
    {
        $this->newConnect($this->config);
     
        if (!empty($this->debugger)) {

            if (false === $this->checkEngine($sql)) {
                throw new \LogicException(' Component PDO: '. SQL_NO_SUPPORT);
                return false;
            }
         
            $result = @parent::query($sql);  
            $this->debugger->error = $this->errorInfo()[2];
            $this->debugger->trace = debug_backtrace();
            $this->debugger->db = $this;
            $this->debugger->component = 'PDO';
            $this->debugger->run($sql, $result);        
        } elseif (empty($this->debugger) && $this->test) {
            throw new \BadFunctionCallException('Component PDO: '. SQL_NO_DEBUGGER);
        } else {
            $result = parent::query($sql);
        }
        
        return $result;
    }

    /**
     * @param string $sql
     * @param array $options
     * @return false|\PDOStatement
     */
    public function prepare($sql, $options = [])
    { 
        $this->newConnect($this->config);
        $stmt = parent::prepare($sql, $options);
        $stmt->rawSql = $sql;
        return $stmt;
    }

    /**
     * @param string $string
     * @param null $type
     * @return false|string
     */
    public function quote($string, $type = null)
    { 
        $this->newConnect($this->config);
        return parent::quote($string, \PDO::PARAM_STR);
    }

    /**
     * @param $sql
     * @return false|\PDOStatement
     */
    public function rawQuery($sql)
    {
        return parent::query($sql);
    }

    /**
     * @param $sql
     * @return bool
     */
    public function checkEngine($sql)
    {    
        $sql = str_replace('`', '', trim($sql)) .' ';
        $sql = str_ireplace(['IGNORE', 'LOW_PRIORITY', 'DELAYED', 'INTO', 'FROM', 'QUICK'], ' ', $sql);
        preg_match('~^[INSERT|UPDATE|DELETE]+?[\s]+(.+?)[\s]+.*~i', $sql, $match);
        
        if (empty($match[1])) {
            return true;
        }
        $dbName = preg_replace('~.*?dbname=(.+?);.*~ui', '$1', $this->config['dsn']);
        $table  = preg_replace('~.*?\.~', '', $match[1]);        
        $stmt   = $this->rawQuery("SHOW TABLES LIKE '". $table ."'");
        $result = $stmt->fetchColumn();
        
        if (empty($result)) {
            throw new \LogicException(' Component PDO: '. sprintf(SQL_NO_TABLE, $table, $table));
        }
       
        $stmt  = $this->rawQuery("SELECT `ENGINE` 
                                     FROM `INFORMATION_SCHEMA`.`TABLES`
                                     WHERE `TABLE_NAME` =  ". $this->quote($table) ."
                                         AND `TABLE_SCHEMA` = '$dbName'"
                                  );
     
        $result = $stmt->fetchColumn();
        if ($result !== 'InnoDB') {
            throw new \LogicException(' Component PDO: '. sprintf(SQL_NO_SUPPORT, $result));
        }
        
        return true;
    }

    /**
     * @param $config
     */
    protected function setMode($config) 
    {
        $this->config = !empty($config) ? $config : $this->config;
        $this->prefix = isset($this->config['prefix']) ? $this->config['prefix'] : null;
        $this->debugger  = !empty($this->config['debug']) ? new SqlDebug($this->html) : null;
        En::set();
    }

    /**
     *
     */
    protected function connector()
    {
        $this->checkConfig($this->config);
        $opt = [
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_STRINGIFY_FETCHES  => false,
        ];  
     
        if (!empty($this->config['opt'])) {
            $opt = array_merge($opt, $this->config['opt']);
        } 
        parent::__construct($this->config['dsn'], $this->config['user'], $this->config['pass'], $opt);
      
        if (!empty($this->config['debug'])) {
            $this->setAttribute(\PDO::ATTR_STATEMENT_CLASS, [__NAMESPACE__ .'\Shaper', [$this]]);
            $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
        }
    }

    /**
     * @param array $config
     * @return bool
     */
    protected function checkConfig($config = [])
    {
        extract($config);

        if (!isset($dsn, $user, $pass)) {
            throw new \InvalidArgumentException(' Component PDO: '. SQL_WRONG_CONNECTION);
        }
        
        return true;
    }
}
