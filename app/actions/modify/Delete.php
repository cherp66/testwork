<?php
namespace app\actions\modify;

use application\api\BaseAction;

/**
 * Class Delete
 * @package app\actions\modify
 */
class Delete extends BaseAction
{
    /**
     * @param $request
     * @param $handler
     */
    public function process($request, $handler)
    {
        $id = $this->get('delete');
        $stmt = $this->db->prepare(
            "DELETE FROM `tree` 
                WHERE `id` = ?"
        );
        $stmt->execute([$id]);
        if ($stmt->rowCount() > 0) {
            $request = $request->withAttribute(self::ANSWER, $id);
        }
        $this->withState(self::OUT);
        return $handler($request);
    }
}
