<?php
namespace sql\db\sqldebug;

/**
 * Class View
 * @package sql\db\sqldebug
 */
class View
{
    /**
    * It marks a problem area SQL
    *
    * @param string $sql
    * @param string $location
    *
    * @return string
    */    

    public function highlightLocation($sql, $location)
    {
        return str_replace($location, '<b style="color:red">'. $location .'</b>', $sql);
    }

    /**
    * Generates listing SQL
    *
    * @param array $data
    *
    * @return void
    */    
    public function createReport($data, $html = true)
    {
        if(!$html){
            exit($data['error']);
        }
        $data['num'] = implode('<br>', $data['num']);
        $tpl = $this->getTpl(__DIR__ .'/tpl/report_sql.tpl');
        $this->display($this->parseTpl($tpl, $data));
    }    
    
    /**
    * Generates EXPLAIN
    *
    * @param array $data
    *
    * @return string
    */     
    public function createExplain($data)
    { 
        $tpl = $this->getTpl(__DIR__ .'/tpl/explain.tpl');
        return $this->parseTpl($tpl, $data);
    }
    
    /**
    * Highlighting the line
    *
    * @param string $line
    *
    * @return string
    */  
    public function wrapLine($line)
    {
        return '<span class="error_line">'. $line .'</span>';
    } 
    
    /**
    * Highlighting code PHP
    *
    * @param string $php
    * @param int $position
    * @param int $size
    *
    * @return string
    */    
    public function highlightString($php, $position, $size)
    {
        $descr = preg_match('~^[\r\n\s\t]*?<\?php~uis', $php) ? '' : '<?php ';
        $php   = highlight_string($descr . $php, true);
        $lines = preg_split('~<br[\s/]*?>~ui', $php);       
        $lines = array_slice($lines, $position, $size);
        return implode('<br />', $lines);
    } 
    
    /**
    * Returns generated listing php code
    *
    * @param array $data
    *
    * @return string
    */    
    public function createPhp($data)
    {
        $data['num'] = implode('<br>', $data['num']);
        $tpl = $this->getTpl(__DIR__ .'/tpl/php.tpl');
        return $this->parseTpl($tpl, $data);
    } 
    
    /**
    * Gets a blank template
    *
    * @param string $file
    *
    * @return string
    */       
    public function getTpl($file)
    {
        return file_get_contents($file);
    }
    
    /**
    * Fills the pattern data and returns it
    *
    * @param string $tpl
    * @param array $data
    *
    * @return string
    */     
    public function parseTpl($tpl, $data)
    {
        $tpl = preg_replace('#\{\$(.+?)\}#i', '<?=$\\1;?>', $tpl);
        extract($data);
        ob_start();
        include_once __DIR__ .'/tpl/assets.inc'; 
        eval('?>'. $tpl);
        return ob_get_clean();
    }
    
    /**
    * Sends the result to the stream
    *
    * @param string $html
    *
    * @return void
    */     
    public function display($html)
    {
        echo $html;
    }    
}
