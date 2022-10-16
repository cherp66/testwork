<?php

namespace http;

/**
 * Class RequestAddition
 * @package http
 */
abstract class RequestAddition
{
    protected $jsonParams;
    protected $externalStatus;

    /**
    * Возвращает параметр server ($_SERVER) по ключу.
    *
    * @param string $key 
    * @param string|array $default 
    *
    * @return string|array
    */
    public function getServerParam($key, $default = null)
    {
        $serverParams = $this->getServerParams();
        return isset($serverParams[$key]) ? $serverParams[$key] : $default;
    }
    
    /**
    * Возвращает параметр query string ($_GET) по ключу.
    *
    * @param string $key 
    * @param string|array $default 
    *
    * @return string|array
    */
    public function getQueryParam($key, $default = null)
    {
        $getParams = $this->getQueryParams();
        return isset($getParams[$key]) ? $getParams[$key] : $default;
    }

    /**
     * Проверка внешнего статуса
     *
     * @param $status
     * @return bool
     */
    public function checkStatus($status)
    {
        return $this->externalStatus === $status;
    }

    /**
    * Возвращает параметр cookie ($_COOKIE) по ключу.
    *
    * @param string $key 
    * @param string|array $default 
    *
    * @return string|array
    */
    public function getCookieParam($key, $default = null)
    {
        $cookieParams = $this->getCookieParams();
        return isset($cookieParams[$key]) ? $cookieParams[$key] : $default;
    }

    /**
     * Атрибуты массивом
     *
     * @param $status
     * @return RequestAddition
     */
    public function withAttributes($attributes)
    {
        $clone = clone $this;
        $clone->attributes = $attributes;
        return $clone;
    }

    /**
     * Сохраняет внешний статус
     *
     * @param $status
     * @return RequestAddition
     */
    public function withStatus($status)
    {
        $clone = clone $this;
        $clone->externalStatus = $status;
        return $clone;
    }

    public function getJsonParams()
    {
        if (empty($this->jsonParams)) {
            $json = $this->getBody()->getContents();
            $result = json_decode($json);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->jsonParams = $result;
            } else {
                throw new \InvalidArgumentException('This is not json');
            }
           return $this->jsonParams;
        }
    }

    /**
     * @return bool
     */
    public function isAjax()
    {
        $requestedWith = $this->getHeader('X-Requested-With');
        return (!empty($requestedWith) && strtolower($requestedWith) === 'xmlhttprequest');
    }
}

