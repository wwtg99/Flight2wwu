<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/11/24
 * Time: 14:13
 */

namespace Wwtg99\Flight2wwu\Component\Controller;


use Wwtg99\App\Controller\DefaultController;
use Wwtg99\App\Model\Message;

/**
 * Class RestfulAPIController
 * Implement restful API controller.
 * @package Wwtg99\Flight2wwu\Component\Controller
 */
abstract class RestfulAPIController extends RestfulParserController
{

    /**
     * @var array
     */
    protected $createFields = [];

    /**
     * @var array
     */
    protected $updateFields = [];

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
     * @return Message|array
     */
    abstract protected function createResource($data);

    /**
     * Update resource.
     *
     * @param $id
     * @param array $data
     * @return Message|array
     */
    abstract protected function updateResource($id, $data);

    /**
     * Delete resource.
     *
     * @param $id
     * @return Message|array
     */
    abstract protected function deleteResource($id);

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
        return self::getResponse()->setHeader(DefaultController::$defaultApiHeaders)->setResType('json')->setData($data)->send();
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
        return self::getResponse()->setHeader(DefaultController::$defaultApiHeaders)->setResType('json')->setData($data)->send();
    }

    /**
     * Create new Item.
     * Method Get
     * @return mixed
     */
    public function create()
    {
        return false;
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
                return self::getResponse()->setHeader(DefaultController::$defaultApiHeaders)->setResType('json')->setResCode(200)->setData(TA($data->toApiArray()))->send();
            } elseif ($data) {
                return self::getResponse()->setHeader(DefaultController::$defaultApiHeaders)->setResType('json')->setResCode(201)->setData($data)->send();
            } else {
                return self::getResponse()->setHeader(DefaultController::$defaultApiHeaders)->setResType('json')->setResCode(500)->setData(TA(Message::messageList(1)->toApiArray()))->send();
            }
        } else {
            \Flight::redirect(U('405'));
        }
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
        return false;
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
                return self::getResponse()->setHeader(DefaultController::$defaultApiHeaders)->setResType('json')->setResCode(200)->setData(TA($data->toApiArray()))->send();
            } elseif ($data) {
                return self::getResponse()->setHeader(DefaultController::$defaultApiHeaders)->setResType('json')->setResCode(201)->setData($data)->send();
            } else {
                return self::getResponse()->setHeader(DefaultController::$defaultApiHeaders)->setResType('json')->setResCode(500)->setData(TA(Message::messageList(1)->toApiArray()))->send();
            }
        } elseif (self::getRequest()->checkMethod('PATCH')) {
            $d = self::getRequest()->getArrayInputN($this->updateFields);
            $data = $this->updateResource($id, $d);
            if ($data instanceof Message) {
                return self::getResponse()->setHeader(DefaultController::$defaultApiHeaders)->setResType('json')->setResCode(200)->setData(TA($data->toApiArray()))->send();
            } elseif ($data) {
                return self::getResponse()->setHeader(DefaultController::$defaultApiHeaders)->setResType('json')->setResCode(201)->setData($data)->send();
            } else {
                return self::getResponse()->setHeader(DefaultController::$defaultApiHeaders)->setResType('json')->setResCode(500)->setData(TA(Message::messageList(1)->toApiArray()))->send();
            }
        } else {
            \Flight::redirect(U('405'));
        }
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
                return self::getResponse()->setHeader(DefaultController::$defaultApiHeaders)->setResType('json')->setResCode(200)->setData(TA($data->toApiArray()))->send();
            } elseif ($data) {
                return self::getResponse()->setHeader(DefaultController::$defaultApiHeaders)->setResType('json')->setResCode(204)->setData($data)->send();
            } else {
                return self::getResponse()->setHeader(DefaultController::$defaultApiHeaders)->setResType('json')->setResCode(500)->setData(TA(Message::messageList(1)->toApiArray()))->send();
            }
        } else {
            \Flight::redirect(U('405'));
        }
        return false;
    }

}