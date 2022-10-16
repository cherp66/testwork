<?php

namespace dic\exceptions;

 
class ContainerException extends \Exception
{
    public function __construct($message) 
    {      
        parent::__construct($message);
    }
}  
