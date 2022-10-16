<?php
namespace application\api;

use pipe\StateInterface;

/**
 * Class BaseAction
 * @package services\application\api
 */
abstract class BaseAction implements StateInterface
{
    const ANSWER = 'answer';

    protected $db;
    protected $config;
    protected $sender;

    private $state = self::NEXT;

    private static $get;
    private static $body;

    /**
     * @param config\Main $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @param $db
     */
    public function setDB($db)
    {
        $this->db = $db;
    }

    /**
     * @param $sender
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @param $sender
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    /**
     * @param $sender
     */
    public function withState($state)
    {
        $this->state = $state;
    }

    /**
     * @return int|string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param null $key
     * @return mixed
     */
    public function get($key = null, $request = null)
    {
        if (empty(self::$get) && !empty($request)) {
            self::$get = $request->getQueryParams();
        }
        return !is_null($key) ? (isset(self::$get[$key]) ? self::$get[$key] : null) :self::$get;
    }

    /**
     * @return mixed
     */
    public function body($request = null)
    {
        if (empty(self::$body)) {
            self::$body = $request->getJsonParams();
        }
        return self::$body;
    }

    /**
     * @param $message
     * @return array
     */
    public function success($message)
    {
        return [
            'status'  => 'success',
            'message' => $message,
        ];
    }

    abstract public function process($request, $handler);
}