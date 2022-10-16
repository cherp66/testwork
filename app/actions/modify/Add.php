<?php
namespace app\actions\modify;

use application\api\BaseAction;
use exceptions\UserErrorException;

/**
 * Class Add
 * @package app\actions\modify
 */
class Add extends BaseAction
{
    /**
     * @param $request
     * @param $handler
     */
    public function process($request, $handler)
    {
        $parent_id = !empty($this->get('add')) ? $this->get('add') : null;
        $body = $this->body();

        $data = [
            ':name' => $body->name,
            ':description' => $body->description,
        ];
        if (!is_null($parent_id)) {
            $data[':parent_id'] = $parent_id;
        }

        $stmt = $this->db->prepare(
            "INSERT INTO `tree` SET 
                `parent_id` = ". (is_null($parent_id) ? ' NULL, ' : ':parent_id,')."
                `name` = :name, 
                `description` = :description "
        );

        $stmt->execute($data);
        if ($stmt->rowCount() > 0) {
            $this->withState(self::OUT);
            $answer['id'] = $this->db->lastInsertId();
            $answer['root'] = $parent_id;
            return $handler($request->withAttribute(self::ANSWER, $answer));
        }
        throw new UserErrorException('He удалось добавить объект '. $body->name);
    }
}
