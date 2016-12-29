<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/11/25
 * Time: 14:13
 */

namespace Wwtg99\Flight2wwu\Component\Controller;
use Wwtg99\DataPool\Mappers\ArrayMapper;

/**
 * Class RestfulParserController
 * Add parse fields, paging, orders and filters.
 * @package Wwtg99\Flight2wwu\Component\Controller
 */
abstract class RestfulParserController extends RestfulController
{

    /**
     * @var array
     */
    protected $filterFields = [];

    /**
     * @var string
     */
    protected $keyFields = 'fields';

    /**
     * @var string
     */
    protected $keyLimit = 'limit';

    /**
     * @var string
     */
    protected $keyOffset = 'offset';

    /**
     * @var string
     */
    protected $keyPage = 'page';

    /**
     * @var string
     */
    protected $keyPageSize = 'page_size';

    /**
     * @var string
     */
    protected $keySort = 'sort';

    /**
     * Use comma to separate fields
     * @return array|null
     */
    protected function parseFields()
    {
        $fields = self::getRequest()->getInput($this->keyFields);
        if ($fields) {
            return explode(',', $fields);
        }
        return null;
    }

    /**
     * Use limit and offset or page and page_size
     * @return array
     */
    protected function parsePaging()
    {
        $limit = self::getRequest()->getInput($this->keyLimit);
        $offset = self::getRequest()->getInput($this->keyOffset);
        $page = self::getRequest()->getInput($this->keyPage);
        $pageSize = self::getRequest()->getInput($this->keyPageSize);
        $paging = [];
        if ($page) {
            $paging = [ArrayMapper::CONTEXT_PAGE=>$page];
            if ($pageSize) {
                $paging[ArrayMapper::CONTEXT_PAGE_SIZE] = $pageSize;
            }
        } elseif ($limit) {
            $paging = [ArrayMapper::CONTEXT_LIMIT=>$limit];
            if ($offset) {
                $paging[ArrayMapper::CONTEXT_OFFSET] = $offset;
            }
        }
        return $paging;
    }

    /**
     * Use >field order by asc, <field order by desc, comma (,) to separate
     * @return string|null
     */
    protected function parseOrders()
    {
        return self::getRequest()->getInput($this->keySort);
    }

    /**
     * Support key=value or key>=value or key<=value or key!=value
     * @return array
     */
    protected function parseFilters()
    {
        $filter = [];
        foreach ($this->filterFields as $filterField) {
            $veq = self::getRequest()->getInput($filterField);
            if (!is_null($veq)) {
                $filter[$filterField] = $veq;
            }
            $vgt = self::getRequest()->getInput($filterField . '>');
            if (!is_null($vgt)) {
                $filter[$filterField . '[>=]'] = $vgt;
            }
            $vlt = self::getRequest()->getInput($filterField . '<');
            if (!is_null($vlt)) {
                $filter[$filterField . '[<=]'] = $vlt;
            }
            $vne = self::getRequest()->getInput($filterField . '!');
            if (!is_null($vne)) {
                $filter[$filterField . '[!=]'] = $vne;
            }
        }
        if ($filter) {
            $filter = ['AND'=>$filter];
        }
        return $filter;
    }
}