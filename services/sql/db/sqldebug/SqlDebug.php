<?php
namespace sql\db\sqldebug;


/**
 * Class SqlDebug
 * @package sql\db\sqldebug
 */
class SqlDebug
{
   
    public $sizeListing = 30; 
    public $db;
    public $error;
    public $trace;
    
    protected $pref;  
    protected $view; 
    protected $explain;    
    protected $file;
    protected $line;
    protected $message = 'SQL error: ';
    
    public function __construct($html = true)
    {
        $this->html = $html;
        $this->view = new View;
    }
    
    /**
    * Start debug
    *
    * $param string $sql
    *
    * @return void
    */     
    public function run($sql, $result)
    {
        if (false === $result || $this->db->test) {
          
            if (false === $result) {
                $this->errorReport($sql);
            } else {
                $this->testReport($sql);
            }  
        }
    } 
    
    /**
    * It generates a report on a SQL query error
    *  
    * @param string $sql
    *
    * @return void
    */        
    public function errorReport($sql)
    { 
        $raw = $this->prepareSqlListing($sql, $this->error);
        
        $data = ['message' => $this->component .': <b>'. $this->message .'</b>',
                 'pref'    => $this->pref,
                 'error'   => $this->error,
                 'num'     => $raw['num'],
                 'sql'     => $raw['sql'],
                 'explain' => $this->explain,
                 'php'     => $this->preparePhp(),
                 'file'    => $this->file,
                 'line'    => $this->line,
        ];

        $this->view->createReport($data, $this->html);
        die;
    } 
    
    /**
    * Test query
    *
    * @param string $sql
    *
    * @return void
    */       
    public function testReport($sql)
    {    
        $this->message = null;
        $start = microtime(true);      
        $this->db->rawQuery($sql); 
        $time = sprintf("%01.4f", microtime(true) - $start);
        $this->explain = $this->performExplain($sql, $time);
        $this->pref = 'Testing the ';        
        $this->errorReport($sql); 
    }

    /**
    * Prepares listing SQL
    *
    * @param string $sql
    * @param string $error
    *
    * @return array
    */    
    protected function prepareSqlListing($sql, $error = '')
    { 
        $sql   = htmlSpecialChars($sql);
        $error = htmlSpecialChars($error);
        
        if (!empty($error)) {
            preg_match("#'(.+?)'#is", $error, $location);
           
            if (!empty($location[1])) {
                $sql = $this->view->highlightLocation($sql, $location[1]);
            }
        }
     
        $cnt = substr_count($sql, "\n") + 2;
        $num = range(1, $cnt);
        return ['num' => $num, 'sql' => $sql];
    }
 
    /**
    * EXPLAIN
    *
    * @param string $sql
    * @param string $time
    *
    * @return null
    */    
    protected function performExplain($sql, $time)
    {    
        if (!preg_match('~^select.+~is', trim($sql))) {
            return null;
        }
        
        $res = $this->db->rawQuery("EXPLAIN PARTITIONS ". $sql);
       
        if (is_object($res)) {
            $data = $res->fetchAll()[0];
          
            $data['queryTime'] = $time;
            return $this->view->createExplain($data);
        }
        
        return null;
    } 
 
    /**
    * Generates problematic PHP code section 
    *
    * @return string
    */    
    protected function preparePhp()
    {
        $php = '';
        $i = 0;
        $hide = ['Shaper.php', 'DbCommand'];
       
        foreach ($this->trace as $block) {
         
            if (empty($block['file'])) {
                continue;
            }
         
            $name = basename($block['file']);
            $dir  = basename(dirname($block['file']));
            
            if (in_array($name, $hide) || in_array($dir, $hide)) {
                continue; 
            }    
            
            break;        
        }
        
        $this->file = $block['file'];
        $this->line = $block['line'];
        $script = file($block['file']);
        $ext = ceil($this->sizeListing / 2);
        $position = ($block['line'] <= $ext) ? 0 : $block['line'] - $ext;
        
        foreach ($script as $string) {
            ++$i;
         
            if($i == $block['line']) {
                $lines[] = $this->view->wrapLine($i, 'error');
            } elseif($i == $block['line']) {
                $lines[] = $this->view->wrapLine($i, 'trace');
            }
            else {
                $lines[] = $i;
            }
            
            $php .= $string;
        } 
       
        $data['num'] = array_slice($lines, $position, $this->sizeListing);
        $data['total'] = $this->view->highlightString($php, $position, $this->sizeListing);
        $cnt = substr_count($data['total'], "\n") + 2;
        return $this->view->createPhp($data);
    }
}
