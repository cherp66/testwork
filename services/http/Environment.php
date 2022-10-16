<?php

namespace http;

/**
 * Class Environment
 * @package http
 */
class Environment
{
    public static function get()
    {
        return [
                'SERVER_PROTOCOL'      => 'HTTP/1.1',
                'REQUEST_METHOD'       => 'GET',
                'SCRIPT_NAME'          => '',
                'REQUEST_URI'          => '',
                'QUERY_STRING'         => '',
                'SERVER_NAME'          => 'localhost',
                'SERVER_PORT'          => 80,
                'HTTP_HOST'            => 'localhost',
                'HTTP_ACCEPT'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'HTTP_ACCEPT_LANGUAGE' => 'ru-ru,ru;q=0.8,en-us;q=0.6,en;q=0.4',
                'HTTP_ACCEPT_CHARSET'  => 'ISO-8859-1,utf-8;q=0.7,*;q=0.3',
                'HTTP_USER_AGENT'      => 'ABC Framework',
                'REMOTE_ADDR'          => '127.0.0.1',
                'REQUEST_TIME'         => time(),
                'REQUEST_TIME_FLOAT'   => microtime(true),
                ];
    }
}