<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 22.02.2025
 * Time: 12:48
 */

namespace IdImage\Abstracts;


use IdImage\Support\Client;

abstract class ApiAbstract
{
    protected Client $client;

    /* @var \IdImage\Support\Client|null $query */
    protected $query = null;

    const METHOD_GET = 'get';
    const METHOD_POST = 'post';
    const METHOD_DELETE = 'delete';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    protected function response()
    {
        $Response = $this->send();
        if (!$Response->isOk()) {
            throw new Exception('Ошибка запроса: '.$Response->getMessage());
        }

        return $Response;
    }

    protected function get(string $url, $params = null)
    {
        return $this->_request(self::METHOD_GET, $url, $params);
    }

    protected function post(string $url, $params = null)
    {
        return $this->_request(self::METHOD_POST, $url, $params);
    }

    protected function del(string $url, $params = null)
    {
        return $this->_request(self::METHOD_DELETE, $url, $params);
    }


    public function send()
    {
        return $this->query->send();
    }

    protected function _request(string $method, string $uri, $data = null)
    {
        switch (strtolower($method)) {
            case 'get':
                $query = $this->client->get($uri, $data);
                break;
            case 'post':
                $query = $this->client->post($uri, $data);
                break;
            default:
                break;
        }
        $this->query = $query;

        return $this;
    }


}
