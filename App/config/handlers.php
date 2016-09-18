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
        if (Flight::request()->ajax) {
            \Flight::json(['error'=>['message' => T($m), 'code'=>$ex->getCode()]]);
        } else {
            $v = getView();
            if ($v) {
                $v->render('error/500', ['message' => $m, 'code' => $ex->getCode(), 'title' => 'Error']);
            } else {
                echo T($msg);
            }
        }
    } catch (\Exception $e) {
        exit($msg);
    }
});

// not found
\Flight::map('notFound', function() {
    if (Flight::request()->ajax) {
        Flight::json(['error'=>['message'=>T('page not found'), 'code'=>404]]);
    } else {
        $v = getView();
        if ($v) {
            $v->render('error/404', ['title' => 'page not found']);
        } else {
            echo T('page not found');
        }
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
    echo "<br>---Cookies--<br>";
    print_r($_COOKIE);
});
// ajax json
Flight::route('/ajax_json', function () {
    $re = [
        'get'=>$_GET,
        'post'=>$_POST,
        'header'=>$_SERVER,
        'cookies'=>$_COOKIE,
        'ajax'=>Flight::request()->ajax
    ];
    Flight::json($re);
});
