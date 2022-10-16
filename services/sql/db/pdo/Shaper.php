<?php
namespace sql\db\pdo;

/**
 * Class Shaper
 * @package sql\db\Pdo
 */
class Shaper extends \PDOStatement
{
    public $rawSql;   

    protected $pdo;   
    protected $bound;

    
    /**
    * Конструктор
    *
    * @param object $pdo
    * @param string $sql
    *    
    */     
    protected function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    
    /**
    * Подготавливает параметры для запроса.
    *
    * @param mixed $param
    * @param mixed &$value
    * @param int $type
    * @param int $length
    * @param mixed $driver
    *    
    * @return bool
    */
    public function bindParam($param, &$value, $type = PDO::PARAM_STR, $length = 0, $driver = null)
    {
        $this->bound[$param] = ['value' => &$value,
                                'type'  => $type
        ]; 
     
        return parent::bindParam($param, $value, $type, $length, $driver);
    }
    
    /**
    * Подготавливает параметры для запроса.
    *
    * @param mixed $param
    * @param mixed $value
    * @param int $type
    *    
    * @return bool
    */    
    public function bindValue($param, $value, $type = PDO::PARAM_STR)
    {
        $this->bound[$param] = ['value' => $value,
                                'type'  => $type
        ];
     
        return parent::bindValue($param, $value, $type);
    }
    
    /**
    * Выполняет запрос.
    *
    * @param array $params
    *    
    * @return void
    */
    public function execute($params = null)
    {
        $sql = $this->createSqlString($params);
        $check = $this->pdo->checkEngine($sql);
        
        if (null === $check) {
            return false;
        }
        
        if ($this->pdo->inTransaction()) {
            $this->pdo->exec("SAVEPOINT sqldebug");
            $this->pdo->query($sql);            
            $this->pdo->exec("ROLLBACK TO SAVEPOINT sqldebug");
        } else {
            $this->pdo->beginTransaction();
            $this->pdo->query($sql);
            $this->pdo->rollback();        
        }
        
        return parent::execute($params);
    }

    /**
    * Генерирует результирующий SQL.
    * 
    * @param array $params
    *
    * @return string
    */ 
    protected function createSqlString($params = null)
    {
        $sql = $this->rawSql;
     
        $params = !empty($this->bound) ? $this->bound : $params;
        
        if (!empty($params)) {
            ksort($params);
            foreach ($params as $marker => $param) {
                $replace = (is_array($param)) ? $param
                                              : ['value' => $param,
                                                 'type'  => PDO::PARAM_STR ];
                $replace = $this->escape($replace);
                $sql     = $this->replace($sql, $marker, $replace);
            }
        }
     
        return $sql;
    }
    
    protected function replace($sql, $marker, $replace)
    {
        if (is_numeric($marker)) {
            $marker = '\?';
        } else {
            $marker = (preg_match('/^\:/', $marker)) ? $marker : ':' . $marker;
        }
     
        return preg_replace('#'. $marker .'(?!\w)#', $replace, $sql, 1);
    }
    
    /**
    * Обрабатывает параметры для дебаггинга в зависимости от типа.
    *
    * @param string $param
    *    
    * @return string
    */     
    protected function escape($param)
    {
        if (empty($param)) {
            throw new \InvalidArgumentException('Params is empty');
        }
        switch ($param['type']) {
            case PDO::PARAM_INT :
                return (int)$param['value'];
                
            case PDO::PARAM_STR :
                return $this->pdo->quote($param['value']);
                
            default :
                return $param['value'];
        }   
    } 
}
