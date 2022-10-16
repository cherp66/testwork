<?php
namespace app\controllers\api\v1;

use application\Controller;
use app\actions\Assembler;

/**
 * Class LoginController
 * @package app\controllers\api\v1
 */
class LoginController extends Controller
{
    /**
     *
     */
    public function run()
    {
        (new Assembler($this))->run();
    }
}
