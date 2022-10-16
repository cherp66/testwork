<?php
namespace exceptions;


class UserErrorException extends \Exception
{
    public function __construct($message, $code = 200)
    {
        parent::__construct($message, $code);
    }
}