<?php
namespace application\api;


/**
 * Class EnterAction
 * @package app\api\modify
 */
class ExitAction extends BaseAction
{
    /**
     * @param $request
     * @param $handler
     * @return mixed
     */
    public function handle($request)
    {
        $answer = $request->getAttribute(self::ANSWER);
        $this->sender->asJson($this->success($answer));
        $this->sender->display();
    }

    public function process($request, $handler){}
}
