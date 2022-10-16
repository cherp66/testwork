<?php
namespace application;

use http\Http;


/**
 * Class RouteManager
 * @package application
 */
class RouteManager
{
    protected $container;
    protected $storage;
    protected $request;
    protected $method;
    protected $group;
    protected $sections;

    protected static $validMethods = [
        'GET'     => true,
        'POST'    => true,
        'PUT'     => true,
        'DELETE'  => true,
        'OPTIONS' => true,
    ];

    public function __construct($container)
    {
        $this->container = $container;
        $this->storage = Http::getStorage();
        $this->request = Http::getRequest();
        $this->method = $this->request->getMethod();
        if (!in_array($this->method, self::$validMethods)) {
            $this->notFound();
        }
    }

    /**
     * @param $pattern
     * @param $callable
     */
    public function get($pattern, $callable)
    {
        $this->resolver($pattern, $callable, 'GET');
    }

    /**
     * @param $pattern
     * @param $callable
     */
    public function post($pattern, $callable)
    {
        $this->resolver($pattern, $callable, 'POST');
    }

    /**
     * @param $pattern
     * @param $callable
     */
    public function put($pattern, $callable)
    {
        $this->resolver($pattern, $callable, 'PUT');
    }

    /**
     * @param $pattern
     * @param $callable
     */
    public function delete($pattern, $callable)
    {
        $this->resolver($pattern, $callable, 'DELETE');
    }

    /**
     * @param $pattern
     * @param $callable
     */
    public function options($pattern, $callable)
    {
        $this->resolver($pattern, $callable, 'OPTIONS');
    }

    /**
     * @param $pattern
     * @param $callable
     * @return false|mixed
     */
    public function group($pattern, $callable)
    {
        $this->group = $pattern;
        $path = $this->getPath();
        $pattern .= '/.*';
        if ($this->checkRule($pattern, $path)) {
            return call_user_func($callable);
        }
        return false;
    }

    /**
     *
     */
    public function notFound()
    {
        $sender = $this->container->get('Sender');
        $sender->notFound();
    }

    /**
     * @param $pattern
     * @param $callable
     * @param $method
     * @return bool
     */
    protected function resolver($pattern, $callable, $method)
    {
        if ($this->method === $method) {
            $patterns = is_array($pattern) ? $pattern : [$pattern];
            $path = $this->getPath();
            foreach($patterns as $pattern) {
                $pattern = $this->group . $pattern;
                if ($this->checkRule($pattern, $path)) {
                    if (is_string($callable) && class_exists($callable)) {
                        $params = $this->createGetParams($path);
                        $request = $this->request->withQueryParams($params);
                        $this->container->add('Request', function() use ($request) {
                            return $request;
                        });
                        $controller = new $callable($this->container);
                        $parent = '\application\Controller';
                        if (!($controller instanceof $parent)) {
                            throw new \BadFunctionCallException(
                                $callable .' should extend from \router\Controller');
                        }
                        return $controller->run();
                    } elseif (is_callable($callable)) {
                        return call_user_func($callable);
                    }
                }
            }
        }
    }

    /**
     * Формирует GET параметры
     *
     * @param $path
     * @return array|false
     */
    protected function createGetParams($path)
    {
        $elements = explode('/', $path);
        $GET = [];
        foreach ($this->sections as $num => $section) {
            if (is_array($section)) {
                $GET[$section['name']] = $elements[$num];
            }
        }
        return $GET;
    }

    /**
     * Распознование подходящего правила
     *
     * @param $rule
     * @param $path
     * @return bool
     */
    protected function checkRule($rule, $path)
    {
        $rule = trim($rule, '/');
        $pattern = '';
        $this->sections = $this->preapareSections($rule);

        foreach ($this->sections as $section) {
            if (is_array($section)) {
                $pattern .= '('. $section['value'] .'?)/';
            } else {
                $pattern .= $section .'/';
            }
        }

        return (bool)preg_match('~^'. $pattern .'$~', $path);
    }


    /**
     * Подготовка шаблонов для RegExp
     *
     * @param $rule
     * @return array
     */
    protected function preapareSections($rule)
    {
        $rule = explode('/', $rule);
        $this->patterns = [];

        foreach ($rule as $section) {
            $section = str_replace(['{', '}'], ['<', '>'], $section);
            if (preg_match_all('~<([\w._-]+)?>~', $section, $out)) {
                $this->patterns[] = ['name' => $out[1][0], 'value' => '[^/]+'];
            } elseif (preg_match_all('~<([\w._-]+):?([^>]+)?>~', $section, $out)) {
                $this->patterns[] = ['name' => $out[1][0], 'value' => $out[2][0]];
            } else {
                $this->patterns[] = $section;
            }
        }

        return $this->patterns;
    }

    /**
     * @return string
     */
    protected function getPath()
    {
        return trim($this->storage->get('path'), '/') .'/';
    }
}