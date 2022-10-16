<?php

namespace http\language;

/**
 * Class En
 * @package http
 */
class En
{

    public static function set()
    {
        if(!defined("HTTP")){

            define("HTTP", true);

            define('HTTP_INVALID_STREAM',           'Invalid stream provided.');
            define('HTTP_INVALID_PROTOCOL',         'Invalid HTTP version. ');
            define('HTTP_INVALID_TARGET',           'Invalid request target provided; cannot contain whitespace ');
            define('HTTP_INVALID_BODY',             'Request body media type parser return value must be an array, an object, or null');
            define("HTTP_NO_METHOD",                'Method %s not implemented in HTTP system');
            define('HTTP_NO_HEADER',                ' - There is no such header. ');
            define('HTTP_VALUE_NO_STRING',          'Header must be a string or array of strings ');
            define('HTTP_INVALID_HEADER_NAME',      'Invalid header name. ');
            define('HTTP_INVALID_HEADER_VALUE',     'Invalid header. ');
            define('HTTP_NO_RESOURCE',              ' is not a resource. ');
            define('HTTP_NO_REWIND',                'Could not rewind stream ');
            define('HTTP_NO_POINTER',               'Could not get the position of the pointer in stream ');
            define('HTTP_NO_WRITE',                 'Could not write to stream ');
            define('HTTP_NO_READ',                  'Could not read from stream ');
            define('HTTP_NO_CONTENT',               'Could not get contents of stream ');
            define('HTTP_PATH_NO_STRING',           'Path must be a string ');
            define('HTTP_URI_NO_STRING',            'Uri must be a string ');
            define('HTTP_INVALID_URI',              'The invalid Uri ');
            define('HTTP_SCHEME_NO_STRING',         'Uri scheme must be a string ');
            define('HTTP_INVALID_SCHEME',           'Uri scheme must be one of: "", "https", "http" ');
            define('HTTP_EMPTY_ARGYMENTS',          'Uri fragment must be a string ');
            define('HTTP_EMPTY_FILE_PATH',          'No path is specified for moving the file ');
            define('HTTP_CANNOT_MOVE_FILE',         'Cannot move file ');
            define('HTTP_ERROR_MOVED',              'Cannot retrieve stream after it has already been moved ');
            define('HTTP_ERROR_FILE',               'Error occurred while moving uploaded file ');
            define('HTTP_URI_IS_FRAGMENT',          'Query string must not include a URI fragment ');
            define('HTTP_INVALID_STATUS',          'Invalid status code. Must be an integer between 100 and 599, inclusive ');
        }
    }
}
















