<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/7/15
 * Time: 11:49
 */

namespace Flight2wwu\Component\Config;


use Symfony\Component\Config\Loader\FileLoader;

class JsonLoader extends FileLoader
{
    /**
     * Loads a resource.
     *
     * @param mixed $resource The resource
     * @param string|null $type The resource type or null if unknown
     *
     * @throws \Exception If something went wrong
     */
    public function load($resource, $type = null)
    {
        $conf = json_decode(file_get_contents($resource), true);
        return $conf;
    }

    /**
     * Returns whether this class supports the given resource.
     *
     * @param mixed $resource A resource
     * @param string|null $type The resource type or null if unknown
     *
     * @return bool True if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'json' === pathinfo($resource, PATHINFO_EXTENSION);
    }


}