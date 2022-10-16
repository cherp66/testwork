<?php
namespace Pipe;

/**
 * Class CallableHandler
 * @package Pipe
 */
class CallableHandler
{
    protected $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * Выполнение invoke.
     *
     * @return object
     */
    public function __invoke()
    {
        return $this->process(func_get_args());
    }

    /**
    * Выполнение Tree PSR-15.
    *
    * @param object $request
    *
    * @return object
    */
    public function handle($request)
    {
        return $this->process($request);
    }

    /**
    * Выполнение Middleware PSR-15.
    *
    * @param mix $arguments
    *
    * @return object
    */
    public function process($arguments)
    {
        $arguments = is_array($arguments) ? $arguments : [$arguments];
        return call_user_func_array($this->callable, $arguments);
    }
}
