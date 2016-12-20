<#php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/11/27 0027
 * Time: 下午 6:37
 */

namespace Wwtg99\App\Controller\Admin;


use Wwtg99\App\Controller\DefaultController;
use Wwtg99\App\Model\Message;
use Wwtg99\DataPool\Mappers\ArrayMapper;
use Wwtg99\DataPool\Utils\FieldFormatter;
use Wwtg99\Flight2wwu\Component\Controller\RestfulPlusController;
use Wwtg99\Flight2wwu\Component\Utils\FormatUtils;

abstract class AdminAPIController extends RestfulPlusController
{

    /**
     * @var array
     */
    protected $defaultListFields = [];

    /**
     * @var array
     */
    protected $defaultShowFields = [];

    /**
     * @var string
     */
    protected $route = '';

    /**
     * List resources.
     *
     * @param array $fields
     * @param array $filter
     * @param array $sort
     * @param array $paging
     * @return array
     */
    protected function listResources($fields = null, $filter = [], $sort = [], $paging = [])
    {
        if (!$fields) {
            $fields = $this->defaultListFields;
        }
        $mapper = $this->getMapper();
        $context = [];
        if ($paging) {
            $context = $paging;
        }
        if ($sort) {
            $context[ArrayMapper::CONTEXT_ORDER] = $sort;
        }
        if ($context) {
            $mapper->setContext($context);
        }
        $data = $mapper->select($fields, $filter);
        $head = FormatUtils::formatHead($fields);
        return ['data'=>FieldFormatter::formatDateTime($data), 'head'=>$head, 'route'=>U($this->route)];
    }

    /**
     * Get one resource.
     *
     * @param $id
     * @param array $fields
     * @return array
     */
    protected function getResource($id, $fields = [])
    {
        if (!$fields) {
            $fields = $this->defaultShowFields;
        }
        $mapper = $this->getMapper();
        $data = $mapper->get($id, $fields);
        if ($data) {
            $data = FieldFormatter::formatDateTime($data);
        }
        return ['data'=>$data, 'route'=>U($this->route), 'id'=>$id];
    }

    /**
     * Create resource.
     *
     * @param array $data
     * @return Message|array
     */
    protected function createResource($data)
    {
        $model = $this->getMapper();
        $re = $model->insert($data);
        if ($re) {
            $data['id'] = $re;
            return $data;
        }
        return Message::messageList(12);
    }

    /**
     * Update resource.
     *
     * @param $id
     * @param array $data
     * @return Message|array
     */
    protected function updateResource($id, $data)
    {
        $model = $this->getMapper();
        $re = $model->update($data, null, $id);
        if ($re) {
            $d = $model->get($id);
            return FieldFormatter::formatDateTime($d);
        }
        return Message::messageList(13);
    }

    /**
     * Delete resource.
     *
     * @param $id
     * @return Message|array
     */
    protected function deleteResource($id)
    {
        $model = $this->getMapper();
        $re = $model->delete($id);
        if ($re) {
            return ['id'=>$id];
        }
        return Message::messageList(14);
    }

    /**
     * List all items.
     * Method: Get
     * @return mixed
     */
    public function index()
    {
        getAssets()->addLibrary(['bootstrap-table', 'bootstrap-dialog']);
        return parent::index();
    }

    /**
     * Create new Item.
     * Method Get
     * @return mixed
     */
    public function create()
    {
        getAssets()->addLibrary(['validation', 'bootstrap-dialog']);
        return self::getResponse()->setResType('view')
            ->setHeader(DefaultController::$defaultViewHeaders)
            ->setView($this->viewCreate)
            ->setData(['title'=>$this->title, 'route'=>U($this->route)])
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
        getAssets()->addLibrary(['bootstrap-dialog']);
        return parent::show($id);
    }

    /**
     * Edit specific item.
     * Method Get
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        getAssets()->addLibrary(['validation', 'bootstrap-dialog']);
        return parent::edit($id);
    }

    /**
     * @return \Wwtg99\DataPool\Common\IDataMapper
     */
    abstract protected function getMapper();
}