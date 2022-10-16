<?php
namespace sql\migrations;

use sql\db\pdo\Pdo;

/**
 * Class Migrations
 * @package SQL\Migrations
 */
class Migrations
{
    protected $config;
    protected $db;
    protected $loger;
    protected $formatter;    
    protected $report = [];

    public function __construct($config)
    {
        $this->config  = $config['migrations'];
        $lang = !empty($this->config['language']) ? $this->config['language'] : 'En';
        $lang = '\sql\migrations\language\\'. $lang;
        $lang::set();
        $this->db = new Pdo($this->config['db']);
        $this->db->html = false;
        $this->formatter = new Formater($this->config['namespace']);
        $this->loger = new Loger($this->db);
        $report = $this->loger->createTable();
        
        if(false !== $report){
            $this->report = [$report];
        }
    }

    /**
     * @param $argv
     * @return false
     */
    public function route($argv)
    {
        if(empty($argv[1])){
            exit;
        }
        
        $rout = explode('/', $argv[1]);
        $arg1 = $argv[2] ?? null;
        $arg2 = $argv[3] ?? null;
        $arg3 = $argv[4] ?? null;
        
        if(empty($rout[1])){
           return $this->run($arg1);
        }
        
        $command = $rout[1] ?? null;
        switch(strtolower($command)){
            case 'create' :
                if(isset($arg1)){    
                    $this->report[] = $this->create($arg1, $arg2 ?? '--e', $arg3 ?? 2);                
                } else {
                    $this->report[] = MIGRATION_NO_TABLENAME;
                }
            break;
         
            case 'wipe' :
                $this->report[] = $this->loger->wipe($arg1);
            break;
         
            case 'rollback' :
                $this->report[] = (new Executor($this->config, $this->db, $this->loger))->rollback($arg1 ?? 10);
            break;
            
            case 'history' : 
                $report = $this->getHistory($arg1 ?? 20);
               $this->report[] = empty($report) ? MIGRATION_APPLY_EMPTY 
                                                : Helper::prepareReport($report, MIGRATION_HISTORY);
            break;
            
            case 'help' : 
            case 'doc' :  
                print((new Doc)->getDocRu());
            break;
            
            default :
                $this->report[] = sprintf(MIGRATION_INVALID_COMMAND, $command);    
            
        }
    }

    /**
     * @param $limit
     * @return array
     */
    protected function getHistory($limit)
    {
        $history = $this->loger->getHistory($limit);
        $report  = [];
        foreach($history['version'] as $k => $item){
            $report[] = $item .' ('. date('Y-m-d H:i:s', $history['apply_time'][$k]) .')';
        }
        return $report;
    }

    /**
     * @param $name
     * @param $type
     * @param $numcol
     * @return string
     */
    protected function create($name, $type, $numcol)
    {
        $date   = date('ymd_His');
        $format = $method = Dialog::getFormat($type);
        $format = ('empty' !== $format) ? $format .'_' : '';
        $class  = 'm'. $date .'_'. $format . $name;
        $text   = $this->formatter->$method($class, $name, $numcol);
     
        if(false === @file_put_contents($this->config['dir'] . $class .'.php', $text)){
            return sprintf(MIGRATION_INVALID_PATH, $this->config['dir'], $this->config['dir']);
        }
     
        return sprintf(MIGRATION_SUCCESS, $class);
    }

    /**
     * @param $limit
     * @return false
     */
    protected function run($limit)
    { 
        $limit = strtolower($limit);
        $limit = '' === $limit || 'all' === $limit ? 'all' : $limit;        
        if(false === ($migrates = $this->getNewMigrations($limit))) {
            $this->report[] = sprintf(MIGRATION_INVALID_COMMAND, $limit);
            return false;
        } elseif(empty($migrates)) {
            $this->report[] = MIGRATION_NEW_EMPTY;
            return false;
        }
     
        print(Helper::prepareReport($migrates, MIGRATION_LIST_NEW));
        switch(Dialog::confirm(MIGRATION_APPLY)) {
            case 1 :
                $executor = new Executor($this->config, $this->db, $this->loger);
                $result = $executor->run($migrates);
                $mess = $executor->getMessage();
                $this->report[] = Helper::prepareReport($result, $mess);
                return false;
            case 2 :
                $this->report[] = MIGRATION_CANCEL;
                return false;
                
        }    
    }

    /**
     * @param $limit
     * @return array|false
     */
    protected function getNewMigrations($limit)
    {
        if(!is_numeric($limit) && strtolower($limit) !== 'all' && !is_null($limit)) {
            return false;
        }
        $history = $this->loger->getHistory('all')['version'];   
        $migrations = scandir($this->config['dir']);
        array_splice($migrations, 0, 2);
        
        $newMigrates = [];
        foreach($migrations as $migrate){
            $migrate = basename($migrate, '.php');
            if(!in_array($migrate, $history)){
                $newMigrates[] = $migrate;
            }
        }
     
        if((int)$limit > 0){
            return array_slice($newMigrates, -(int)$limit);
        }
        
        return $newMigrates;
    }

    /**
     * @return string
     */
    public function getReport()
    {
        return Helper::stripTags(implode(PHP_EOL, $this->report)) . PHP_EOL;
    }    
}
 