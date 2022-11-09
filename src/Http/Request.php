<?php

namespace Granal1\Php2\Http;

use Granal1\Php2\Blog\Exceptions\HttpException;
use JsonException;


class Request
{
    // аргумент, соответствующий суперглобальной переменной $_GET
    // аргумент, соответствующий суперглобальной переменной $_SERVER
    // Добавляем свойство для хранения тела запроса
    public function __construct(
        private array $get,
        private array $server,
        private string $body,
        ) {
    }

    // Метод для получения пути запроса
    // Напрмер, для http://example.com/some/page?x=1&y=acb
    // путём будет строка '/some/page'
    public function path(): string
    {
        // В суперглобальном массиве $_SERVER
        // значение URI хранится под ключом REQUEST_URI
        // Если мы не можем получить URI - бросаем исключение
        if (!array_key_exists('REQUEST_URI', $this->server)) {
            throw new HttpException('Cannot get path from the request');
        }

        // Используем встроенную в PHP функцию parse_url
        // Если мы не можем получить путь - бросаем исключение
        $components = parse_url($this->server['REQUEST_URI']);
        if (!is_array($components) || !array_key_exists('path', $components)) {
            throw new HttpException('Cannot get path from the request');
        }

        return $components['path'];
    }

    public function query(string $param): string
    {
        // Если нет такого параметра в запросе - бросаем исключение
        if (!array_key_exists($param, $this->get)) {
            throw new HttpException(
                "No such query param in the request: $param"
            );
        }

        // Если значение параметра пусто - бросаем исключение
        $value = trim($this->get[$param]);
        if (empty($value)) {
            throw new HttpException(
                "Empty query param in the request: $param"
            );
        }
        return $value;
    }

    // Метод для получения значения
    // определённого заголовка
    public function header(string $header): string
    {
        // В суперглобальном массиве $_SERVER
        // имена заголовков имеют префикс 'HTTP_',
        // а знаки подчёркивания заменены на минусы
        $headerName = mb_strtoupper("http_". str_replace('-', '_', $header));

        // Если нет такого заголовка - бросаем исключение
        if (!array_key_exists($headerName, $this->server)) {
            throw new HttpException("No such header in the request: $header");
        }

        $value = trim($this->server[$headerName]);

        // Если значение заголовка пусто - бросаем исключение
        if (empty($value)) {
            throw new HttpException("Empty header in the request: $header");
        }
        return $value;
    }

    // Метод для получения массива,
    // сформированного из json-форматированного
    // тела запроса
    public function jsonBody(): array
    {
        try {   

            // Пытаемся декодировать json
            // Декодируем в ассоциативный массив
            // Бросаем исключение при ошибке
            $data = json_decode(
                $this->body,
                associative: true,
                flags: JSON_THROW_ON_ERROR
            );
        } catch (JsonException) {
            throw new HttpException("Cannot decode json body");
        }

        if (!is_array($data)) {
            throw new HttpException("Not an array/object in json body");
        }

        return $data;
    }

    // Метод для получения отдельного поля
    // из json-форматированного тела запроса
    public function jsonBodyField(string $field): mixed
    {
        $data = $this->jsonBody();
        if (!array_key_exists($field, $data)) {
            throw new HttpException("No such field: $field");
        }
        if (empty($data[$field])) {
            throw new HttpException("Empty field: $field");
        }

        return $data[$field];
    }

    public function method(): string
    {
        // В суперглобальном массиве $_SERVER
        // HTTP-метод хранится под ключом REQUEST_METHOD
        // Если мы не можем получить метод - бросаем исключение
        if (!array_key_exists('REQUEST_METHOD', $this->server)) {
            throw new HttpException('Cannot get method from the request');
        }

        return $this->server['REQUEST_METHOD'];
    }
}