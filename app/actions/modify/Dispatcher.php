<?php
namespace app\actions\modify;

use application\api\BaseAction;
use exceptions\UserErrorException;

/**
 * Class Dispatcher
 * @package app\actions\modify
 */
class Dispatcher extends BaseAction
{
    /**
     * @param $request
     * @param $handler
     */
    public function process($request, $handler)
    {
        $token = $request->getHeader('X-Token');
        if (empty($token)) {
            throw new UserErrorException('Токен не передан');
        }

        $stmt = $this->db->prepare(
            "SELECT `id` FROM `token` WHERE `token` = ?"
        );
        $stmt->execute([$token]);
        if (!$stmt->fetch()) {
            throw new UserErrorException('Не авторизован', 401);
        }

        $this->dispatch();
        return $handler($request);
    }

    /**
     *
     */
    public function dispatch()
    {
        $get = $this->get();
        switch (true) {
            case isset($get['add']) :
            case isset($get['edit']) :
            case isset($get['jump']) :
                return $this->withState(2);
            case isset($get['delete']) :
                return $this->withState(6);
            default :
                throw new UserErrorException('Модуль не найден', 404);
        }
    }
}
