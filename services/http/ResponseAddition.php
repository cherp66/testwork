<?php

namespace http;

/**
 * Class ResponseAddition
 * @package http
 */
abstract class ResponseAddition
{
    /**
     * Запись данных в тело ответа.
     *
     * @param string $data
     * 
     * @return $this
     */
    public function write($data)
    {
        $this->getBody()->write($data);
        return $this;
    }
}

