<?php

namespace dic;

use dic\interfaces\LocatorInterface;

/**
 * Class LazyLoad
 * @package DIC
 */
class LazyLoad implements LocatorInterface
{
    protected $callable;
    protected $obj;

    /**
    * __construct
    */ 
    public function __construct($callable)
    {
        $this->callable = $callable;
    }
    
    /**
    * __toString
    */ 
    public function __toString()
    {
        return (string)$this->createObject();
    }  
    
    /**
    * __call
    */ 
    public function __call($method, $args)
    {
        return call_user_func_array([$this->createObject(), $method], $args);
    }  

    /**
    * __get
    */ 
    public function __get($name)
    { 
        return $this->createObject()->$name;
    }  
    
    /**
    * __set
    */ 
    public function __set($name, $value)
    {
        $obj = $this->createObject();
        $obj->$name = $value;
        return $obj;
    }  
    
    /**
    * __isset
    */ 
    public function __isset($name)
    {
        $obj = $this->createObject();
        return isset($obj->$name);
    } 
    
    /**
    * __unset
    */ 
    public function __unset($name)
    {
        $obj = $this->createObject();
        unset($obj->$name);
    } 
    
    /**
    * 
    */    
    protected function createObject()
    {
        if (empty($this->obj)) { 
            $this->obj = $this->callable->__invoke();
        }
         
        return $this->obj;
    }    
}