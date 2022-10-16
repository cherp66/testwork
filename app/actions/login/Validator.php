<?php
namespace app\actions\login;

use application\api\BaseAction;
use exceptions\UserErrorException;

/**
 * Class Validator
 * @package app\actions\login
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
        if (empty($body->login) || empty($body->password)) {
            throw new UserErrorException('Поля не заполнены');
        }
        return $handler($request);
    }
}
