<?php
namespace app\actions\modify;

use application\api\BaseAction;
use exceptions\UserErrorException;

/**
 * Class Validator
 * @package app\actions\modify
 */
class Validator extends BaseAction
{
    /**
     * @param $request
     * @param $handler
     * @throws UserErrorException
     */
    public function process($request, $handler)
    {
        $body = $this->body();
        $get = $this->get();

        switch($body->state) {
            case 3:
            case 4;
                if (empty($body->name)) {
                    throw new UserErrorException('Название не задано');
                }
                break;
            case 5:
                if ($body->parent_id !== 0 && empty($body->parent_id)) {
                    throw new UserErrorException('ID ветки не задан');
                }
                if ($body->parent_id === $get['jump']) {
                    throw new UserErrorException('Нельзя перемещать ветку саму в себя');
                }
                break;
        }
        $this->withState($body->state);
        return $handler($request);
    }
}
