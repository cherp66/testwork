<?php
namespace application;

/**
 * Class Router
 * @package application
 */
class Router
{
    protected static $router;

    /**
     * @param $http
     * @param $pipe
     */
    public static function init($container)
    {
        self::$router = new RouteManager($container);
    }

    /**
     * @return mixed
     */
    public static function getRouter()
    {
        return self::$router;
    }

    /**
     * @param null $pattern
     * @param null $callable
     */
    public static function get($pattern = null, $callable = null)
    {
        self::$router->get($pattern, $callable);
    }

    /**
     * @param null $pattern
     * @param null $callable
     */
    public static function post($pattern = null, $callable = null)
    {
        self::$router->post($pattern, $callable);
    }

    /**
     * @param null $pattern
     * @param null $callable
     */
    public static function put($pattern = null, $callable = null)
    {
        self::$router->put($pattern, $callable);
    }

    /**
     * @param null $pattern
     * @param null $callable
     */
    public static function delete($pattern = null, $callable = null)
    {
        self::$router->delete($pattern, $callable);
    }

    /**
     * @param null $pattern
     * @param null $callable
     */
    public static function options($pattern = null, $callable = null)
    {
        self::$router->options($pattern, $callable);
    }

    /**
     * @param null $pattern
     * @param null $callable
     */
    public static function group($pattern = null, $callable = null)
    {
        self::$router->group($pattern, $callable);
    }

    /**
     *
     */
    public static function notFound()
    {
        self::$router->notFound();
    }

}