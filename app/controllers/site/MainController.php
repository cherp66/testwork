<?php
namespace app\controllers\site;

use application\Controller;

/**
 * Class MainController
 * @package app\controllers\site
 */
class MainController extends Controller
{
    public function run()
    {
        $this->tpl->selectTpl('main');
        $this->render();
    }
}