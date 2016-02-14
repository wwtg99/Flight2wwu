<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/21
 * Time: 17:55
 */

namespace Flight2wwu\Component\File;

use Flight2wwu\Component\File\Sections\SectionFile;
use Flight2wwu\Component\File\Sections\Section;
use Flight2wwu\Component\Utils\FormatUtils;

class TxtFile extends CommonFile implements Downloadable, Printable
{

    /**
     * @param string $filename
     * @return void
     */
    public function download($filename = '')
    {
        if ($filename) {
            $f = $filename . $this->getExtension();
        } else {
            $f = 'download' . $this->getExtension();
        }
        self::printHeader();
        header("Content-Disposition: attachment;filename='$f'");
        echo $this->content;
    }

    /**
     * @return void
     */
    public function printContent()
    {
        self::printHeader();
        echo $this->content;
    }

    /**
     * @param string $path
     * @return void
     */
    public function writeTo($path)
    {
        file_put_contents($path, $this->content);
    }

    /**
     * @param string $extension
     */
    function __construct($extension = '.txt')
    {
        $this->ext = FormatUtils::formatExtension($extension);
        $this->mime = self::getMimeFromExtension($this->ext);
    }

    /**
     * @param string $filename
     * @return null|TxtFile
     */
    public static function createFromFile($filename)
    {
        if (file_exists($filename)) {
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $cont = file_get_contents($filename);
            $txt = new TxtFile($ext);
            $txt->content = $cont;
            return $txt;
        }
        return null;
    }

    /**
     * @param string $str
     * @param string $extension
     * @return TxtFile
     */
    public static function createFromStr($str, $extension = '.txt')
    {
        $txt = new TxtFile($extension);
        $txt->content = (string)$str;
        return $txt;
    }

    /**
     * @param SectionFile $sectionFile
     * @param string $extension
     * @return TxtFile
     */
    public static function createFromSection(SectionFile $sectionFile, $extension = '.tsv')
    {
        $cont = [];
        //handle data
        foreach ($sectionFile->getSections() as $i => $section) {
            if ($section instanceof Section) {
                if ($section->getShowName()) {
                    array_push($cont, TxtFile::getName($section));
                }
                if ($section->getType() == Section::KV_SECTION) {
                    $cont = array_merge($cont, TxtFile::createKv($section));
                } elseif ($section->getType() == Section::TSV_SECTION) {
                    $cont = array_merge($cont, TxtFile::createTsv($section));
                } elseif ($section->getType() == Section::RAW_SECTION) {
                    $cont = array_merge($cont, TxtFile::createRaw($section));
                }
                array_push($cont, '');
            }
        }
        $txt = new TxtFile($extension);
        $txt->content = implode("\n", $cont);
        return $txt;
    }

    /**
     * @param array $data
     * @param array $head: [['field'=>'f1', 'title'=>'t1'], ...]
     * @param array $rule
     * @return TxtFile
     */
    public static function createFromData($data, $head = [], $rule = [])
    {
        $exdata = [];
        if ($head) {
            $head_field = [];
            $head_title = [];
            foreach ($head as $h) {
                array_push($head_field, $h['field']);
                array_push($head_title, $h['title']);
            }
            array_push($exdata, $head_title);
            foreach ($data as $d) {
                $line = [];
                foreach ($head_field as $f) {
                    if (array_key_exists($f, $d)) {
                        array_push($line, $d[$f]);
                    } else {
                        array_push($line, null);
                    }
                }
                array_push($exdata, $line);
            }
        } else {
            $exdata = $data;
        }
        $rule['showName'] = false;
        $sec = new Section(Section::RAW_SECTION, '', $exdata, null, $rule);
        return TxtFile::createFromSection(new SectionFile([$sec]));
    }

    /**
     * @param Section $section
     * @return string
     */
    private static function getName(Section $section)
    {
        static $i = 1;
        $name = '';
        if ($section->getShowName()) {
            $name = $section->getName();
            if (!$name) {
                $name = "Section " . $i++;
            }
            $name = '[' . $name . ']';
        }
        return $name;
    }

    /**
     * @param Section $section
     * @return array
     */
    private static function createKv(Section $section)
    {
        //skip
        $skip = $section->getSkip();
        //data
        $darr = [];
        foreach ($section->getData() as $h => $d) {
            if (!in_array($h, $skip)) {
                $v = $d;
                if (is_null($v)) {
                    $v = $section->getNull();
                }
                $ht = $h;
                if ($section->getHead()) {
                    foreach ($section->getHead() as $sh) {
                        if ($sh['field'] == $h) {
                            $ht = $sh['title'];
                            break;
                        }
                    }
                }
                $line = $section->getPrefix() . $ht . $section->getDel() . $v . $section->getPostfix();
                array_push($darr, $line);
            }
        }
        return $darr;
    }

    /**
     * @param Section $section
     * @return array
     */
    private static function createTsv(Section $section)
    {
        $out = [];
        if ($section->getHead()) {
            //skip
            $skip = $section->getSkip();
            $harr = [];
            $htitle = [];
            foreach ($section->getHead() as $h) {
                $hf = $h['field'];
                if (!in_array($hf, $skip)) {
                    array_push($harr, $h);
                    array_push($htitle, $h['title']);
                }
            }
            //show head
            if ($section->getShowHead()) {
                $h = implode($section->getDel(), $htitle);
                array_push($out, $h);
            }
            //get data
            $darr = TxtFile::getTsvData($section, $harr);
            $out = array_merge($out, $darr);
        } else {
            $darr = TxtFile::getTsvData($section);
            $out = array_merge($out, $darr);
        }
        return $out;
    }

    /**
     * @param Section $section
     * @return array
     */
    private static function createRaw(Section $section)
    {
        $out = [];
        foreach ($section->getData() as $i => $d) {
            if (is_array($d)) {
                array_push($out, implode($section->getDel(), $d));
            } else {
                array_push($out, $d);
            }
        }
        return $out;
    }

    /**
     * @param Section $section
     * @param array $harr
     * @return array
     */
    private static function getTsvData(Section $section, $harr = array())
    {
        $darr = [];
        foreach ($section->getData() as $j => $d) {
            $line = [];
            //data
            if ($harr) {
                foreach ($harr as $h) {
                    $hf = $h['field'];
                    if (array_key_exists($hf, $d)) {
                        $v = $d[$hf];
                        if (is_null($v)) {
                            array_push($line, $section->getNull());
                        } else {
                            array_push($line, $d[$hf]);
                        }
                    }
                }
            } else {
                foreach ($d as $dd) {
                    array_push($line, $dd);
                }
            }
            $l = implode($section->getDel(), $line);
            //prefix
            if ($section->getPrefix()) {
                $l = $section->getPrefix() . $l;
            }
            //postfix
            if ($section->getPostfix()) {
                $l = $l . $section->getPostfix();
            }
            array_push($darr, $l);
        }
        return $darr;
    }
} 