<?php
namespace app\actions\modify;

use application\api\BaseAction;
use exceptions\UserErrorException;
use helpers\HtmlHelper;

/**
 * Class Jump
 * @package app\actions\modify
 */
class Jump extends BaseAction
{
    /**
     * @param $request
     * @param $handler
     */
    public function process($request, $handler)
    {
        $id = $this->get('jump');
        $body = $this->body();
        $stmt = $this->db->prepare(
            "SELECT `id`, `name` FROM `tree` 
                 WHERE `id` = :id
                  OR  `id` = :parent_id"
        );
        $stmt->execute([
            ':id' => $id,
            ':parent_id' => $body->parent_id,
        ]);

        $items = [];
        while ($row = $stmt->fetch()) {
            $items[$row['id']] = HtmlHelper::htmlChars($row['name']);
        };

        if ($body->parent_id > 0 && count($items) !== 2) {
            throw new UserErrorException('Ветка для перемещения не найдена');
        }
        $data = [
            ':id' => $id
        ];
        if ($body->parent_id > 0) {
            $data[':parent_id'] = $body->parent_id;
            $parent = "`parent_id` = :parent_id";
        } else {
            $parent = "`parent_id` = NULL";
        }

        $stmt = $this->db->prepare(
            "UPDATE `tree` SET  
                $parent
                 WHERE `id` = :id"
        );
        $stmt->execute($data);
        if ($stmt->rowCount() > 0) {
            $this->withState(self::OUT);
            return $handler($request->withAttribute(self::ANSWER, $items));
        }
        throw new UserErrorException('Не удалось переместить ветку');
    }
}
