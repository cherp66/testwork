<?php
namespace application;

use config\Main;
use exceptions\UserErrorException;
use pipe\Pipe;
use application\api\EnterAction;
use application\api\ExitAction;

/**
 * Class ApiRoot
 * @package application
 */
abstract class ApiRoot
{
    protected $container;
    protected $controller;

    public function __construct($controller)
    {
        $controllerName = get_class($controller);
        $part = explode('\\', $controllerName);
        $this->controller = end($part);
        $this->container = $controller->getContainer();
        $this->container->add('Params', Main::class);
        $this->container->add([
            'EnterAction' => [
                EnterAction::class,
                'sender' => 'Sender'
            ]
        ]);
        $this->container->add([
            'ExitAction' => [
                ExitAction::class,
                'sender' => 'Sender'
            ]
        ]);
        $this->compile();
        $this->addActions();
        $this->container->add([
            'Pipe' => [
                Pipe::class,
                'locator'  => 'Actions',
                'request'  => 'Request',
                'response' => 'Response'
            ]
        ]);
    }

    /**
     * Запуск очереди
     */
    public function run()
    {
        $sender = $this->container->get('Sender');
        try {
        $pipe = $this->container->get('Pipe');
            $pipe->add(array_reverse($this->queue));
            $sender->display($pipe->run());
        } catch (UserErrorException $e) {
            $answer = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
            $code = !empty($e->getCode()) ? $e->getCode() : 200;
            $sender->asJson($answer, $e->getCode());
            return $sender->display();
        }
    }

    /**
     * Сборка конкретного приложения
     */
    abstract public function compile();
    abstract public function addActions();
}