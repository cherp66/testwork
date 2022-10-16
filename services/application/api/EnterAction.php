<?php
namespace application\api;


/**
 * Class EnterAction
 * @package application\api
 */
class EnterAction extends BaseAction
{

    /**
     * @param $request
     * @param $handler
     * @return mixed
     */
    public function process($request, $handler)
    {
        $this->get(null, $request);
        $this->body($request);
        if (!$request->isAjax()) {
            return $this->sender->notFound();
        }
        return $handler($request);
    }
  
}
