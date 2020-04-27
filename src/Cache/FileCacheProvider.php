<?php

namespace App\Cache;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class FileCacheProvider implements CacheInterface
{
    private $adapter;

    public function __construct()
    {
        $this->adapter = new FilesystemAdapter();
    }

    /**
     * Fetches a value from the cache.
     *
     * @param string $key     The unique key of this item in the cache.
     * @param mixed  $default Default value to return if the key does not exist.
     *
     * @return mixed The value of the item from the cache, or $default in case of cache miss.
     */
    public function get(string $key, $default = [])
    {
        $data = $this->adapter->getItem($key);
        if ($data->isHit()) {
            return $data->get();
        }
        return $default;
    }

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @param string                 $key   The key of the item to store.
     * @param mixed                  $value The value of the item to store. Must be serializable.
     * @param int|\DateInterval|null $ttl   Optional. The TTL value of this item. If no value is sent and
     *                                      the driver supports TTL then the library may set a default value
     *                                      for it or let the driver take care of that.
     */
    public function set(string $key, $value, $ttl = null)
    {
        $data = $this->get($key);
        if (empty($data)) {
            $data = $this->adapter->getItem($key);
            $res = $data->set($value);
            $data->expiresAfter($ttl);
            $this->adapter->save($data);
        }
    }
}
