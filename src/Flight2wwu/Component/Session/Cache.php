<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/12/29
 * Time: 16:05
 */

namespace Flight2wwu\Component\Session;

use Flight2wwu\Common\ServiceProvider;

class Cache implements ServiceProvider
{
    /**
     * Called after register.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Called after all class is registered.
     *
     * @return void
     */
    public function boot()
    {
        $cacheDir = TMP . 'cache';
        if (!file_exists($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }
        \FileSystemCache::$cacheDir = $cacheDir;
    }

    function __construct()
    {

    }

    /**
     * @param $name
     * @return \FileSystemCacheKey
     */
    public function generateKey($name)
    {
        $uid = getUser('user_id');
        $key = \FileSystemCache::generateCacheKey($name, $uid);
        return $key;
    }

    /**
     * @param \FileSystemCacheKey $key
     * @param $data
     * @param int $ttl
     * @return bool
     */
    public function store(\FileSystemCacheKey $key, $data, $ttl = null)
    {
        return \FileSystemCache::store($key, $data, $ttl);
    }

    /**
     * @param \FileSystemCacheKey $key
     * @param $newer_than
     * @return mixed
     */
    public function retrieve(\FileSystemCacheKey $key, $newer_than = null)
    {
        return \FileSystemCache::retrieve($key, $newer_than);
    }

    /**
     * @param \FileSystemCacheKey $key
     * @param callable $callback
     * @param bool $resetTtl
     * @return mixed
     */
    public function getAndModify(\FileSystemCacheKey $key, \Closure $callback, $resetTtl = false)
    {
        return \FileSystemCache::getAndModify($key, $callback, $resetTtl);
    }

    /**
     * @param \FileSystemCacheKey $key
     * @return bool
     */
    public function invalidate(\FileSystemCacheKey $key)
    {
        return \FileSystemCache::invalidate($key);
    }

    /**
     * @param $name
     * @param bool $recursice
     * @throws \Exception
     */
    public function invalidateGroup($name = null, $recursice = true)
    {
        \FileSystemCache::invalidateGroup($name, $recursice);
    }

} 