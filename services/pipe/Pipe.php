<?php
namespace pipe;

/**
 * Class Pipe
 * @package pipe
 */
class Pipe implements StateInterface
{
    protected $locator;
    protected $stack;
    protected $request;
    protected $response;
    protected $stateMashine;

    public function __construct() {
        $this->stack = new \SplStack;
        $this->stateMashine = new StateMashine($this->stack);
    }

    public function setLocator($locator) {
        $this->locator = $locator;
    }

    public function setRequest($request) {
        $this->request  = $request;

    }

    public function setResponse($response ) {
        $this->response  = $response;
    }

    /**
    * Добавляет миддлвар в очередь.
    *
    * @param string|array $stack
    *
    * $return object
    */ 
    public function add($stack)
    {
        foreach (is_array($stack) ? $stack : [$stack] as $middlware) {
            $this->stack->push($middlware);        
        }
        return $this;
    }
    
    /**
    * Проверяет стек на пустоту.
    * 
    * $return bool
    */
    public function isEmpty()
    {        
        $stack = clone $this->stack;
        $stack->rewind();
        return !$stack->valid();
    }
    
    /**
    * Запускает очередь.
    *
    * $return object
    */
    public function run()
    {
        if ($this->isEmpty()) {
            throw new PipeException('Queue is empty');
        }
        $this->stack->rewind();
        $this->stack->unshift(function($request, $response){ return $this->response;}); 
 
        $handler = $this->execute();
        return $handler->process([$this->request]);
    }

    /**
    * Рекурсивный обход очереди.
    *
    * @param ResponseInterface $response
    *
    * $return object
    */
    protected function execute()
    {
        return new CallableHandler(function ($request) {
            if(!$this->stack->valid()) {
                throw new PipeException('The position for the state machine is incorrect');
            }
            $middleware = $this->create($this->stack->current());
            if(method_exists($middleware, 'handle')) {
                $this->stack->rewind();
                return $middleware->handle($request);
            }
            $this->stateMashine->run($middleware, $request);
            return $this->stateMashine->process($this->execute());
        });
    }

    /**
     * @param $middleware
     * @return object
     */
    protected function create($name)
    {
        if (!empty($this->locator) && $this->locator->has($name)) {
            return $this->locator->get($name);
        } elseif (is_string($name) && class_exists($name)) {
            return new $name;
        }
        throw new PipeException(sprintf('Middleware %s not found', $name));
    }
}


