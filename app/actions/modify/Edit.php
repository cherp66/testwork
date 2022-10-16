<?php
namespace app\actions\modify;

use application\api\BaseAction;
use exceptions\UserErrorException;
use helpers\HtmlHelper;

/**
 * Class Edit
 * @package app\api\modify
 */
class Edit extends BaseAction
{
    /**
     * @param $request
     * @param $handler
     */
    public function process($request, $handler)
    {
        $id = $this->get('edit');
        $body = $this->body();
        $stmt = $this->db->prepare(
            "UPDATE `tree` SET 
                `name` = :name, 
                `description` = :description
                 WHERE `id` = :id"
        );
        $stmt->execute([
            ':id' => $id,
            ':name' => $body->name,
            ':description' => $body->description,
            ]);
        if ($stmt->rowCount() > 0) {
            $this->withState(self::OUT);
            $answer['id'] = $id;
            $answer['name'] = HtmlHelper::htmlChars($body->name);
            return $handler($request->withAttribute(self::ANSWER, $answer));
        }
        throw new UserErrorException('He удалось отредактировать объект '. $body->name);
    }
}
