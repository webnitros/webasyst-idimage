<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 22.02.2025
 * Time: 12:48
 */

namespace IdImage\Abstracts;


abstract class EntityAbsract
{
    protected $data = [];

    public function __construct($data = null)
    {
        if (is_array($data)) {
            $this->fromArray($data);
        }
    }

    public function isLocalUrl()
    {
        $picture = $this->getPicture();
        $parsedUrl = parse_url($picture);

        if (!$parsedUrl || !isset($parsedUrl['host'])) {
            return 'Not local address';
        }

        $host = strtolower($parsedUrl['host']);

        // Проверяем, является ли хост локальным
        if ($host === 'localhost' || $host === '127.0.0.1') {
            return 'Invalid host, cannot use localhost or 127.0.0.1. Current url: '.PHP_EOL.$picture;
        }

        return true;
    }

    public function get(string $key, $default = null)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return $default;
    }


    public function toArray()
    {
        return $this->data;
    }

    public function fromArray(array $array)
    {
        // all variables
        foreach ($array as $key => $value) {
            $this->data[$key] = $value;
        }

        return $this;
    }


}
