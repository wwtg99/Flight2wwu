<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/22
 * Time: 15:58
 */

namespace Wwtg99\Flight2wwu\Component\Controller;

use Wwtg99\App\Model\Message;
use Wwtg99\Flight2wwu\Common\Request;
use Wwtg99\Flight2wwu\Common\Response;


/**
 * Class BaseController
 * Controllers must extend this class.
 * @package Flight2wwu\Common
 */
abstract class BaseController
{
    /**
     * @return Request
     */
    protected static function getRequest()
    {
        return Request::get();
    }

    /**
     * @return Response
     */
    protected static function getResponse()
    {
        return Response::get();
    }

} 