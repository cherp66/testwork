<?php
namespace app\controllers\api\v1;

use application\Controller;
use app\actions\Assembler;

/**
 * Class ModifyController
 * @package app\controllers\api\v1
 */
class ModifyController extends Controller
{
    public function run()
    {
        (new Assembler($this))->run();
    }
}
