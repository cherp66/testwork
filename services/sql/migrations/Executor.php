<?php
namespace SQL\Migrations;
 
/** 
 * Миграции
 * 
 * NOTE: Requires PHP version 5.5 or later
 * @license http://www.wtfpl.net/ 
 */  
class Executor
{
    protected $config;
    protected $db;
    protected $loger;
    protected $message;
    protected $compiled = [];
    
    public function __construct($config, $db, $loger)
    {
        $this->config  = $config;    
        $this->db = $db;
        $this->loger = $loger;
        $this->progressBar = new ProgressBar;
    }

    /**
    * Исполнение миграции
    *
    */ 
    public function run($migrates)
    {
        try {
            $this->apply($migrates, 'up');
            $this->mark($this->compiled, 'up');
            $this->message = MIGRATION_LIST_EXECUTE;
            return $this->compiled;
            
        } catch (\Exception $e) {
            if(!empty($this->compiled)){
                $this->mark($this->compiled, 'up');
                print(Helper::prepareReport($this->compiled, MIGRATION_LIST_EXECUTE));
            }
            print(PHP_EOL . Helper::stripTags($e->getMessage()) . PHP_EOL);
            
            switch(Dialog::confirm(MIGRATION_ROLLBACK)) {
                case 1 :
                    try{
                        $this->apply(array_reverse($this->compiled), 'down');
                    } catch (\Throwable $t){
                        $this->progressBar->end(Helper::stripTags($e->getMessage()));
                        return false;
                    }
                    
                    if(!empty($this->compiled)){
                        $this->message = MIGRATION_LIST_CANCEL . PHP_EOL;
                    }
                    
                    $this->mark($this->compiled, 'down');                    
                    return array_unique($this->compiled);
                case 2 :
                    print(MIGRATION_CANCEL . PHP_EOL);
                    return false;            
            }
            return false;
        }
    }
    
    /**
    * Исполнение миграции
    *
    */ 
    public function rollback($limit = 10)
    {
        $migrates = $this->loger->getHistory($limit, 'desc')['version'];
        if(!empty($migrates)){
            try{
                $this->apply($migrates, 'down');
                $this->mark($this->compiled, 'down');
                return Helper::prepareReport($this->compiled, MIGRATION_LIST_CANCEL);
            } catch (\Throwable $t) {
                return MIGRATION_NO_ROLLBACK . PHP_EOL . Helper::stripTags($t->getMessage());
            }
        }
        sleep(1);
        $this->progressBar->clear();
        return MIGRATION_APPLY_EMPTY;
    }
    
    /**
    * Исполнение миграции
    *
    */ 
    public function apply($migrates, $method)
    {
        $migrates = !is_array($migrates) ? [$migrates] : $migrates;
        $migrate = '';
        try{ 
            $cnt = 0;
            foreach($migrates as $migrate){
                $cnt = $cnt > 10 ? 0 : $cnt;
                $class = $this->config['namespace'] .'\\'. $migrate;
                if(false === (new $class)->$method($this->db)){
                    throw new \Exception(sprintf(MIGRATION_EXECUTE_ERROR, $migrate));
                }
                $this->compiled[] = $migrate;
                $this->progressBar->create($cnt++);
            }
            
            $this->progressBar->end(MIGRATION_WAIT);
        } catch(\Throwable $t){
            throw new \Exception(sprintf(MIGRATION_EXECUTE_ERROR, $migrate) 
            . PHP_EOL . Helper::stripTags($t->getMessage()));
        }
    }
    
    /**
    * Исполнение миграции
    *
    */ 
    protected function mark($migrates, $method)
    {
        switch($method){
            case 'up':
                $this->loger->markMigrations($migrates, $this->progressBar);
            break;
            case 'down':
                $this->loger->deleteMigrations($migrates);
            break;
        }

    } 
    
    /**
    * Сообщение
    *
    */ 
    public function getMessage()
    {
        return $this->message;
    }
}
    