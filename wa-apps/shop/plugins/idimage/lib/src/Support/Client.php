<?php

namespace IdImage\Support;

use CURLFile;
use Exception;

/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 19.02.2025
 * Time: 23:48
 */
class Client
{
    /* @var null|array $data */
    private $data = null;

    /* @var string $method */
    private $url;

    private $headers = [];
    private $method = 'post';

    public function get(string $url, $params = null)
    {
        return $this->setMethod('get')->setData($params)->setUrl($url);
    }

    public function post(string $url, $params = null)
    {
        return $this->setMethod('post')
            ->setData($params)
            ->setUrl($url)
            ->setHeaders([
                'Content-Type: application/json',
            ]);
    }

    public function file(string $url, $imagePath, $data = null)
    {
        $size = @getimagesize($imagePath);
        if ($size[0] !== 224 || $size[1] !== 224) {
            throw new Exception('Неверный размер изображения, должно быть 224х224');
        }

        if ($size['mime'] !== 'image/jpeg') {
            throw new Exception('Неверный формат изображения, должно быть jpeg');
        }

        $data = $data ?? [];
        $data['image'] = new CURLFile($imagePath, 'image/jpeg', basename($imagePath));

        return $this
            ->setUrl($url)
            ->setHeaders([
                'Accept: application/json',
            ])
            ->setData($data);
    }

    public function toArray()
    {
        return [
            'data' => $this->getData(),
            'url' => $this->getUrl(),
            'headers' => $this->getHeaders(),
        ];
    }

    public function send()
    {
        $postData = $this->getData();

        $settings = wa()->getPlugin('idimage')->getSettings();

        if (empty($settings['api_url'])) {
            throw new Exception('empty api url');
        }
        $apiUrl = $settings['api_url'];
        if (empty($settings['token'])) {
            throw new Exception('empty token');
        }
        $token = $settings['token'];

        $url = $this->getUrl();
        $url = rtrim($apiUrl, '/').$url;

        $headers = $this->getHeaders();

        $headers = array_merge($headers, [
            'Authorization: Bearer '.$token,
        ]);

        $upload = false;
        foreach ($headers as $key => $value) {
            if ($value === 'Accept: application/json') {
                $upload = true;
            }
        }

        $method = $this->getMethod();

        if ($method === 'get') {
            if (!empty($postData)) {
                $url = $url.'?'.http_build_query($postData);
            }
            $postData = null;
        } else {
            if (!$upload) {
                $postData = json_encode($postData);
            }
        }

        // Инициализируем cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // Игнорируем проверку SSL сертификатов
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        switch ($method) {
            case 'post':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                break;
            case 'delete':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                if (!empty($postData)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                }
                break;
            default:
                break;
        }


        // Выполняем запрос
        $response = curl_exec($ch);

        // Проверяем на ошибки
        $error = null;
        if (curl_errno($ch)) {
            $error = 'Ошибка cURL: '.curl_error($ch);
        }

        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // Закрываем соединение
        curl_close($ch);

        return new \IdImage\Support\Response($status, $response, $error);
    }

    protected function setData($data = null)
    {
        if (is_array($data)) {
            $this->data = $data;
        }

        return $this;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;

        return $this;
    }

    public function getHeaders()
    {
        return $this->headers;
    }


    public function getData()
    {
        return $this->data;
    }

    public function getUrl()
    {
        return $this->url;
    }

    protected function setUrl(string $url)
    {
        $this->url = '/'.ltrim($url, '/');

        return $this;
    }

    private function setMethod(string $method)
    {
        $this->method = $method;

        return $this;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function sendException()
    {
        $Response = $this->send();
        if (!$Response->isOk()) {
            throw $Response->exception();
        }

        return $Response;
    }


}
