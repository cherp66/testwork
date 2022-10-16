<?php

namespace dic;

use dic\exceptions\ContainerException;

/**
 * Class Builder
 * @package dic
 */
class Builder  
{ 
    use CallableTrait;

    const INJECT_TYPES = [
        'injectViaConstruct',
        'injectViaMethods',
        'injectViaProperties'
    ];

 /** @var dic\Repository $repository */
    protected $repository;    
    
 /** @var dic\Container $container */
    protected $container;    
    
    protected $defaultType = 1;
    protected $typeConstruct = [];
    protected $typeMethodCall = []; 
    protected $typeProperties = []; 

    protected $dependences;

    public function __construct($container)
    {
        $this->container = $container;
    }
    
    /**
    * Устанавливает тип внедрения через конструктор
    *
    * @param string $services
    *
    * @return $this
    */  
    public function setRepository($repository)
    {
        $this->repository = $repository;
        $this->dependences = $this->repository->getDependences();
        return $this;
    }

    /**
    * Устанавливает тип внедрения через конструктор
    *
    * @param string $services
    *
    * @return $this
    */
    public function typeConstruct($services)
    {
        if(true === $services[0] && $this->defaultType !== 0){
            $this->defaultType = 0;
        } else {
            $services = is_array($services[0]) ? $services[0] : $services;
            $services = $this->container->clearServiceNames($services);
            $this->typeConstruct = array_merge($this->typeConstruct, $services);
        }

        return $this;
    }

    /**
    * Устанавливает тип внедрения через сеттеры
    *
    * @param string $services
    *
    * @return $this
    */  
    public function typeMethodCall($services)
    {
        if(true === $services[0] && $this->defaultType !== 1){
            $this->defaultType = 1;
        } else {
            $services = is_array($services[0]) ? $services[0] : $services;
            $services = $this->container->clearServiceNames($services);
            $this->typeMethodCall = array_merge($this->typeMethodCall, $services);        
        }
     
        return $this;
    } 
    
    /**
    * Устанавливает тип внедрения через свойства
    *
    * @param string $services
    *
    * @return $this
    */  
    public function typeProperties($services)
    {
        if(true === $services[0] && $this->defaultType !== 2){
            $this->defaultType = 2;
        } else {
            $services = is_array($services[0]) ? $services[0] : $services;
            $services = $this->container->clearServiceNames($services);
            $this->typeProperties = array_merge($this->typeProperties, $services);
        }    
        return $this;
    }
    
    /**
    * 
    * @param string $serviceId
    *
    * @return object
    */      
    public function run($serviceId)
    { 
        $callables = [];
        foreach($this->dependences[$serviceId] as $id => $dependence) {
            if(false === $dependence){
                continue;
            }
         
            switch(true){
                case $this->repository->issetCallable($serviceId, $id) :
                    $callables[$id] = $this->repository->getCallable($serviceId, $id);
                    break;
                    
                case is_string($dependence) :
                    $callables[$id] = $this->resolveFromString($dependence, $serviceId);
                    break;
                    
                case is_callable($dependence) :
                    $callables[$id] = $dependence;
                    break;
               
                default : 
                    throw new ContainerException(sprintf(
                            Errors::INVALID_DEPENDANCE,
                            gettype($dependence), 
                            $serviceId,
                            gettype($dependence), 
                            $serviceId
                        )
                    );
            };
        } 
        return  $this->inject($serviceId, $callables);
    }
    
    /**
    * 
    * @param string $serviceId
    *
    * @return object
    */      
    protected function resolveFromString($dependence, $serviceId)
    { 
        switch(true){
         
            case class_exists($dependence) :
                return $this->createClassCallable($dependence);
                
            case $this->repository->has($dependence) && !isset($this->dependences[$dependence]) :
                return $this->createDataCallable($this->container->getNew($dependence));
         
            case isset($this->dependences[$dependence]) :
                return $this->injectInternal($dependence);
                
            default :
                throw new ContainerException(sprintf(
                        Errors::INVALID_DEPENDANCE,
                        $dependence, 
                        $serviceId, 
                        $dependence, 
                        $serviceId
                    )
                );
        }
    }    
    
    /**
    *
    * @param string $serviceId
    * @param array $dependences   
    *
    * @return callable
    */ 
    protected function injectInternal($dependence)
    {  
        $call = [];
        foreach($this->repository->getDependence($dependence) as $key => $value){
         
            if(is_string($value) && class_exists($value)){
                $call[$key] = $this->createClassCallable($value);  
            } elseif(is_callable($value)) {
                $call[$key] = $value;
            } else {
                $call[$key] = $this->createDataCallable($this->container->getNew($value));            
            }
        } 
        return $this->inject($dependence, $call);     
    } 

    /**
    *
    * @param string $serviceId
    * @param array $dependences   
    *
    * @return callable
    */ 
    protected function inject($serviceId, array $dependences = [])
    { 
        $properties = $this->repository->getProperties($serviceId, $dependences);
      
        switch(true){   
            case in_array($serviceId, $this->typeConstruct) :
                return $this->injectViaConstruct($serviceId, $properties);
                
            case in_array($serviceId, $this->typeMethodCall) :
                return $this->injectViaMethods($serviceId, $properties);
                
            case in_array($serviceId, $this->typeProperties) :
                return $this->injectViaProperties($serviceId, $properties);
                
            default :
                return $this->{self::INJECT_TYPES[$this->defaultType]}($serviceId, $properties);
        }    
    } 
    
     /**
    *
    * @param string $serviceId
    * @param array $dependences   
    *
    * @return callable
    */ 
    protected function injectViaConstruct($serviceId, $properties)
    {
        $class = $this->getClass($serviceId);
        $properties = array_values($properties);
        return new $class(...$properties);
    }

    /**
    *
    * @param string $serviceId
    * @param array $dependences   
    *
    * @return callable
    */ 
    protected function injectViaMethods($serviceId, $properties)
    {  
        $object = $this->getValbdService($serviceId);
        foreach($properties as $name => $value){
            $setter = 'set'. $name;
            if(method_exists($object, $setter)){
                $object->{$setter}($value);
                continue;
            }
            $class = get_class($object);
            throw new ContainerException(sprintf(Errors::SETTER_NOT_FOUND, $name, $class, $name, $class));
        }
        
        return $object;
    }
   
    /**
    *
    * @param string $serviceId
    * @param array $dependences   
    *
    * @return callable
    */ 
    protected function injectViaProperties($serviceId, $properties)
    {  
        $object = $this->getValbdService($serviceId);     
        foreach($properties as $name => $value){
            if(property_exists($object, $name)){
                $object->$name = $value;
            }
        }
        
        return $object;
    }

    /**
    *
    * @param string $serviceId
    *
    * @return string
    */
    protected function getClass($serviceId)
    {
        throw new \Exception('Injection via constructor does not work in  PHP version below 7.4 ');

        try {
            $service = $this->repository->getServiceObject($serviceId);
        } catch(\Exception $e){
            $trace = $e->getTrace()[0];
            if($trace['function'] === '__construct'){
                $class = $trace['class'];
            }
        }

        if(empty($class)){
            throw new ContainerException(sprintf(Errors::INVALID_CONSTRUCT, get_class($service), get_class($service)));
        }

        return $class;
    }

     /**
    *
    * @param string $serviceId
    * @param array $dependences   
    *
    * @return callable
    */ 
    protected function getValbdService($serviceId)
    {  
        $object = $this->repository->getServiceObject($serviceId);  
        if(!is_object($object)){
            throw new ContainerException(sprintf(Errors::INVALID_INJECT, $serviceId, $serviceId));
        }
        
        return $object;
    }
}

