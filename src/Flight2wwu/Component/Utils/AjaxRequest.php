<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/9/18
 * Time: 17:21
 */

namespace Wwtg99\Flight2wwu\Component\Utils;


use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

class AjaxRequest
{

    /**
     * @var Client
     */
    protected $cli;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var array
     */
    protected $cookies = [];

    /**
     * @var string
     */
    protected $retype;

    /**
     * AjaxRequest constructor.
     *
     * @param array $config
     * @param $retype: return type: raw, string or json
     */
    public function __construct($config = [], $retype = 'raw')
    {
        $this->headers = ['X_REQUESTED_WITH'=>'XMLHttpRequest'];
        $this->retype = $retype;
        $this->cli = new Client($config);
    }

    /**
     * @param $method
     * @param string $uri
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function request($method, $uri = '', $options = [])
    {
        $options['headers'] = $this->headers;
        if ($this->cookies) {
            $options['cookies'] = new CookieJar(false, $this->cookies);
        }
        $res = $this->cli->request($method, $uri, $options);
        return $this->response($res);
    }

    /**
     * @param $uri
     * @param array $data
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function get($uri, $data = [], $options = [])
    {
        $options['headers'] = $this->headers;
        $options['query'] = $data;
        if ($this->cookies) {
            $options['cookies'] = new CookieJar(false, $this->cookies);
        }
        $res = $this->cli->get($uri, $options);
        return $this->response($res);
    }

    /**
     * @param $uri
     * @param array $data
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function post($uri, $data = [], $options = [])
    {
        $options['headers'] = $this->headers;
        $options['form_params'] = $data;
        if ($this->cookies) {
            $options['cookies'] = new CookieJar(false, $this->cookies);
        }
        $res = $this->cli->post($uri, $options);
        return $this->response($res);
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->cli;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * @return array
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * @param array $cookies
     */
    public function setCookies($cookies)
    {
        $this->cookies = $cookies;
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return string|\Psr\Http\Message\ResponseInterface
     */
    protected function response($response)
    {
        switch ($this->retype) {
            case 'string': $re = (string)$response->getBody(); break;
            case 'json': $re = \GuzzleHttp\json_decode((string)$response->getBody(), true); break;
            case 'raw':
            default: $re = $response;
        }
        return $re;
    }
}