<?php 
namespace tpl;

/**
 * Class Tpl
 * @package tpl
 */
class Tpl extends Processor
{  
    protected $functions   = ['createUri', 'createLink', 'activeLink'];

    /**
     * @param array|null $config
     */
    public function setConfig(array $config = null)
    {
        $language = '\tpl\language\\';
        $language .= !empty($config['language']) ? $config['language'] : 'En';
        $language::set();
        $this->tplDir = str_replace('\\', DIRECTORY_SEPARATOR, $config['dir']);
        $this->layout = !empty($config['layout']) ? $config['layout'] : 'layout';
        $this->tplExt = !empty($config['ext']) ? $config['ext'] : 'tpl';
        $this->tplPhp = !empty($config['php']);
        $functions = !empty($config['functions']) ? $config['functions'] : [];
        $this->functions = array_merge($this->functions, $functions);
    }
} 
