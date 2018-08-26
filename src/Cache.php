<?php namespace NZTim\Logger;

interface Cache
{
    public function has(string $key): bool;
    public function put(string $key, $value, int $minutes): void;
}
