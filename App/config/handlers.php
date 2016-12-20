<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/12/10
 * Time: 16:21
 */

/**
 * Other configurations before Flight starts.
 */

$logger = getLog();
// debug bar
if (isDebug()) {
    $debugbar = new \DebugBar\StandardDebugBar();
    if ($logger instanceof \Wwtg99\Flight2wwu\Component\Log\Monolog) {
        $collector = new \DebugBar\Bridge\MonologCollector($logger->getLogger('main'));
        foreach ($logger->getLogger(null) as $name => $l) {
            if ($name != 'main') {
                $collector->addLogger($l);
            }
        }
        $debugbar['messages']->aggregate($collector);
    }
    Flight::set('debugbar', $debugbar);
}

// handle error
\Flight::map('error', function(\Exception $ex) {
    $max_log = 100;
    getLog()->error(sprintf('Error (%s) message: %s',  $ex->getCode(), $ex->getMessage()));
    getLog()->error($ex->getTraceAsString());
    $m = $ex->getMessage();
    if (strlen($m) > $max_log) {
        $m = substr($m, 0, $max_log) . '...';
    }
    $msg = sprintf('<h1>500 Internal Server Error</h1><h3>%s (%s)</h3>', $m, $ex->getCode());
    try {
        $res = \Wwtg99\Flight2wwu\Common\Response::get()->setResCode(500);
        if (Flight::request()->ajax) {
            $res->setHeader(\Wwtg99\App\Controller\DefaultController::$defaultApiHeaders)
                ->setResType('json')
                ->setData(['error'=>T($m), 'code'=>$ex->getCode()])
                ->send();
        } else {
            $res->setHeader(\Wwtg99\App\Controller\DefaultController::$defaultViewHeaders)
                ->setResType('view')
                ->setView('error/500')
                ->setData(['message' => $m, 'code' => $ex->getCode(), 'title' => 'Error'])
                ->send();
        }
    } catch (\Exception $e) {
        exit($msg);
    }
});

// not found
\Flight::map('notFound', function() {
    $res = \Wwtg99\Flight2wwu\Common\Response::get()->setResCode(404);
    if (Flight::request()->ajax) {
        $res->setHeader(\Wwtg99\App\Controller\DefaultController::$defaultApiHeaders)
            ->setResType('json')
            ->setData(['error'=>T('page not found'), 'code'=>404])
            ->send();
    } else {
        $res->setHeader(\Wwtg99\App\Controller\DefaultController::$defaultViewHeaders)
            ->setResType('view')
            ->setView('error/404')
            ->setData(['title' => 'page not found'])
            ->send();
    }
});

/**
 * Start session
 */
session_start();

/**
 * Other routes
 */
// test
Flight::route('/error', function() {
    throw new Exception('error test', 1);
});
// ajax
Flight::route('/ajax', function () {
    echo "<br>---GET---<br>";
    print_r($_GET);
    echo "<br>---POST---<br>";
    print_r($_POST);
    echo "<br>---Header--<br>";
    print_r($_SERVER);
    echo "<br>---Session---<br>";
    print_r($_SESSION);
    echo "<br>---Cookies--<br>";
    print_r($_COOKIE);
});
// ajax json
Flight::route('/ajax_json', function () {
    $re = [
        'get'=>$_GET,
        'post'=>$_POST,
        'header'=>$_SERVER,
        'session'=>$_SESSION,
        'cookies'=>$_COOKIE,
        'ajax'=>Flight::request()->ajax
    ];
    Flight::json($re);
});
Flight::route('/unset', function() {
    session_destroy();
    foreach ($_COOKIE as $k => $v) {
        setcookie($k, '', time() - 1);
    }
    echo 'unset';
});

Flight::route('/a', function() {
    print_r($_SERVER);
});