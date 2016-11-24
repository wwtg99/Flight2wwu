<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/11/24
 * Time: 14:13
 */

namespace Wwtg99\Flight2wwu\Component\Controller;


use Wwtg99\App\Model\Message;

abstract class RestfulAPIController extends RestfulController
{

    /**
     * @var array
     */
    protected $filterFields = [];

    /**
     * @var array
     */
    protected $createFields = [];

    /**
     * @var array
     */
    protected $updateFields = [];

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
     * @var string
     */
    protected $viewCreate = '';

    /**
     * @var string
     */
    protected $viewEdit = '';

    /**
     * List resources.
     *
     * @param array $fields
     * @param array $filter
     * @param array $sort
     * @param array $paging
     * @return array
     */
    abstract protected function listResources($fields = null, $filter = [], $sort = [], $paging = []);

    /**
     * Get one resource.
     *
     * @param $id
     * @param array $fields
     * @return array
     */
    abstract protected function getResource($id, $fields = []);

    /**
     * Create resource.
     *
     * @param array $data
     * @return Message|bool
     */
    abstract protected function createResource($data);

    /**
     * Update resource.
     *
     * @param $id
     * @param array $data
     * @return Message|bool
     */
    abstract protected function updateResource($id, $data);

    /**
     * Delete resource.
     *
     * @param $id
     * @return Message|bool
     */
    abstract protected function deleteResource($id);

    /**
     * List all items.
     * Method: Get
     * @return mixed
     */
    public function index()
    {
        //fields: comma to separate
        $fields = self::getRequest()->getInput($this->keyFields);
        //paging: use limit and offset or page and page size
        $limit = self::getRequest()->getInput($this->keyLimit);
        $offset = self::getRequest()->getInput($this->keyOffset);
        $page = self::getRequest()->getInput($this->keyPage);
        $pageSize = self::getRequest()->getInput($this->keyPageSize);
        $paging = [];
        if ($page) {
            $paging = ['page'=>$page];
            if ($pageSize) {
                $paging['pageSize'] = $pageSize;
            }
        } elseif ($limit) {
            $paging = ['limit'=>$limit];
            if ($offset) {
                $paging['offset'] = $offset;
            }
        }
        //sort: use +field order by asc, -field order by desc, comma (,) to separate
        $sortstr = self::getRequest()->getInput($this->keySort);
        $sort = [];
        if ($sortstr) {
            $sortfs = explode(',', $sortstr);
            foreach ($sortfs as $sortf) {
                if (substr($sortf, 0, 1) == '+') {
                    array_push($sort, [substr($sortf, 1) => 'ASC']);
                } elseif (substr($sortf, 0, 1) == '-') {
                    array_push($sort, [substr($sortf, 1) => 'DESC']);
                }
            }
        }
        //filter: key=value or key>=value or key<=value or key!=value
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
        $data = $this->listResources($fields, $filter, $sort, $paging);
        return self::getResponse()->setResType('json')->setData($data)->send();
    }

    /**
     * Show specific item.
     * Method Get
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        //fields: comma to separate
        $fields = self::getRequest()->getInput($this->keyFields);
        $data = $this->getResource($id, $fields);
        return self::getResponse()->setResType('json')->setData($data)->send();
    }

    /**
     * Create new Item.
     * Method Get
     * @return mixed
     */
    public function create()
    {
        return self::getResponse()->setResType('view')->setView($this->viewCreate)->send();
    }

    /**
     * Store new Item.
     * Method Post
     * @return mixed
     */
    public function store()
    {
        if (self::getRequest()->checkMethod('POST')) {
            $d = self::getRequest()->getArrayInputN($this->createFields);
            $data = $this->createResource($d);
            if ($data instanceof Message) {
                return self::getResponse()->setResType('json')->setResCode(400)->setData($data->toApiArray())->send();
            } elseif ($data) {
                return self::getResponse()->setResType('json')->setResCode(201)->setData($data)->send();
            }
        } else {
            \Flight::redirect(U('405'));
        }
        \Flight::redirect(U('404'));
        return false;
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
        return self::getResponse()->setResType('view')->setView($this->viewEdit)->setData($data)->send();
    }

    /**
     * Update specific item.
     * Method Post
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        if (self::getRequest()->checkMethod('PUT')) {
            $d = self::getRequest()->getArrayInput($this->updateFields);
            $data = $this->updateResource($id, $d);
            if ($data instanceof Message) {
                return self::getResponse()->setResType('json')->setResCode(400)->setData($data->toApiArray())->send();
            } elseif ($data) {
                return self::getResponse()->setResType('json')->setResCode(201)->setData($data)->send();
            }
        } elseif (self::getRequest()->checkMethod('PATCH')) {
            $d = self::getRequest()->getArrayInputN($this->updateFields);
            $data = $this->updateResource($id, $d);
            if ($data instanceof Message) {
                return self::getResponse()->setResType('json')->setResCode(400)->setData($data->toApiArray())->send();
            } elseif ($data) {
                return self::getResponse()->setResType('json')->setResCode(201)->setData($data)->send();
            }
        } else {
            \Flight::redirect(U('405'));
        }
        \Flight::redirect(U('404'));
        return false;
    }

    /**
     * Destroy item.
     * Method Post
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        if (self::getRequest()->checkMethod('DELETE')) {
            $data = $this->deleteResource($id);
            if ($data instanceof Message) {
                return self::getResponse()->setResType('json')->setResCode(400)->setData($data->toApiArray())->send();
            } elseif ($data) {
                return self::getResponse()->setResType('json')->setResCode(204)->setData($data)->send();
            }
        } else {
            \Flight::redirect(U('405'));
        }
        \Flight::redirect(U('404'));
        return false;
    }

}