<?php
namespace SQL\Migrations;

/** 
 * Миграции
 * 
 * NOTE: Requires PHP version 5.5 or later
 * @license http://www.wtfpl.net/ 
 */  
class Helper
{
    /**
    * Подготовка отчета
    *
    */ 
    public static function prepareReport($result, $message = '')
    {
        if(is_string($result)){
            return $result;
        } elseif(is_bool($result)){
            return null;
        }
        
        $numResult = [];
        for($i = 1; $i <= count($result); $i++) {
           $numResult[] = ($i) .') '. $result[$i - 1];
        }
     
        return $message . implode(PHP_EOL, $numResult);
    }
    
    /**
    * Подготовка отчета
    *
    */ 
    public static function stripTags($text)
    {
        $text = str_replace(['<br>', '<br />'], PHP_EOL, $text);
        return strip_tags($text);
    }    
    
}
 