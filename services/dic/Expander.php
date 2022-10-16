<?php

namespace DIC;

/**
 * Class Expander
 * @package DIC
 */
class Expander
{
    
    protected $service;
    protected $parents = [];
    protected $dependences = [];
    protected $callables = [];

    /**
     * @param $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }

    /**
     * @param $parents
     */
    public function setParents($parents)
    {
        $this->parents = $parents;
    }

    /**
     * @param $dependences
     */
    public function setDependences($dependences)
    {
        $this->dependences = $dependences;
    }

    /**
     * @param $callables
     */
    public function setCallables($callables)
    {
        $this->callables = $callables;
    }

    /**
     * @return array[]
     */
    public function getDependences()
    {
        $ext = $dependences = [];
        foreach($this->parents as $parent){
            if(isset($this->dependences[$parent])){
                $dependences[] = $this->dependences[$parent];
            }
        }
   
        foreach($dependences as $dependence){
            $ext = array_merge($ext, $dependence);
        }
         
        return [$this->service => $ext];
    }

    /**
     * @return array
     */
    public function getCallables()
    {
        $callables = [];
        foreach($this->parents as $parent){
            if(isset($this->callables[$parent])){
                $callables[] = $this->callables[$parent];
            }
        }
        
        foreach($callables as $callable){
            $this->callables = array_merge($this->callables, $callable);
        }
     
        return [$this->service => $this->callables];
    }
}