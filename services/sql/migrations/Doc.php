<?php
namespace SQL\Migrations;

/** 
 * Миграции
 * 
 * NOTE: Requires PHP version 5.5 or later
 * @license http://www.wtfpl.net/ 
 */  
class Doc
{
    public function getDocRu()
    {    
        return  <<<EOD
        
        КРАТКОЕ РУКОВОДСТВО ПО РАБОТЕ С МИГРАЦИЯМИ.
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -    
    Команда    |   Аргумент   |  Oпции   |   Доп   |   Действие
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
  migrate/help|doc                                  Помощь
   
  migrate           <num>                           Показывть список невыполненных миграций и
                                                    выполнить их.
                                                    Аргументом можно задать количество 
                                                    последних. По умолчанию все.
                                                    
  migrate/history   <num>                           Показать список выполненных миграций.
                                                    num - количество последних, 
                                                    по умолчанию 20
                                                    
  migrate/rollback  <num>                           Откатить миграции.
                                                    num - количество последних, 
                                                    по умолчанию все
                                                    
  migrate/wipe                                      Очистить историю миграций.
  
                                                    
  migrate/create  <TableName>   --e                 Команда сооздает миграцию
                                --empty              (пустую)
                                         
                                --с                  - миграция CreateTable
                                --create      <num>  (создать таблицу, к-во столбцов)
                             
                                --d                  - миграция DropTable
                                --drop        <num>  (удалить таблицу, к-во столбцов в down)
                             
                                --r                  - миграция RenameTable
                                --rename             (переименовать таблицу)
                              
                                --t                  - миграция TruncateTable
                                --truncate           (очистить таблицу)
                                            
                                --ac                 - миграция АddColumn
                                --addcolumn   <num>  (добавить столбцы, к-во)
                             
                                --dc                 - миграция DropColumn
                                --dropcolumn  <num>  (удалить столбцы, к-во столбцов в down) 
                             
                                --rc                 - миграция RenameColumn
                                --renamecolumn       (переименовать столбец) 
                                
                                --af                 - миграция AfterColumn
                                --aftercolumn        (изменить тип столбца)
                             
                                --ap                 - миграция AddPrymary
                                --addprymary         (добавить первичиный ключ)
                                
                                --dp                 - миграция DropPrymary
                                --dropprymary        (удалить первичиный ключ)
                                
                                --ci                 - миграция CreateIndex
                                --createindex  <num> (создать индекс, к-во столбцов в нем)
                             
                                --cu                 - миграция CreateUniqueIndex
                                --createunique <num> (создать уникальный индекс, к-во столбцов)
                                
                                --di                 - миграция DropIndex
                                --drop  index        (удалить индекс)
                                
 - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -     
EOD;
    }
}
 
