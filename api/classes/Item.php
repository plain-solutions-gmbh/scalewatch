<?php

require_once(__DIR__ . '/../autoload.php');

class Item {

    
    public function __construct(
        private array $data = [],
        private int $index = 0
    ) {
    }

    public function __call(string $method, array $args)
    {
        
        if (method_exists($this, $method)) {
            return $this->{$method}(...$args);
        }

        $value = $this->get($method);
        return is_callable($value) ? $value(...$args) : $value;

    }

    public function sort() {

    }

    public function get(?string $key = null)
    {

        if ($key === null) {
            return $this->data;
        }

        if (\array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        throw new Exception("Item '{$key}' not found.");

    }

    public static function factory(...$params): self
    {
        return new static(...$params);
    }

    public function index() {
        return $this->index;
    }

    public function toArray()
    {
        return $this->data + ['index' => $this->index];
    }



}