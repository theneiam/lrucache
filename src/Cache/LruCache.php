<?php

/**
 * LRU (Least Recently Used) cache implementation
 *
 * @author Eugene Nezhuta <eugene.nezhuta@gmail.com>
 */

namespace Cache;

class LruCache
{
    const ENTRY_NOT_FOUND = -1;

    protected $cache = [];

    protected $capacity = 0;

    /**
     * Initialize cache with capacity
     *
     * @param int $capacity
     * @throws \InvalidArgumentException
     */
    public function __construct($capacity)
    {
        if (!is_integer($capacity) || $capacity <= 0) {
            throw \InvalidArgumentException('Capacity should be greater then 0');
        }
        $this->capacity = $capacity;
        $this->flush();
    }

    /**
     * Get value from cache by its key
     *
     * @param int|string $key
     * @return mixed|int Integer -1 returned if the key not found in cache
     */
    public function get($key)
    {
        if (isset($this->cache[$key])) {
            $this->updateKey($key);
            return $this->cache[$key];
        }
        return static::ENTRY_NOT_FOUND;
    }

    /**
     * Set value to cache
     *
     * @param int|string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        if (isset($this->cache[$key])) {
            $this->cache[$key] = $value;
            $this->updateKey($key);
        } else {
            $this->cache[$key] = $value;
            if (sizeof($this->cache) > $this->capacity) {
                reset($this->cache);
                unset($this->cache[key($this->cache)]);
            }
        }
    }

    /**
     * Remove value from cache by key
     *
     * @param int|string $key
     * @return mixed Return integer -1 if key is not found in cache
     */
    public function remove($key)
    {
        if (isset($this->cache[$key])) {
            $value = $this->cache[$key];
            unset($this->cache[$key]);
            return $value;
        }
        return static::ENTRY_NOT_FOUND;
    }

    /**
     * Clear entire cache
     */
    public function flush()
    {
        $this->cache = [];
    }

    /**
     * Mark entry as accessed by moving it to the end of an cache array
     *
     * @param $key
     */
    protected function updateKey($key)
    {
        $value = $this->cache[$key];
        unset($this->cache[$key]);
        $this->cache[$key] = $value;
    }
}

