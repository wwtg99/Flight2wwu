<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/2
 * Time: 10:58
 */

//define route here


Flight::route('/tran', function(){
    echo T('login failed');
});

Flight::route('/view', function() {
    $v = getView();
//    $v->addLibrary(['jquery', 'bootstrap', 'custom']);
    $v->render('center', ['test'=>'Page not found']);
});

Flight::route('/comp', function() {
    getAssets()->addLibrary(['icheck', 'bootstrap-switch', 'buttons', 'fa']);
    $steps = new \Flight2wwu\Component\View\Html\StepComp([['title'=>'step 1', 'descr'=>'step 1 descr'], ['title'=>'step 2', 'descr'=>'step 2 descr'], ['title'=>'step 3'], ['title'=>'step 4']]);
    getAssets()->addLibrary($steps->getLibrary());
    $data['steps'] = $steps;
    $listview1 = new \Flight2wwu\Component\View\Html\ListviewComp();
    getAssets()->addLibrary($listview1->getLibrary());
    $data['listview1'] = $listview1;
    $listview2 = new \Flight2wwu\Component\View\Html\ListviewComp(2, 'right');
    $data['listview2'] = $listview2;
    $listview3 = new \Flight2wwu\Component\View\Html\ListviewComp(3, 'center');
    $data['listview3'] = $listview3;
    $v = getView();
    $v->render('components', $data);
});

Flight::route('/download', function() {
    $data1 = ['f1'=>'v1', 'f2'=>'v2'];
    $head1 = [['title'=>'t1', 'field'=>'f1'], ['title'=>'f2', 'field'=>'f2']];
    $data2 = [['f1'=>'v1', 'f2'=>'v2'], ['f1'=>null, 'f2'=>'v4']];
    $head2 = [['title'=>'t1', 'field'=>'f1', 'type'=>'string'], ['title'=>'t2', 'field'=>'f2', 'type'=>'int']];
    $rule = ['null'=>'*', 'skip'=>['f2'], 'del'=>'|', 'prefix'=>'#', 'postfix'=>'$', 'showName'=>true, 'showHead'=>true];
    $s1 = new \Flight2wwu\Component\File\Sections\Section(
        \Flight2wwu\Component\File\Sections\Section::KV_SECTION, '', $data1, $head1, $rule
    );
    $s2 = new \Flight2wwu\Component\File\Sections\Section(
        \Flight2wwu\Component\File\Sections\Section::TSV_SECTION, '', $data2, $head2, $rule
    );
    $sf1 = new \Flight2wwu\Component\File\Sections\SectionFile([$s1, $s2]);
    $excel = \Flight2wwu\Component\File\ExcelFile::createFromSection($sf1);
    $excel->download();
});

Flight::route('/print', function() {
    $img = new \Flight2wwu\Component\File\ImageFile(ROOT . '/test/files/logo.png');
    $img->printContent();
});

Flight::route('/error', function() {
    throw new Exception('error test', 1);
});

Flight::route('/php', function() {
    if (Flight::request()->method == 'POST') {
        $code = Flight::request()->data['code'];
        $p = getPlugin('php');
        $v = $p->getVersion();
        echo "<p>$v</p>";
        if ($code) {
            $re = $p->exec('-r', $code);
            echo "<p>Result: </p>";
            echo $re;
        }
    } else {
        echo "<p><strong>Run PHP Code</strong></p>";
        echo "<form method='post'><textarea name='code'></textarea><br><button type='submit'>Submit</button></form>";
    }
});