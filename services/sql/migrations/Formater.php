<?php
namespace SQL\Migrations;

/**
 * Class Formater
 * @package SQL\Migrations
 */
class Formater
{
    protected $namespace;
    
    public function __construct($namespace)
    {    
        $this->namespace = $namespace;
    }

    /**
     * @param $type
     * @param $args
     * @return string
     */
    public function __call($type, $args)
    {
        $methods = $this->$type($args[1], $args[2]);

        return <<<EOD
<?php
namespace {$this->namespace};

class {$args[0]}
{
{$methods}
}
EOD;
    }


/**
* Пустая миграция для произвольного запроса
*
*/
    protected function empty($table)
    {
        return <<<EOD

    public function up(\$db)
    {
       \$db->query(" ");
    }

    public function down(\$db)
    {
       \$db->query(" ");
    }

EOD;
    }  
    
/**
* Создание таблицы
*/
    protected function createTable($table, $num)
    {    
        return <<<EOD

    public function up(\$db)
    {
        \$db->query("CREATE TABLE IF NOT EXISTS `{$table}` (
                       `id` int(10) NOT NULL AUTO_INCREMENT,
{$this->generateColumnNames($num)}              PRIMARY KEY `id` (`id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
                );
    }

    public function down(\$db)
    {
        \$db->query("DROP TABLE `{$table}`");
    }        

EOD;
    }

    /**
    * 
    *
    */ 
    protected function dropTable($table, $num)
    {    
        return <<<EOD

    public function up(\$db)
    {
        \$db->query("DROP TABLE `{$table}`");
    }

    public function down(\$db)
    {
        \$db->query("CREATE TABLE IF NOT EXISTS `{$table}` (
                       `id` int(10) NOT NULL AUTO_INCREMENT,
{$this->generateColumnNames($num)}              PRIMARY KEY `id` (`id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
                );
    }

EOD;
    }  
    
    /**
    * 
    *
    */ 
    protected function renameTable($table)
    {
        return <<<EOD

    public function up(\$db)
    {
       \$db->query("RENAME TABLE `{$table}` TO ``");
    }

    public function down(\$db)
    {
       \$db->query("RENAME TABLE `` TO `{$table}`");
    }

EOD;
    }
    
    /**
    * 
    *
    */ 
    protected function truncateTable($table)
    {    
        return <<<EOD

    public function up(\$db)
    {
       \$db->query("TRUNCATE TABLE `{$table}`");
    }

    public function down(\$db)
    {
        return false;
    }

EOD;
    }  
    
    /**
    * 
    *
    */ 
    protected function addColumn($table, $num)
    {    
        return <<<EOD

    public function up(\$db)
    {
       \$db->query("ALTER TABLE `{$table}`
{$this->generateColumnNames($num, 'ADD')}      
        AFTER `` ");
    }        

    public function down(\$db)
    {
       \$db->query("ALTER TABLE `{$table}`
{$this->generateColumnNames($num, 'DROP')}      
        ");        
    }

EOD;
    } 
    
    /**
    * 
    *
    */ 
    protected function dropColumn($table, $num)
    {    
        return <<<EOD

    public function up(\$db)
    {
       \$db->query("ALTER TABLE `{$table}`
{$this->generateColumnNames($num, 'DROP')}      
        AFTER `` ");  
    }        

    public function down(\$db)
    {
       \$db->query("ALTER TABLE `{$table}`
{$this->generateColumnNames($num, 'ADD')}      
        AFTER `` ");
    }

EOD;
    }

    /**
    * 
    *
    */ 
    protected function renameColumn($table)
    {    
        return <<<EOD

    public function up(\$db)
    {
        \$db->query("ALTER TABLE `{$table}` CHANGE `` ``");
    }        

    public function down(\$db)
    {
        \$db->query("ALTER TABLE `{$table}` CHANGE `` ``");
    }

EOD;
    }

    /**
    * 
    *
    */ 
    protected function afterColumn($table)
    {    
        return <<<EOD

    public function up(\$db)
    {
        \$db->query("ALTER TABLE `{$table}` CHANGE `` `` AFTER ``");
    }        

    public function down(\$db)
    {
        \$db->query("ALTER TABLE `{$table}` CHANGE `` `` AFTER ``");
    }

EOD;
    }

    /**
    * 
    *
    */ 
    protected function addPrimaryKey($table)
    {    
        return <<<EOD

    public function up(\$db)
    {
        \$db->query("ALTER TABLE `{$table}` ADD PRIMARY KEY(``);");
    }        

    public function down(\$db)
    {
        \$db->query("ALTER TABLE `{$table}` MODIFY `` INT NOT NULL;");
        \$db->query("ALTER TABLE `{$table}` DROP PRIMARY KEY;");
    }

EOD;
    }

    /**
    * 
    *
    */ 
    protected function dropPrimaryKey($table)
    {    
        return <<<EOD

    public function up(\$db)
    {
        \$db->query("ALTER TABLE `{$table}` MODIFY `` INT NOT NULL;");
        \$db->query("ALTER TABLE `{$table}` DROP PRIMARY KEY;");
    }        

    public function down(\$db)
    {
        \$db->query("ALTER TABLE `{$table}` ADD PRIMARY KEY(``);");
    }

EOD;
    }

    /**
    * 
    *
    */ 
    protected function createIndex($table, $num)
    {    
        return <<<EOD

    public function up(\$db)
    {
        \$db->query("ALTER TABLE `{$table}` ADD INDEX(``);");
    }        

    public function down(\$db)
    {
        \$db->query("ALTER TABLE `{$table}` DROP INDEX(``);");
    }

EOD;
    }

    /**
    * 
    *
    */ 
    protected function createUniqueIndex($table)
    {    
        return <<<EOD

    public function up(\$db)
    {
        \$db->query("ALTER TABLE `{$table}` ADD UNIQUE(``);");
    }        

    public function down(\$db)
    {
        \$db->query("ALTER TABLE `{$table}` DROP INDEX(``);");
    }

EOD;
    } 

    /**
    * 
    *
    */ 
    protected function dropIndex($table)
    {    
        return <<<EOD

    public function up(\$db)
    {
        \$db->query("ALTER TABLE `{$table}` DROP INDEX(``);");
    }        

    public function down(\$db)
    {
        \$db->query("ALTER TABLE `{$table}` ADD INDEX(``);");
    }

EOD;
    }

    protected function generateColumnNames($num, $option = '')
    {    
        $columns = '';
        for($i = 0; $i < $num; $i++){
            $columns .= "                     {$option}  ``,\n";
        }
        
        return $columns;
    }      
}
 
