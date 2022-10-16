<?php
namespace console;

use sql\migrations\Migrations;

/**
 * Class CliManager
 * @package console
 */
class CliManager
{
    
    protected $argc;
    protected $argv;
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
        ini_set('register_argc_argv', true);
    }

    /**
     * @return string
     */
    public function run()
    {
        $this->argc = $GLOBALS['argc'];
        $this->argv = $GLOBALS['argv'];
        
        if($this->argc <= 1){
            return 'Please enter command.';
        }
        
        $rout = explode('/', $this->argv[1]);
        $command = !empty($rout[0]) ? $rout[0] : null;
        
        switch ($command) {
            case 'migrate' :
                return $this->migration($this->config);
            default :
                return sprintf("Command %s not support.", $command);
        }
    }

    /**
     * @param $config
     * @return string
     */
    protected function migration($config)
    {
        $migrate = new Migrations($config);
        $migrate->route($this->argv);
        return $migrate->getReport();
    }
}
