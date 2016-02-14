<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/21
 * Time: 17:26
 */

namespace Flight2wwu\Component\File\Sections;

class Section
{
    const TSV_SECTION = 1;
    const KV_SECTION = 2;
    const RAW_SECTION = 3;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     * [[field=>"", title=>"", type=>""], ...]
     */
    protected $head;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var int: TSV_SECTION or KV_SECTION
     */
    protected $type;

    /**
     * @var array
     *
     * rules:
     * showHead: bool, default true
     * showName: bool, default true
     * null: string, default '-'
     * skip: array, default []
     * prefix: string, default ''
     * postfix: string, default ''
     * del: string, default '\t'
     */
    protected $rule;

    /**
     * @param int $type
     * @param string $name
     * @param array $data
     * @param array $head
     * @param array $rule
     */
    function __construct($type, $name, $data, $head = array(), $rule = array())
    {
        $this->type = $type;
        $this->name = $name;
        $this->data = $data;
        $this->head = $head;
        $this->rule = $rule;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getHead()
    {
        return $this->head;
    }

    /**
     * @param array $head
     */
    public function setHead($head)
    {
        $this->head = $head;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function getShowHead()
    {
        if (array_key_exists('showHead', $this->rule)) {
            return (bool)$this->rule['showHead'];
        }
        return true;
    }

    /**
     * @return bool
     */
    public function getShowName()
    {
        if (array_key_exists('showName', $this->rule)) {
            return (bool)$this->rule['showName'];
        }
        return true;
    }

    /**
     * @return string
     */
    public function getNull()
    {
        if (array_key_exists('null', $this->rule)) {
            return $this->rule['null'];
        }
        return '-';
    }

    /**
     * @return array
     */
    public function getSkip()
    {
        if (array_key_exists('skip', $this->rule)) {
            return $this->rule['skip'];
        }
        return [];
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        if (array_key_exists('prefix', $this->rule)) {
            return $this->rule['prefix'];
        }
        return '';
    }

    /**
     * @return string
     */
    public function getPostfix()
    {
        if (array_key_exists('postfix', $this->rule)) {
            return $this->rule['postfix'];
        }
        return '';
    }

    /**
     * @return string
     */
    public function getDel()
    {
        if (array_key_exists('del', $this->rule)) {
            return $this->rule['del'];
        }
        return "\t";
    }

    /**
     * @param string $type
     * @return int
     */
    public static function getTypeCode($type)
    {
        switch (strtolower($type)) {
            case 'tsv': return Section::TSV_SECTION;
            case 'kv': return Section::KV_SECTION;
            default: return Section::RAW_SECTION;
        }
    }
} 