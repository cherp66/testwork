<?php
namespace app\controllers\api\v1;

use application\Controller;
use app\actions\Assembler;

/**
 * Class ObjectController
 * @package app\controllers\api\v1
 */
class ObjectController extends Controller
{
    /**
     *
     */
    public function run()
    {
        (new Assembler($this))->run();
    }
}
