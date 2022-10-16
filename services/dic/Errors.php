<?php

namespace dic;

/**
 * Class Errors
 * @package dic
 */
class Errors
{

    const INVALID_SERVICE =           ' The service is set incorrectly';
    const ALREADY_SERVICE =           ' Service %s is already installed';
    const NOT_FOUND_SERVICE =         ' Service %s not found';

    const INVALID_DEPENDANCE =        ' The %s type cannot be used for a dependency in a service %s. Wrap the data in a closure.';
    const INVALID_INJECT =            ' Dependency injection into service %s is not possible. Service is not an object of a valid class';
    const INVALID_INSTANCE =          ' Service %s must be an instance of %s. Check disabled lazy loading';
    const INVALID_SERVICENAME =       ' %s cannot be used as service name. The key must be string';
    const INVALID_DATA =              'The service %s is set incorrectly. 
    The value must be a class name, object, or closure. Wrap the data in a closure or use single sintax';
    const NOT_FOUND_IN_LOCATOR =      ' Service %s not found in servicelocator %s';
    const SETTER_NOT_FOUND =          ' Setter for property %s not found in class %s ';
    const INVALID_CALL_ORDER =        ' Method %s can only be called after addAsLocal( or addAsShared(';
    const INVALID_CONSTRUCT =         ' The constructor is not implemented in %s, or it does not accept arguments';
    const LOCATOR_EXISTS =            ' Locator %s is already installed';
    const RESERVED_WORD =             ' You cannot use the %s reserved word for a service identifier';
    const SYNTHETIC_SERVICE =         ' Service  %s created synthetically. 
    Impossible to implement services according to the synthetic';
}
















