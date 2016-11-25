<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/11/25
 * Time: 14:20
 */

namespace Wwtg99\Flight2wwu\Component\Controller;


abstract class RestfulPlusController extends RestfulAPIController
{

    /**
     * @var string
     */
    protected $viewList = '';

    /**
     * @var string
     */
    protected $viewShow = '';

    /**
     * @var string
     */
    protected $viewCreate = '';

    /**
     * @var string
     */
    protected $viewEdit = '';

    /**
     * @var string
     */
    protected $title = '';

    /**
     * List all items.
     * Method: Get
     * @return mixed
     */
    public function index()
    {
        $fields = $this->parseFields();
        $filter = $this->parseFilters();
        $sort = $this->parseOrders();
        $paging = $this->parsePaging();
        $data = $this->listResources($fields, $filter, $sort, $paging);
        if ($this->title) {
            $data['title'] = $this->title;
        }
        return self::getResponse()->setResType('view')
            ->setView($this->viewList)
            ->setData($data)
            ->send();
    }

    /**
     * Show specific item.
     * Method Get
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $fields = $this->parseFields();
        $data = $this->getResource($id, $fields);
        if ($this->title) {
            $data['title'] = $this->title;
        }
        return self::getResponse()->setResType('view')
            ->setView($this->viewShow)
            ->setData($data)
            ->send();
    }

    /**
     * Create new Item.
     * Method Get
     * @return mixed
     */
    public function create()
    {
        return self::getResponse()->setResType('view')
            ->setView($this->viewCreate)
            ->setData(['title'=>$this->title])
            ->send();
    }

    /**
     * Edit specific item.
     * Method Get
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        $data = $this->getResource($id);
        if ($this->title) {
            $data['title'] = $this->title;
        }
        return self::getResponse()->setResType('view')
            ->setView($this->viewEdit)
            ->setData($data)
            ->send();
    }

}