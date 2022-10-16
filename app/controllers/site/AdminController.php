<?php
namespace app\controllers\site;

use application\Controller;

/**
 * Class AdminController
 * @package app\controllers\site
 */
class AdminController extends Controller
{
    /**
     *
     */
    public function run()
    {
        $this->tpl->selectTpl('admin');
        $this->render();
    }
}

