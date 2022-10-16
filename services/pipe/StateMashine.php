<?php
namespace pipe;

/**
 * Class StateMashine
 * @package pipe
 */
class StateMashine implements StateInterface
{
    protected $stack;
    protected $request;

    public function __construct($stack) {
        $this->stack = $stack;
    }

    /**
     * @param $middleware
     * @param $request
     */
    public function run($middleware, $request)
    {
        $this->request = $middleware->process($request, function($request) {
            return $request;
        });
        $this->changeState($middleware->getState());
    }

    /**
     * @param $middleware
     * @param $request
     */
    public function process($handler)
    {
        $handler($this->request);
    }

    /**
     * @param $state
     */
    public function changeState($state)
    {
        $state = !is_null($state) ? $state : self::NEXT;
        switch($state) {
            case self::NEXT :
                $this->stack->next();
                break;
            case self::BEGIN :
                $this->stack->rewind();
                break;
            case self::OUT :
                $this->end();
                break;
            default :
                $this->to($state);
        }
    }

    /**
     * @param $goin
     */
    protected function to($state)
    {
        $this->stack->rewind();
        $this->skip($state);
    }

    /**
     * @param $step
     */
    protected function skip($step)
    {
        do {
            $this->stack->next();
        } while(--$step);
    }

    /**
     *
     */
    protected function end()
    {
        $this->stack->rewind();
        $cnt = $this->stack->count();
        $this->skip($cnt - 2);
    }
}
