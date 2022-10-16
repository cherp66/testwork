<?php
namespace SQL\Migrations;
 
/** 
 * Миграции
 * 
 * NOTE: Requires PHP version 5.5 or later
 * @license http://www.wtfpl.net/ 
 */  
class Loger
{
    const MIGRATIONS_TABLE = '_migration';
    public $db;
    
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
    * Создание таблицы истории
    *
    */ 
    public function createTable()
    {
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `". self::MIGRATIONS_TABLE ."` (
                                        `version` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
                                        `apply_time` int(10) DEFAULT NULL,
                                          UNIQUE KEY `version` (`version`)
                                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

            return $this->insertBegin() ? MIGRATION_TABLE_SUCCESS : null;
        } catch (\Throwable $t) {
            return MIGRATION_FAILED_TABLE . PHP_EOL . $t->getMessage();
        }
        
        return false;
    }
    
    /**
    * Запись в историю
    *
    */ 
    public function markMigrations($migrates, $progressBar)
    {
        $migrates = !is_array($migrates) ? [$migrates] : $migrates;
        $insert = [];
        $cnt = 0;
        foreach($migrates as $migrate){
           $cnt = $cnt > 10 ? 0 : $cnt;
           $progressBar->create($cnt++);
           $insert[] = [$migrate, time()];
           sleep(1);
        }
        $part = array_fill(0, count($insert), "(?, ?)");
        $stmt = $this->db->prepare("INSERT INTO `". self::MIGRATIONS_TABLE ."`
                 (`version`, `apply_time`) VALUES "
                 . implode(", ", $part));
        $i = 1;
        foreach($insert as $item) {
           $stmt->bindValue($i++, $item[0]);
           $stmt->bindValue($i++, $item[1]);
        }        
        $stmt->execute();
        $progressBar->end("It's okay.". PHP_EOL ."Все отлично.");
    } 
    
    /**
    * Удаление из истории
    *
    */ 
    public function deleteMigrations($migrates)
    {
        $migrates = !is_array($migrates) ? [$migrates] : $migrates;
        $migrates = array_values($migrates);
        try{
            $this->db->query("DELETE FROM `". self::MIGRATIONS_TABLE ."`
                WHERE `version` IN ('". implode("', '", $migrates) ."')");
                
            $num = count($migrates);
            return Helper::prepareReport($migrates, sprintf(MIGRATION_DELETE, $num, $num));
        } catch (\Throwable $t) {
            return MIGRATION_NO_CLEAR . PHP_EOL . $t->getMessage();
        }
    } 
    
    /**
    * Просмотр истории
    *
    */ 
    public function getHistory($limit = 10)
    {
        if('all' === strtolower($limit)){
            $res = $this->db->query("SELECT * FROM `". self::MIGRATIONS_TABLE ."` 
                              order by `version` ASC");
         
        } else {
            $res = $this->db->query("SELECT * FROM 
                                (SELECT * FROM `". self::MIGRATIONS_TABLE ."` 
                                    ORDER BY `version` DESC  LIMIT ". (int)$limit .") m
                               ORDER BY m.`version` ASC");
        }
        
        $result = ['version' => [], 'apply_time' => []];
        $rows = $res->fetchAll();
        foreach ($rows as $row)
        {
            if($row['version'] === '000000_begin') continue;
            $result['version'][] = $row['version']; 
            $result['apply_time'][] = $row['apply_time'];
        }
        return $result;
    }  

    /**
    * Очистка истории
    *
    */ 
    public function wipe($limit = 10)
    {   
        if(!empty($limit)){
            $migrations = $this->getHistory($limit)['version'];
            if(!empty($migrations)){
                return $this->deleteMigrations($migrations);
            }
            return MIGRATION_EMPTY;
        }
        
        try {
            $this->db->query("TRUNCATE TABLE `". self::MIGRATIONS_TABLE ."`");
            $this->insertBegin();
            return MIGRATION_CLEAR;
        } catch (\Throwable $t) {
            return MIGRATION_NO_CLEAR . PHP_EOL . $t->getMessage();
        }
    }
    
    /**
    * Старт
    *
    */ 
    protected function insertBegin()
    {
        $res = $this->db->query("INSERT IGNORE INTO `". self::MIGRATIONS_TABLE ."` (`version`, `apply_time`) 
                                        VALUES ('000000_begin', '". time() ."')");
                                    
        return $res->rowCount() > 0;
    }
}

