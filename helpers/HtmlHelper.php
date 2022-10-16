<?php
namespace helpers;

class HtmlHelper
{
     public static function htmlChars($data)
     {
         if (is_array($data)) {
             $data = array_map([__CLASS__, 'htmlChars'], $data);
         } elseif (is_string($data)) {
             $data = htmlspecialchars($data);
         }

         return $data;
     }
}