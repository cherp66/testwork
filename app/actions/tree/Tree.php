<?php
namespace app\actions\tree;

use application\api\BaseAction;
use helpers\HtmlHelper;

/**
 * Class Tree
 * @package app\api\tree
 */
class Tree extends BaseAction
{
    /**
     * @param $request
     * @param $handler
     */
    public function process($request, $handler)
    {
        $id = $request->getQueryParam('id');
        $conditions = "a.`parent_id` IS NULL";
        if (!empty($id)) {
            $conditions = "a.`parent_id` = ?";
        }
        $stmt = $this->db->prepare(
            "SELECT a.*, COUNT(b.`id`) AS `root` FROM `tree` a
                LEFT JOIN `tree` b ON a.`id` = b.`parent_id`
                 WHERE ". $conditions ."
                 GROUP BY a.`id`"
        );
        $stmt->execute([$id]);
        $items = [];
        while ($row = $stmt->fetch()) {
            $items[] = HtmlHelper::htmlChars($row);
        };
        return $handler($request->withAttribute(self::ANSWER, $items));
    }
}
