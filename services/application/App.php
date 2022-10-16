<?php
namespace application;

use config\Main;
use dic\Container;
use http\Http;
use Exception;

/**
 * Class app
 * @package application
 */
class App
{
    public static function run($routes)
    {
        try {
            Router::init((new static())->initContainer());
            include_once $routes;
            Router::notFound();
        } catch (Exception $e) {
            //@Todo Create log
            throw $e;
        }

    }

    public function initContainer()
    {
        $params = new Main();
        $container = new Container();
        $this->setErrorHandlers($container);
        $container->add('Response', function() {
            return Http::getResponse();
        });
        $container->add([
            'Sender' => [
                Sender::class,
                'response' => 'Response'
            ]
        ]);
        $container->lazyLoad(true, ['DB', 'TPL']);
        $container->typeMethodCall(true);
        $container->add('DB', '\sql\db\pdo\PDO');
        $container->addDependences('DB', [
            'config' => function () use($params) {return $params->dbParams();}
        ]);
        $container->add('TPL', '\tpl\Tpl');
        $container->addDependences('TPL', [
            'config' => function () use($params) {return $params->tplParams();}
        ]);
        $this->addComponents($container);

        return $container;
    }

    /**
     *
     */
    protected function setErrorHandlers($container)
    {
/*
        set_error_handler(function($code, $message, $file, $line) {
            if (error_reporting() & $code) {
                $message .= ':'. $line .' in '. $file;
                throw new Exception(strip_tags($message), $code);
            }
        });
        register_shutdown_function(function() {
            if ($error = error_get_last() AND $error['type'] & E_ALL) {
                $message = $error['message'] .':'. $error['line'] .' in '. $error['file'];
                throw new Exception(strip_tags($message), 500);
            }
        });
        set_exception_handler(function($e) use ($container) {
            $sender = $container->get('Sender');
            $sender->answer('Ошибка сервера', 500);
        });
*/
    }

            /**
             * Сторонние библиотеки
             */
    private function addComponents($container)
    {
        // This include external components
    }
}