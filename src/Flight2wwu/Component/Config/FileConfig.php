<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/7/15
 * Time: 12:28
 */

namespace Flight2wwu\Component\Config;


use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;

class FileConfig extends BaseConfig
{

    /**
     * @var string
     */
    protected $cacheFile = 'conf.cache';

    /**
     * FileConfig constructor.
     *
     * @param array|string $dirs
     * @param array|string $files
     * @param bool $useCache
     */
    public function __construct($dirs, $files, $useCache = true)
    {
        $cache = TMP . $this->cacheFile;
        if ($useCache && file_exists($cache)) {
            $conf = $this->loadCache();
            return parent::__construct($conf);
        }
        if (!is_array($dirs)) {
            $dirs = [$dirs];
        }
        $locator = new FileLocator($dirs);
        $jsloader = new JsonLoader($locator);
        $phploader = new PhpLoader($locator);
        $rev = new LoaderResolver([$jsloader, $phploader]);
        $dloader = new DelegatingLoader($rev);
        $c = $this->loadFile($files, $locator, $dloader);
        parent::__construct($c);
        if ($useCache) {
            $this->saveCache();
        }
    }

    /**
     * @param string|array $files
     * @param FileLocator $locator
     * @param DelegatingLoader $delegator
     * @return array
     */
    protected function loadFile($files, $locator, $delegator)
    {
        if (is_array($files)) {
            $cf = [];
            foreach ($files as $file) {
                $c = $this->loadFile($file, $locator, $delegator);
                $cf = array_merge($cf, $c);
            }
            return $cf;
        } else {
            $f = $locator->locate($files, null, false);
            if (is_array($f)) {
                $cf = [];
                foreach ($f as $item) {
                    $c = $delegator->load($item);
                    $cf = array_merge($cf, $c);
                }
                return $cf;
            } else {
                $c = $delegator->load($f);
                return $c;
            }
        }
    }

    /**
     * Save conf to cache file.
     */
    public function saveCache()
    {
        $ca = new ConfigCache(TMP . $this->cacheFile, false);
        if (!$ca->isFresh()) {
            $content = $this->export();
            $ca->write(json_encode($content, JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * Load conf from cache file.
     */
    public function loadCache()
    {
        $f = TMP . $this->cacheFile;
        if (file_exists($f)) {
            $content = file_get_contents($f);
            return json_decode($content, true);
        }
        return [];
    }

}