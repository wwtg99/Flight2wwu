<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/11/23
 * Time: 15:50
 */

namespace Wwtg99\Flight2wwu\Common;


class Response
{

    /**
     * @var Response
     */
    private static $instance = null;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var int
     */
    protected $jsonOptions = JSON_UNESCAPED_UNICODE;

    /**
     * @var string
     */
    protected $jsonpCallback = '';

    /**
     * @var string
     */
    protected $view = '';

    /**
     * @var string
     */
    protected $resType = 'string';

    /**
     * @var int
     */
    protected $resCode = 200;

    /**
     * @var array
     */
    protected $header = ['Cache-Control: no-cache', 'Pragma: no-cache'];

    /**
     * Response constructor.
     */
    private function __construct()
    {
    }

    /**
     * @return Response
     */
    public static function get()
    {
        if (!self::$instance) {
            self::$instance = new Response();
        }
        return self::$instance;
    }

    /**
     * Send header
     */
    public function sendHeader()
    {
        if ($this->header) {
            foreach ($this->header as $head) {
                header($head);
            }
        }
    }

    /**
     * @return bool
     */
    public function send()
    {
        $this->sendHeader();
        switch ($this->resType) {
            case 'json': \Flight::json($this->data, $this->resCode, true, 'utf8', $this->jsonOptions); return false;
            case 'jsonp': \Flight::jsonp($this->data, $this->jsonpCallback, $this->resCode, true, 'utf8', $this->jsonOptions); return false;
            case 'view': getView()->render($this->view, $this->data); return false;
            case 'filter': return true;
        }
        return false;
    }

    /**
     * @param $json
     * @param $decode
     * @return Response
     */
    public function setData($json, $decode = false)
    {
        if ($decode) {
            $this->data = json_decode($json, true);
        } else {
            $this->data = $json;
        }
        return $this;
    }

    /**
     * @param $key
     * @param $data
     */
    public function addData($key, $data)
    {
        $this->data[$key] = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getResType()
    {
        return $this->resType;
    }

    /**
     * Return type: string, html, json, jsonp, filter, view
     *
     * @param string $resType
     * @return Response
     */
    public function setResType($resType)
    {
        $this->resType = $resType;
        return $this;
    }

    /**
     * @return int
     */
    public function getResCode()
    {
        return $this->resCode;
    }

    /**
     * @param int $resCode
     * @return Response
     */
    public function setResCode($resCode)
    {
        $this->resCode = $resCode;
        return $this;
    }

    /**
     * @return int
     */
    public function getJsonOptions()
    {
        return $this->jsonOptions;
    }

    /**
     * @param int $jsonOptions
     * @return Response
     */
    public function setJsonOptions($jsonOptions)
    {
        $this->jsonOptions = $jsonOptions;
        return $this;
    }

    /**
     * @return string
     */
    public function getJsonpCallback()
    {
        return $this->jsonpCallback;
    }

    /**
     * @param string $jsonpCallback
     * @return Response
     */
    public function setJsonpCallback($jsonpCallback)
    {
        $this->jsonpCallback = $jsonpCallback;
        return $this;
    }

    /**
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param string $view
     * @return Response
     */
    public function setView($view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * @return array
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param array $header
     * @return Response
     */
    public function setHeader($header)
    {
        $this->header = $header;
        return $this;
    }


}