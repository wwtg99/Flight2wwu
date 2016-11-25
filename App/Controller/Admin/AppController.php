<#php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/10/20
 * Time: 11:48
 */

namespace Wwtg99\App\Controller\Admin;


use Wwtg99\App\Model\Message;
use Wwtg99\DataPool\Mappers\ArrayMapper;
use Wwtg99\Flight2wwu\Component\Controller\RestfulPlusController;
use Wwtg99\Flight2wwu\Component\Utils\FormatUtils;

class AppController extends RestfulPlusController
{

    protected $filterFields = ['app_name', 'descr', 'redirect_uri'];

    protected $createFields = ['app_name', 'descr', 'redirect_uri'];

    protected $updateFields = ['app_name', 'descr', 'redirect_uri'];

    protected $viewList = 'admin/app_index';

    protected $viewShow = 'admin/app_show';

    protected $viewCreate = 'admin/app_edit';

    protected $viewEdit = 'admin/app_edit';

    protected $title = 'App Management';

    protected $route = 'admin/apps';

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
            $fields = ['app_id', 'app_name', 'redirect_uri', 'created_at', 'updated_at'];
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
        return ['data'=>$data, 'head'=>$head, 'route'=>U($this->route)];
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
            $fields = ['app_id', 'app_name', 'descr', 'app_secret', 'redirect_uri', 'created_at', 'updated_at'];
        }
        $mapper = $this->getMapper();
        $data = $mapper->get($id, $fields);
        return ['data'=>$data, 'route'=>U($this->route)];
    }

    /**
     * Create resource.
     *
     * @param array $data
     * @return Message|array
     */
    protected function createResource($data)
    {
        if (!isset($data['app_name'])) {
            return new Message(11, 'invalid app_name');
        }
        if (!isset($data['redirect_uri'])) {
            return new Message(11, 'invalid redirect_uri');
        }
        $model = $this->getMapper();
        $re = $model->insert($data);
        if ($re) {
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
        // TODO: Implement updateResource() method.
    }

    /**
     * Delete resource.
     *
     * @param $id
     * @return Message|array
     */
    protected function deleteResource($id)
    {
        // TODO: Implement deleteResource() method.
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
     * @return array|bool
     */
    protected function storeParse()
    {
        $name = self::getRequest()->checkInput('app_name');
        if ($name instanceof Message) {
            $msg = $name->toArray();
            getOValue()->addOldOnce('msg', $msg);
            \Flight::redirect($this->baseRoute . '/create');
            return false;
        }
        $uri = self::getRequest()->checkInput('redirect_uri');
        if ($uri instanceof Message) {
            $msg = $uri->toArray();
            getOValue()->addOldOnce('msg', $msg);
            \Flight::redirect($this->baseRoute . '/create');
            return false;
        }
        return parent::storeParse();
    }

    /**
     * @return \Wwtg99\DataPool\Common\IDataMapper
     */
    protected function getMapper()
    {
        $dep = getDataPool()->getConnection('auth')->getMapper('App');
        return $dep;
    }
}