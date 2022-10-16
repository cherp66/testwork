<?php
namespace app\actions\tree;

use application\api\BaseAction;
use helpers\HtmlHelper;

/**
 * Class Single
 * @package app\api\tree
 */
class Single extends BaseAction
{
    /**
     * @param $request
     * @param $handler
     */
    public function process($request, $handler)
    {
        $id = $this->get('single');
        if (!empty($id)) {
            $stmt = $this->db->prepare(
                "SELECT `id`, `parent_id`, `name`, `description` FROM `tree`
                 WHERE `id` = ? "
            );
            $stmt->execute([$id]);
            $this->withState(self::OUT);
            $answer = HtmlHelper::htmlChars($stmt->fetch());
            return $handler($request->withAttribute(self::ANSWER, $answer));
        }
        return $handler($request);
    }
}
