<#php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/10/20
 * Time: 11:53
 */

namespace Wwtg99\App\Controller\Admin;


use Wwtg99\App\Model\Message;
use Wwtg99\DataPool\Utils\FieldFormatter;
use Wwtg99\Flight2wwu\Component\Controller\RestfulController;
use Wwtg99\Flight2wwu\Component\Utils\FormatUtils;

abstract class ResourceAdminController extends RestfulController
{
    /**
     * @var array
     */
    protected $indexFields = [];

    /**
     * @var array
     */
    protected $showFields = [];

    /**
     * @var array
     */
    protected $storeFields =[];

    /**
     * @var array
     */
    protected $updateFields = [];

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $baseRoute = '';

    /**
     * @var string
     */
    protected $templatePrefix = '';

    /**
     * List all items.
     * Method: Get
     * @return mixed
     */
    public function index()
    {
        $model = $this->getMapper();
        $re = $model->select($this->indexFields);
        $re = FieldFormatter::formatDateTime($re);
        getAssets()->addLibrary(['bootstrap-table', 'bootstrap-dialog']);
        return self::getResponse()->setResType('view')
            ->setView($this->templatePrefix . 'index')
            ->setData(['data'=>$re, 'head'=>FormatUtils::formatHead($this->indexFields), 'title'=>$this->title, 'route'=>$this->baseRoute])
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
        $model = $this->getMapper();
        $re = $model->get($id, $this->showFields);
        $re = FieldFormatter::formatDateTime($re);
        return self::getResponse()->setResType('view')
            ->setView($this->templatePrefix . 'show')
            ->setData(['data'=>$re, 'title'=>$this->title, 'route'=>$this->baseRoute])
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
            ->setView($this->templatePrefix . 'edit')
            ->setData(['title'=>$this->title, 'route'=>$this->baseRoute])
            ->send();
    }

    /**
     * Store new Item.
     * Method Post
     * @return mixed
     */
    public function store()
    {
        $d = $this->storeParse();
        if ($d === false) {
            return false;
        }
        $model = $this->getMapper();
        $re = $model->insert($d);
        if ($re) {
            $msg = Message::getMessage(0, 'create successfully', 'success');
        } else {
            $msg = Message::getMessage(12);
        }
        getOValue()->addOldOnce('msg', $msg);
        \Flight::redirect($this->baseRoute);
        return false;
    }

    /**
     * @return array|bool
     */
    protected function storeParse()
    {
        $d = FormatUtils::removeArrayEmpty(FormatUtils::trimArray(self::getRequest()->getArrayInputN($this->storeFields)));
        return $d;
    }

    /**
     * Edit specific item.
     * Method Get
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        $model = $this->getMapper();
        $re = $model->get($id, $this->showFields);
        $re = FieldFormatter::formatDateTime($re);
        getView()->render($this->templatePrefix . 'edit', ['data'=>$re, 'title'=>$this->title, 'route'=>$this->baseRoute]);
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
        $d = $this->updateParse($id);
        if ($d === false) {
            return false;
        }
        $model = $this->getMapper();
        $re = $model->update($d, null, $id);
        if ($re) {
            $msg = Message::getMessage(0, 'update successfully', 'success');
        } else {
            $msg = Message::getMessage(13);
        }
        getOValue()->addOldOnce('msg', $msg);
        \Flight::redirect($this->baseRoute . "/$id/edit");
        return false;
    }

    /**
     * @param $id
     * @return array|bool
     */
    protected function updateParse($id)
    {
        $d = FormatUtils::trimArray(self::getRequest()->getArrayInputN($this->updateFields));
        return $d;
    }

    /**
     * Destroy item.
     * Method Post
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        $model = $this->getMapper();
        $re = $model->delete($id);
        if ($re) {
            $msg = Message::getMessage(0, 'delete successfully', 'success');
        } else {
            $msg = Message::getMessage(14);
        }
        \Flight::json(TA($msg), 200, true, 'utf8', JSON_UNESCAPED_UNICODE);
        return false;
    }

    /**
     * @return \Wwtg99\DataPool\Common\IDataMapper
     */
    abstract protected function getMapper();
}