<?php

namespace dic\exceptions;

/**
 * Class NotFoundException
 * @package dic\exceptions
 */
class NotFoundException extends \Exception
{
    public function __construct($message) 
    {       
        parent::__construct($message);
    }
}  
