<?php
namespace application;

/**
 * Class Controller
 * @package application
 */
abstract class Controller
{
    public $db;
    public $tpl;
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
        $this->init();
    }

    /**
     * @return Request
     */
    public function getContainer()
    {
        return $this->container;
    }

    protected $response;
    protected $sender;

    /**
     *
     */
    public function init()
    {
        $this->sender = $this->container->get('Sender');
        $this->db = $this->container->get('DB');
        $this->tpl = $this->container->get('TPL');
        $this->response = $this->container->get('Response');
    }

    /**
     * @param null $content
     */
    public function render($content = 'content')
    {
        if (!empty($content)) {
            $this->tpl->extendsTpl($content);
            $content = $this->tpl->getContent();
            $this->sender->answer($content);
        }
    }


    abstract public function run();
}