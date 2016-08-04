<#php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/2/4
 * Time: 11:22
 */

namespace Wwtg99\App\Plugin;


use Wwtg99\Flight2wwu\Component\Plugin\CommandPlugin;

class PHPInterpreter extends CommandPlugin
{
    function __construct()
    {
        $this->async = false;
        $this->cmd = 'php';
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        $re = $this->exec('-v');
        return $re;
    }

    /**
     * Run with parameters
     *
     * @return mixed
     */
    public function run()
    {
        $args = func_get_args();
        if (count($args) > 0) {
            $code = $args[0];
        }
        return $this->exec('-r', $code);
    }
} 