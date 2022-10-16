<?php
namespace sql\migrations\language;

/**
 * Class Ru
 * @package sql\migrations\language
 */
class Ru
{
    public static function set() 
    {
        if(!defined("MIGRATION")){
         
            define("MIGRATION", true);

            define("MIGRATION_ERROR",               "Unidentified migrations error. (Неопознанная ошибка миграции)");
            define("MIGRATION_WAIT",                "Please wait, writing history.". PHP_EOL ."Пожалуйста ждите, пишем историю.");
            define("MIGRATION_FAILED_TABLE",        "Failed to create migration table. (Не удалось создать таблицу миграций)");
            define("MIGRATION_SUCCESS",             "Migration %s successfully created. (Миграция успешно создана)");
            define("MIGRATION_TABLE_SUCCESS",       "Migration table created successfully. (Таблица миграций успешно создана)"); 
            define("MIGRATION_NO_CREATE",           "Failed to create migration: %s (Не удалось создать миграцию.");         
            define("MIGRATION_NO_EXECUTED",         "Failed to execute migration: %s (Не удалось исполнить миграцию.)");          
            define("MIGRATION_NO_TABLENAME",        "Table name not specified. (Не указано имя таблицы.)");
            define("MIGRATION_NO_CONFIRM",          PHP_EOL ."Confirmation requi#f98a8a. (Требуется подтверждение)". PHP_EOL );
            define("MIGRATION_INVALID_COMMAND",     PHP_EOL ."Migration command \"%s\" not recognized. (Команда миграции не распознана)");
            define("MIGRATION_CLEAR",               "Migration history table clear. (Таблица истории миграций очищена)");
            define("MIGRATION_DELETE",              "Removed of recent migrations from history: %s  (Удалено из истории миграций: %s)". PHP_EOL);
            define("MIGRATION_NO_CLEAR",            "Failed to clear migration history. (Не удалось очистить историю миграций)");
            define("MIGRATION_NEW_EMPTY",           "New migrations not found. (Новых миграций не найдено)");
            define("MIGRATION_APPLY_EMPTY",         "Applied migrations not found. (Выполненных миграций не найдено)");
            define("MIGRATION_APPLY",               PHP_EOL ."Apply migrations? (Применить миграции?) Y/N". PHP_EOL);
            define("MIGRATION_CANCEL",              PHP_EOL ."Operation canceled. (Операция отменена)");
            define("MIGRATION_LIST_NEW",            PHP_EOL ."List of new migrations. (Список новых миграций):". PHP_EOL);
            define("MIGRATION_LIST_EXECUTE",        PHP_EOL ."Successfully completed migrations. (Успешно выполненные миграции):". PHP_EOL);
            define("MIGRATION_LIST_CANCEL",         PHP_EOL ."Successfully canceled migrations. (Успешно отмененные миграции):". PHP_EOL);
            define("MIGRATION_EXECUTE_ERROR",       PHP_EOL ."Migration \"%s\" execution error.". PHP_EOL ." (Ошибка выполнения миграции)". PHP_EOL);
            define("MIGRATION_ROLLBACK",            PHP_EOL . PHP_EOL. "Roll back migrations? (Откатить миграции?) Y/N"
            . PHP_EOL);
            define("MIGRATION_NO_ROLLBACK",         PHP_EOL ."Failed to roll back migrations (Не удалось откатить миграции)". PHP_EOL);
            define("MIGRATION_HISTORY",             PHP_EOL ."History of completed migrations. (История исполненных миграций)". PHP_EOL);
            define("MIGRATION_INVALID_PATH",        "Failed to create migration. Check for directories under path %s (Не удалось создать миграцию. Проверьте наличие директорий по пути %s)");
        }
    }
    
}
    
