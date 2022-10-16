<?php
namespace app\actions\login;

use application\api\BaseAction;
use exceptions\UserErrorException;

/**
 * Class Login
 * @package app\actions\login
 */
class Login extends BaseAction
{
    /**
     * @param $request
     * @param $handler
     * @throws UserErrorException
     */
    public function process($request, $handler)
    {
        $body = $this->body();
        $stmt = $this->db->prepare(
            "SELECT `password` FROM `user`
                WHERE `login` = ?");
        $stmt->execute([$body->login]);
        $hash = $stmt->fetchColumn();
        if (!empty($hash)) {
            if (password_verify($body->password, $hash)) {
                $token = hash('sha256', microtime(true) . rand(1000, 100000));
                $stmt = $this->db->prepare(
                    "INSERT INTO `token` (`token`) 
                       VALUES (?)"
                );
                $stmt->execute([$token]);
                return $handler($request->withAttribute(self::ANSWER, ['token' => $token]));
            }
        }
        throw new UserErrorException('Учетная запись не найдена');
    }
}
