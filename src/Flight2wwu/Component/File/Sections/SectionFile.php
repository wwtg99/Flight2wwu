<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/21
 * Time: 16:41
 */

namespace Flight2wwu\Component\File\Sections;

class SectionFile
{

    /**
     * @var array
     */
    protected $sections = [];

    function __construct($sections = array())
    {
        $this->sections = $sections;
    }

    /**
     * @param Section $section
     * @return $this
     */
    public function addSection(Section $section)
    {
        array_push($this->sections, $section);
        return $this;
    }

    /**
     * @param array $sections
     * @return $this
     */
    public function addSections($sections)
    {
        foreach ($sections as $i => $s) {
            $this->addSection($s);
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * @param int $index
     * @return null|Section
     */
    public function getSection($index)
    {
        if ($index >= 0 && $index < count($this->sections)) {
            return $this->sections[$index];
        }
        return null;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return count($this->sections);
    }

    /**
     * @return $this
     */
    public function clearSection()
    {
        $this->sections = [];
        return $this;
    }

    /**
     * @param string $name
     * @return int
     */
    public function getIndexByName($name)
    {
        foreach ($this->sections as $i => $sec) {
            if ($sec instanceof Section) {
                if ($sec->getName() == $name) {
                    return $i;
                }
            }
        }
        return -1;
    }

    /**
     * @param string $name
     * @return Section|null
     */
    public function getSectionByName($name)
    {
        foreach ($this->sections as $i => $sec) {
            if ($sec instanceof Section) {
                if ($sec->getName() == $name) {
                    return $sec;
                }
            }
        }
        return null;
    }

    /**
     * @param string $filename
     * @param array $rules
     * @return SectionFile
     */
    public static function fromTxtFile($filename, $rules)
    {
        $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $insection = false;
        $read_head = false;
        $secinfo = [];
        $head = [];
        $data = [];
        $secs =[];
        foreach ($lines as $ln => $lcont) {
            if (!trim($lcont)) {
                continue;
            }
            if ($insection) {
                if (substr($lcont, 0, 1) == '[' && substr($lcont, strlen($lcont) - 1, 1) == ']') {
                    // close section
                    $sec = new Section($secinfo['type'], $secinfo['name'], $data, SectionFile::formatHead($head));
                    array_push($secs, $sec);
                    // start section
                    $secinfo = SectionFile::readSectionInfo($lcont, $rules);
                    $read_head = false;
                    $head = [];
                    $data = [];
                    continue;
                }
                if ($secinfo['has_head'] && !$read_head) {
                    $head = explode($secinfo['del'], $lcont);
                    $read_head = true;
                } else {
                    if ($secinfo['type'] == Section::KV_SECTION) {
                        $darr = SectionFile::readKvLine($lcont, $secinfo['del']);
                        $data = array_merge($data, $darr);
                    } elseif ($secinfo['type'] == Section::TSV_SECTION) {
                        $darr = SectionFile::readTsvLine($lcont, $secinfo['del'], $head);
                        array_push($data, $darr);
                    } else {
                        array_push($data, $lcont);
                    }
                }
            } else {
                if (substr($lcont, 0, 1) == '[' && substr($lcont, strlen($lcont) - 1, 1) == ']') {
                    $secinfo = SectionFile::readSectionInfo($lcont, $rules);
                    if (is_null($secinfo)) {
                        continue;
                    } else {
                        $insection = true;
                    }
                } else {
                    continue;
                }
            }
        }
        if ($secinfo) {
            $sec = new Section($secinfo['type'], $secinfo['name'], $data, SectionFile::formatHead($head));
            array_push($secs, $sec);
        }
        return new SectionFile($secs);
    }

    /**
     * @param string $line
     * @param array $rules
     * @return array|null
     */
    private static function readSectionInfo($line, $rules)
    {
        $secname = substr($line, 1, strlen($line) - 2);
        if (array_key_exists($secname, $rules)) {
            $rule = $rules[$secname];
            $type = Section::getTypeCode($rule['type']);
            if ($type == Section::TSV_SECTION) {
                $has_head = array_key_exists('head', $rule) ? $rule['head'] : true;
            } else {
                $has_head = array_key_exists('head', $rule) ? $rule['head'] : false;
            }
            $del = array_key_exists('del', $rule) ? $rule['del'] : "\t";
            return ['name'=>$secname, 'has_head'=>$has_head, 'type'=>$type, 'del'=>$del, 'rule'=>$rule];
        } else {
            return null;
        }
    }

    /**
     * @param string $line
     * @param string $del
     * @param array $head
     * @return array
     */
    private static function readTsvLine($line, $del, $head = [])
    {
        $out = [];
        $darr = explode($del, $line);
        if ($head) {
            $n = count($head);
            for ($i = 0; $i < $n; $i++) {
                if (count($darr) > $i) {
                    $out[$head[$i]] = $darr[$i];
                } else {
                    $out[$head[$i]] = null;
                }
            }
            return $out;
        } else {
            return $darr;
        }
    }

    /**
     * @param string $line
     * @param string $del
     * @return array
     */
    private static function readKvLine($line, $del)
    {
        $kv = explode($del, $line);
        if (count($kv) >= 2) {
            return [$kv[0] => $kv[1]];
        } else {
            return [$kv[0] => null];
        }
    }

    /**
     * @param array $head
     * @return array
     */
    private static function formatHead($head)
    {
        $fhead = [];
        foreach ($head as $h) {
            array_push($fhead, ['field'=>$h, 'title'=>$h]);
        }
        return $fhead;
    }
} 