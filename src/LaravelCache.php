<?php namespace NZTim\Logger;

use Illuminate\Contracts\Cache\Repository;

class LaravelCache implements Cache
{
    protected $cache;

    public function __construct(Repository $cache)
    {
        $this->cache = $cache;
    }

    public function has(string $key): bool
    {
        return $this->cache->has($key);
    }

    public function put(string $key, $value, int $minutes): void
    {
        $this->cache->put($key, $value, now()->addMinutes($minutes));
    }
}
