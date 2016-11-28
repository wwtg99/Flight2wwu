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
use Wwtg99\DataPool\Utils\FieldFormatter;
use Wwtg99\Flight2wwu\Component\Utils\FormatUtils;

class AppController extends AdminAPIController
{

    protected $defaultListFields = ['app_id', 'app_name', 'redirect_uri', 'created_at', 'updated_at'];

    protected $defaultShowFields = ['app_id', 'app_name', 'descr', 'app_secret', 'redirect_uri', 'created_at', 'updated_at'];

    protected $filterFields = ['app_name', 'descr', 'redirect_uri'];

    protected $createFields = ['app_name', 'descr', 'redirect_uri'];

    protected $updateFields = ['app_name', 'descr', 'redirect_uri', 'app_secret'];

    protected $viewList = 'admin/app_index';

    protected $viewShow = 'admin/app_show';

    protected $viewCreate = 'admin/app_edit';

    protected $viewEdit = 'admin/app_edit';

    protected $title = 'App Management';

    protected $route = 'admin/apps';

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
        $model = $this->getMapper();
        $a = $model->get(null, null, ['app_name'=>$data['app_name']]);
        if ($a) {
            return Message::messageList(33);
        }
        if (!isset($data['redirect_uri'])) {
            return new Message(11, 'invalid redirect_uri');
        }
        return parent::createResource($data);
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