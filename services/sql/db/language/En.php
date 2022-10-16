<?php
namespace sql\db\language;

/**
 * Class En
 * @package sql\db\language
 */
class En
{
    
    public static function set() 
    {
        if(!defined("SQL")){
         
            define("SQL", true);
            
            define('SQL_WRONG_CONNECTION',         ' wrong data connection in the configuration file ');
            define('SQL_NO_DEBUGGER',              ' SQL debugger is inactive. Set to true debug configuration. ');    
            define('SQL_INVALID_MYSQLI_TYPE',      ' Number of elements in type definition string doesn\'t match number of bind variables');
            define('SQL_NO_MYSQLI_TYPE',           ' Unknown type of the parameter ');
            define('SQL_ERROR',                    ' Query build error ');
            define('SQL_EMPTY_ARGUMENTS',          ' Too few arguments to method %s');  
            define('SQL_DISABLE',                  ' Blocked');        
            define('SQL_TRANSACTION_EXIST',        ' There is already an active transaction ');
            define('SQL_TRANSACTION_ERROR',        ' Transaction error: '); 
            define('SQL_NO_SUPPORT',               ' Type %s is not supported by the debugger ');
            define('SQL_OTHER_OBJECT',             ' An inappropriate object is used ');
            define('SQL_NO_TABLE',                 ' Table "%s" doesn\'t exists');
        }        
    }
}
















