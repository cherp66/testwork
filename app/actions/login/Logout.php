<?php
namespace app\actions\login;

use application\api\BaseAction;

/**
 * Class Logout
 * @package app\actions\login
 */
class Logout extends BaseAction
{
    /**
     * @param $request
     * @param $handler
     */
    public function process($request, $handler)
    {
        $data = $this->body($request);
        if (empty($data->logout)) {
            return $handler($request);
        }

        $token = $request->getHeader('X-Token');
        $stmt = $this->db->prepare(
            "DELETE FROM `token` 
                WHERE `token` = ? 
                   OR `created_at` < NOW() - INTERVAL 1 DAY"
        );
        $stmt->execute([$token]);
        $this->withState(self::OUT);
        return $handler($request);
    }
}
