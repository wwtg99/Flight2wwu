<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/21
 * Time: 17:56
 */

namespace Flight2wwu\Component\File;

use Flight2wwu\Component\File\Sections\Section;
use Flight2wwu\Component\File\Sections\SectionFile;
use Respect\Validation\Rules\File;

class ExcelFile extends CommonFile implements Downloadable
{

    /**
     * @var \PHPExcel
     */
    private $excel;

    /**
     * @param string $filename
     * @return mixed
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
        $objWriter = \PHPExcel_IOFactory::createWriter($this->excel, "Excel2007");
        $objWriter->save('php://output');
    }

    /**
     * @param string $path
     * @return void
     */
    public function writeTo($path)
    {
        $objWriter = \PHPExcel_IOFactory::createWriter($this->excel, "Excel2007");
        $objWriter->save($path);
    }

    function __construct()
    {
        $this->ext = '.xlsx';
        $this->mime = self::getMimeFromExtension($this->ext);
    }

    /**
     * @return \PHPExcel
     */
    public function getData()
    {
        return $this->excel;
    }

    /**
     * @param $filename
     * @return ExcelFile
     */
    public static function createFromFile($filename)
    {
        $excel = new ExcelFile();
        $excel->excel = \PHPExcel_IOFactory::load($filename);
        return $excel;
    }

    /**
     * @param SectionFile $sectionFile
     * @return ExcelFile
     */
    public static function createFromSection(SectionFile $sectionFile)
    {
        $data = [];
        //handle sections
        foreach ($sectionFile->getSections() as $i => $section) {
            if ($section instanceof Section) {
                //show name
                if ($section->getShowName()) {
                    $name = ExcelFile::getName($section);
                    array_push($data, $name);
                }
                //depends on type
                if ($section->getType() == Section::TSV_SECTION) {
                    $data = array_merge($data, ExcelFile::createTsv($section));
                } elseif ($section->getType() == Section::KV_SECTION) {
                    $data = array_merge($data, ExcelFile::createKv($section));
                } elseif ($section->getType() == Section::RAW_SECTION) {
                    $data = array_merge($data, ExcelFile::createRaw($section));
                }
                array_push($data, []);
            }
        }
        $excel = new ExcelFile();
        $excel->excel = ExcelFile::getExcel($data);
        return $excel;
    }

    /**
     * @param array $data
     * @param array $head: [['field'=>'f1', 'title'=>'t1'], ...]
     * @param string $null
     * @return ExcelFile
     */
    public static function createFromData($data, $head = [], $null = ' - ')
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
                        array_push($line, $null);
                    }
                }
                array_push($exdata, $line);
            }
        } else {
            $exdata = $data;
        }
        $excel = new ExcelFile();
        $excel->excel = ExcelFile::getExcel($exdata);
        return $excel;
    }

    /**
     * @param array $data
     * @return \PHPExcel
     * @throws \PHPExcel_Exception
     */
    private static function getExcel($data)
    {
        $excel = new \PHPExcel();
        $app = \Flight::get('app');
        $excel->getProperties()->setCreator($app)->setLastModifiedBy($app)->setTitle($app);
        $row = 1;
        $col = 0;
        $sheet = $excel->getActiveSheet();
        foreach ($data as $d) {
            if (is_array($d)) {
                foreach ($d as $dd) {
                    $sheet->getCellByColumnAndRow($col, $row)->setValue((string)$dd);
                    $col++;
                }
                $col = 0;
            } else {
                $sheet->getCellByColumnAndRow($col, $row)->setValue((string)$d);
            }
            $row++;
        }
        return $excel;
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
            $line = [];
            if (!in_array($h, $skip)) {
                $v = $d;
                if (is_null($v)) {
                    $v = $section->getNull();
                }
                //prefix
                if ($section->getPrefix()) {
                    array_push($line, $section->getPrefix());
                }
                //head
                if ($section->getHead()) {
                    foreach ($section->getHead() as $sh) {
                        $hf = $sh['field'];
                        if ($hf == $h) {
                            array_push($line, $sh['title']);
                            break;
                        }
                    }
                } else {
                    array_push($line, $h);
                }
                array_push($line, $v);
                //postfix
                if ($section->getPostfix()) {
                    array_push($line, $section->getPostfix());
                }
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
                array_push($out, $htitle);
            }
            //get data
            $darr = ExcelFile::getTsvData($section, $harr);
            $out = array_merge($out, $darr);
        } else {
            //get data
            $darr = ExcelFile::getTsvData($section);
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
        return $section->getData();
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
            //prefix
            if ($section->getPrefix()) {
                array_push($line, $section->getPrefix());
            }
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
            //postfix
            if ($section->getPostfix()) {
                array_push($line, $section->getPostfix());
            }
            array_push($darr, $line);
        }
        return $darr;
    }

} 