<?php
namespace sql\migrations\language;

/**
 * Class En
 * @package sql\migrations\language
 */
class En
{
    
    public static function set() 
    {
        if(!defined("MIGRATION")){
         
            define("MIGRATION", true);
            /**
            * migrations
            */ 
            define('MIGRATION_ERROR',               'Unidentified migrations error.');
            define('MIGRATION_WAIT',                'Please wait, writing history');
            define('MIGRATION_FAILED_TABLE',        'Failed to create migration table.');        
            define('MIGRATION_SUCCESS',             'Migration %s successfully created.');
            define('MIGRATION_TABLE_SUCCESS',       'Migration table created successfully.'); 
            define('MIGRATION_NO_CREATE',           'Failed to create migration: %s');        
            define('MIGRATION_INVALID_PATH',        'Failed to create migration. Check for directories under path %s');   
            define('MIGRATION_NO_EXECUTED',         'Failed to execute migration: %s');
            define('MIGRATION_NO_TABLENAME',        'Table name not specified.');
            define("MIGRATION_NO_CONFIRM",          "\nConfirmation required. (Требуется подтверждение)\n");        
            define('MIGRATION_INVALID_COMMAND',     "\nMigration command \"%s\" not recognized.");
            define('MIGRATION_CLEAR',               'Migration history table cleared.');
            define('MIGRATION_DELETE',              "Removed of recent migrations from history: %s\n");        
            define('MIGRATION_NO_CLEAR',            'Failed to clear migration history.');
            define('MIGRATION_EMPTY',               'New migrations not found.');
            define('MIGRATION_APPLY_EMPTY',         'Applied migrations not found.');
            define('MIGRATION_LIST_NEW',            "List of new migrations:\n");
            define('MIGRATION_APPLY',               "\nApply migrations? Y/N\n");
            define('MIGRATION_CANCEL',              "\nOperation canceled.");
            define('MIGRATION_LIST_NEW',            "\nList of new migrations:\n");        
            define('MIGRATION_LIST_EXECUTE',        "\nSuccessfully completed migrations:\n");
            define('MIGRATION_LIST_CANCEL',         "\nSuccessfully canceled migrations:\n");        
            define('MIGRATION_EXECUTE_ERROR',       "\nMigration \"%s\" execution error.\n");
            define('MIGRATION_ROLLBACK',            "\nRoll back migrations? Y/N\n");
            define('MIGRATION_HISTORY',             "\nHistory of completed migrations.\n");    
        }        
    }
}
















