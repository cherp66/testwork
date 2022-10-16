<?php
namespace app\actions;

use application\ApiRoot;

/**
 * Class Assembler
 * @package app\api
 */
class Assembler extends ApiRoot
{
    protected $queue;

    /**
     * Сборка приложения
     * @throws \DIC\Exceptions\ContainerException
     */
    public function compile()
    {
        $this->container->add('Assembler', $this);
    }

    /**
     * @throws \DIC\Exceptions\ContainerException
     */
    public function addActions()
    {
        $actions = $this->getActionsMap();
        $this->container->add($actions);
        $this->queue = array_keys($actions);
        array_shift($this->queue);
        array_unshift($this->queue, 'EnterAction');
        array_push($this->queue, 'ExitAction');
        $this->container->createLocator('Actions', $this->queue);
    }

    /**
     * Карта экшенов
     * @return array
     */
    private function getActionsMap()
    {
        $default[DIC_DEFAULT] = [
            'config' => 'Params',
            'DB'     => 'DB',
            'request' => 'Request',
        ];
        $actions = include_once __DIR__ . '/_map.php';
        return array_merge($default, $actions[$this->controller]);
    }
}