<?php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2015/12/14 0014
 * Time: 下午 9:13
 */

namespace Wwtg99\Flight2wwu\Component\View;

use Wwtg99\Flight2wwu\Component\Utils\FormatUtils;

class AssetsManager
{
    /**
     * @var array
     */
    protected $libs = [];

    /**
     * @var array
     */
    protected $enabledLibs = [];

    /**
     * @var array
     */
    protected $libsAfter = [];

    /**
     * @var string
     */
    protected $resourceDir = '';

    /**
     * AssetsManager constructor.
     * @param array $conf
     */
    public function __construct($conf = [])
    {
        if (!$conf) {
            $conf = \Flight::get('config')->get('assets');
        }
        $this->loadConfig($conf);
    }

    /**
     * @param array $conf
     */
    public function loadConfig($conf)
    {
        if (is_array($conf)) {
            $this->libs = isset($conf['libs']) ? $conf['libs'] : [];
            if (array_key_exists('global_before', $conf)) {
                $this->addLibrary($conf['global_before']);
            }
            $this->libsAfter = isset($conf['global_after']) ? $conf['global_after'] : [];
            if (isset($conf['resource_dir'])) {
                $this->resourceDir = $conf['resource_dir'];
            }
        }
    }

    /**
     * @param string|array $lib
     * @return AssetsManager
     */
    public function addLibrary($lib)
    {
        $this->enabledLibs = $this->addLib($this->enabledLibs, $lib);
        return $this;
    }

    /**
     * @return string
     */
    public function renderCss()
    {
        $libs = $this->getLibs();
        $out = [];
        foreach ($libs as $name) {
            if (isset($this->libs[$name])) {
                $l = $this->libs[$name];
                $prefix = isset($l['prefix']) ? $l['prefix'] : '';
                $css = isset($l['css']) ? $l['css'] : [];
                foreach ($css as $c) {
                    if (is_array($c)) {
                        if (isset($c['file'])) {
                            $attr = [];
                            if (isset($c['attr'])) {
                                $attr = $c['attr'];
                            }
                            $st = $this->formatStyle($c['file'], $prefix, $attr);
                        }
                    } else {
                        $st = $this->formatStyle($c, $prefix);
                    }
                    array_push($out, $st);
                }
            }
        }
        return implode("\n", $out) . "\n";
    }

    /**
     * Dump css.
     */
    public function dumpCss()
    {
        echo $this->renderCss();
    }

    /**
     * @return string
     */
    public function renderJs()
    {
        $libs = $this->getLibs();
        $out = [];
        foreach ($libs as $name) {
            if (isset($this->libs[$name])) {
                $l = $this->libs[$name];
                $prefix = isset($l['prefix']) ? $l['prefix'] : '';
                $js = isset($l['js']) ? $l['js'] : [];
                foreach ($js as $c) {
                    if (is_array($c)) {
                        if (isset($c['file'])) {
                            $attr = [];
                            if (isset($c['attr'])) {
                                $attr = $c['attr'];
                            }
                            $st = $this->formatScript($c['file'], $prefix, $attr);
                        }
                    } else {
                        $st = $this->formatScript($c, $prefix);
                    }
                    array_push($out, $st);
                }
            }
        }
        return implode("\n", $out) . "\n";
    }

    /**
     * Dump js.
     */
    public function dumpJs()
    {
        echo $this->renderJs();
    }

    /**
     * @param string $name
     * @param string $prefix
     * @return string
     */
    public function getResource($name, $prefix = '')
    {
        if (!$prefix) {
            $prefix = $this->resourceDir;
        }
        return $this->formatPath($prefix, $name);
    }

    /**
     * @param array $arr
     * @param string|array $name
     * @return array
     */
    private function addLib(array $arr, $name)
    {
        if (is_array($name)) {
            foreach ($name as $item) {
                $arr = $this->addLib($arr, $item);
            }
        } else {
            if (isset($this->libs[$name])) {
                $l = $this->libs[$name];
                $depends = isset($l['depends']) ? $l['depends'] : [];
                $arr = $this->addLib($arr, $depends);
                if (!in_array($name, $arr)) {
                    array_push($arr, $name);
                }
            }
        }
        return $arr;
    }

    /**
     * @return array
     */
    private function getLibs()
    {
        $libs = $this->enabledLibs;
        if ($this->libsAfter) {
            $libs = $this->addLib($libs, $this->libsAfter);
        }
        return $libs;
    }

    /**
     * @param $file
     * @param string $prefix
     * @param array $attr
     * @return string
     */
    private function formatStyle($file, $prefix = '', $attr = [])
    {
        if (!isset($attr['rel'])) {
            $attr['rel'] = 'stylesheet';
        }
        $attr['href'] = $this->formatPath($prefix, $file);
        if (!isset($attr['type'])) {
            $attr['type'] = 'text/css';
        }
        $ele = ['<link'];
        foreach ($attr as $name => $val) {
            array_push($ele, "$name=\"$val\"");
        }
        array_push($ele, '/>');
        return implode(' ', $ele);
    }

    /**
     * @param $file
     * @param string $prefix
     * @param array $attr
     * @return string
     */
    private function formatScript($file, $prefix = '', $attr = [])
    {
        $attr['src'] = $this->formatPath($prefix, $file);
        if (!isset($attr['type'])) {
            $attr['type'] = 'text/javascript';
        }
        $ele = ['<script'];
        foreach ($attr as $name => $val) {
            array_push($ele, "$name=\"$val\"");
        }
        array_push($ele, '></script>');
        return implode(' ', $ele);
    }

    /**
     * @param $prefix
     * @param $file
     * @return string
     */
    private function formatPath($prefix, $file)
    {
        return $prefix ? FormatUtils::formatWebPathArray([getConfig()->get('base_url'), $prefix, $file]) : $file;
    }
}