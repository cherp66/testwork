<?php

namespace dic;

use dic\exceptions\ContainerException;
use dic\exceptions\NotFoundException;
use dic\interfaces\LocatorInterface;

/**
 * Trait CallableTrait
 * @package dic
 */
trait CallableTrait 
{
    /**
    *
    * @param string $serviceId
    * @param string|callable $source
    * @param bool $shared
    *
    * @return void
    */      
    public function bind($callable, $newthis)
    {
        return \Closure::bind($callable, $newthis);
    } 

    /**
    *
    * @param obj $space
    * @param callable $source
    * @param callable $callable
    *
    * @return void
    */ 
    protected function attachFactory($space, callable $source, callable $callable)
    {
        if ($space->repository->factories->contains($source)) {
            $space->repository->factories->offsetUnset($source);
            $space->repository->factories->attach($callable);
        }
    }       
 
    /**
    *
    * @param string $class
    *
    * @return callable
    */ 
    protected function createClassCallable($class)
    {
        $callable = function() use ($class) {
            return new $class;
        };
        $container = $this->container ? $this : null;
        $this->bind($callable, $container);
        return $callable;
    } 

    /**
    *
    * @param string $class
    *
    * @return callable
    */ 
    protected function createDataCallable($data)
    {
        return function() use ($data) {
            return $data;
        };
    }
    
    /**
    *
    * @param string $class
    *
    * @return callable
    */ 
    protected function createServiceLocator($locatorId, array $services, $container)
    {
        return function () use ($locatorId, $services, $container) {
            return new Locator($locatorId, $services, $container);
        };
    } 
}

class Locator implements LocatorInterface
{
    private $locatorServices = [];
    private $locatorId;
    private $container;

    public function __construct($locatorId, $services, $container)
    {
        $this->locatorId = $locatorId;
        $this->locatorServices = $services;
        unset($this->locatorServices[$container->getDefaultName()]);
        $this->container = $container;
    }

    /**
     * @param $implementation
     * @param null $serviceId
     * @return mixed
     * @throws ContainerException
     */
    public function get($implementation, $serviceId = null)
    {
        return $this->instaceOf($serviceId, $implementation);
    }

    /**
     * @param $implementation
     * @param null $serviceId
     * @return mixed
     * @throws ContainerException
     */
    public function getNew($implementation, $serviceId = null)
    {
        return $this->instaceOf($serviceId, $implementation, true);
    }

    /**
     * @param $serviceId
     * @return bool
     */
    public function has($serviceId)
    {
        return in_array($serviceId, $this->locatorServices);
    }

    /**
     * @param $serviceId
     * @param $implementation
     * @param false $new
     * @return mixed
     * @throws ContainerException
     * @throws NotFoundException
     */
    protected function instaceOf($serviceId = null, $implementation = null, $new = false)
    {
        $id = !empty($serviceId) ? $serviceId : $implementation;
        $this->checkHas($id);
        $service = $new ? $this->container->getNew($id) : $this->container->get($id);

        if(!empty($serviceId) && !$service instanceof $implementation) {
            throw new ContainerException(
                sprintf(Errors::INVALID_INSTANCE, $serviceId, $implementation, $serviceId, $implementation));
        }
        return $service;
    }

    /**
     * @param $serviceId
     * @throws NotFoundException
     */
    public function checkHas($serviceId)
    {
        if(!in_array($serviceId, $this->locatorServices)) {
            throw new NotFoundException(
                sprintf(Errors::NOT_FOUND_IN_LOCATOR, $serviceId, $this->locatorId, $serviceId, $this->locatorId));
        }
    }
}