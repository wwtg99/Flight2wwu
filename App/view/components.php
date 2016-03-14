<div>
    <h1>Components</h1>
    <div>
        <h3>Normal input</h3>
        <?php
        $comp1 = new \Flight2wwu\Component\View\Html\InputComp('id1');
        echo $comp1->render();
        echo ' ';
        $comp2 = new \Flight2wwu\Component\View\Html\InputComp('id2', 'text', '', 'aa');
        echo $comp2->render();
        echo ' ';
        $comp3 = new \Flight2wwu\Component\View\Html\InputComp('id3', 'text', '', ['aa', 'bb'], ['value'=>'value']);
        echo $comp3->render();
        echo ' ';
        $comp4 = new \Flight2wwu\Component\View\Html\InputComp('id4', 'text', 'label');
        echo $comp4->render();
        ?>
    </div>
    <br>
    <div>
        <h3>Radio and checkbox</h3>
        <?php
        $comp5 = new \Flight2wwu\Component\View\Html\InputComp('id5', 'radio');
        echo $comp5->render();
        echo ' ';
        $comp6 = new \Flight2wwu\Component\View\Html\InputComp('id6', 'checkbox');
        echo $comp6->render();
        echo ' ';
        $comp7 = new \Flight2wwu\Component\View\Html\InputComp('id7', 'radio', 'radio1', '', ['name'=>'g1']);
        echo $comp7->render();
        echo ' ';
        $comp8 = new \Flight2wwu\Component\View\Html\InputComp('id8', 'radio', 'radio2', '', ['name'=>'g1']);
        echo $comp8->render();
        echo ' ';
        $comp9 = new \Flight2wwu\Component\View\Html\InputComp('id9', 'checkbox', 'checked', '', ['checked'=>'1']);
        echo $comp9->render();
        ?>
    </div>
    <br>
    <div>
        <h3>Buttons</h3>
        <?php
        $comp10 = new \Flight2wwu\Component\View\Html\InputComp('id10', 'button', 'button', ['btn', 'btn-default']);
        echo $comp10->render();
        echo ' ';
        $comp11 = new \Flight2wwu\Component\View\Html\InputComp('id11', 'submit', 'submit');
        echo $comp11->render();
        echo ' ';
        $comp12 = new \Flight2wwu\Component\View\Html\InputComp('id12', 'reset', 'reset');
        echo $comp12->render();
        ?>
    </div>
    <br>
    <div>
        <h3>Other inputs</h3>
        <?php
        $comp13 = new \Flight2wwu\Component\View\Html\InputComp('id13', 'password', 'password');
        echo $comp13->render();
        echo ' ';
        $comp14 = new \Flight2wwu\Component\View\Html\InputComp('id14', 'file', 'file');
        echo $comp14->render();
        echo ' ';
        $comp15 = new \Flight2wwu\Component\View\Html\InputComp('id15', 'number', 'number');
        echo $comp15->render();
        echo ' ';
        $comp16 = new \Flight2wwu\Component\View\Html\InputComp('id16', 'date', 'date');
        echo $comp16->render();
        ?>
    </div>
    <br>
    <div>
        <h3>Steps</h3>
        <?php
        echo $steps->render(['current'=>2]);
        ?>
    </div>
    <div class="clear"></div>
    <br>
    <div>
        <h3>List view</h3>
        <p>1 column left align</p>
        <?php
        echo $listview1->render(['data'=>['field1'=>'value1', 'field2'=>'value2']]);
        ?>
        <div class="clear"></div>
        <p>2 columns right align</p>
        <?php
        echo $listview2->render(['data'=>['field3'=>'value3', 'field4'=>'value4', 'field5'=>'value5']]);
        ?>
        <div class="clear"></div>
        <p>3 columns center align</p>
        <?php
        echo $listview3->render(['data'=>['field6'=>'value6', 'field7'=>'value7', 'field8'=>'value8', 'field9'=>'value9', 'field10'=>'value10', 'field11'=>'value11']]);
        ?>
    </div>
    <div class="clear"></div>
    <br>
    <div>
        <h3>Alert</h3>
        <?php
        $a1 = new \Flight2wwu\Component\View\Html\AlertComp('success');
        echo $a1->render(['data'=>'success']);
        $a1 = new \Flight2wwu\Component\View\Html\AlertComp('info');
        echo $a1->render(['data'=>'info']);
        $a1 = new \Flight2wwu\Component\View\Html\AlertComp('warning');
        echo $a1->render(['data'=>'warning']);
        $a1 = new \Flight2wwu\Component\View\Html\AlertComp('danger');
        echo $a1->render(['data'=>'danger']);
        ?>
    </div>
    <br>
    <div>
        <h3>Pagination</h3>
        <p>First</p>
        <?php
        \Flight2wwu\Component\Database\Pagination::clearPage('test');
        $page = \Flight2wwu\Component\Database\Pagination::getAutoPage('test');
        echo "limit " . $page->getLimit() . " offset " . $page->getOffset();
        ?>
        <p>Second</p>
        <?php
        $page = \Flight2wwu\Component\Database\Pagination::getAutoPage('test');
        echo "limit " . $page->getLimit() . " offset " . $page->getOffset();
        ?>
        <p>Third</p>
        <?php
        $page = \Flight2wwu\Component\Database\Pagination::getAutoPage('test');
        echo "limit " . $page->getLimit() . " offset " . $page->getOffset();
        ?>
    </div>
    <br>
    <div>
        <h3>iCheck</h3>
        <p><label><input class="icheck" type="checkbox">check1</label><label><input class="icheck" type="radio">check2</label><label><input class="icheck square" type="checkbox">check3</label></p>
    </div>
    <div>
        <h3>Bootstrap Switch</h3>
        <p><input class="switch" type="checkbox" checked /></p>
    </div>
    <div>
        <h3>Buttons</h3>
        <div>
            <a href="#" class="button button-action">Go</a>
            <a href="#" class="button button-action button-rounded">Go</a>
            <a href="#" class="button button-action button-pill">Go</a>
            <button class="button button-action button-square"><i class="fa fa-plus"></i></button>
            <button class="button button-action button-box"><i class="fa fa-plus"></i></button>
            <button class="button button-action button-circle"><i class="fa fa-plus"></i></button>
        </div>
    </div>
    <script>
        function init_center() {
            $('.icheck').iCheck({
                checkboxClass: 'icheckbox_minimal',
                radioClass: 'iradio_minimal'
            });
            $('.icheck.square').iCheck({
                checkboxClass: 'icheckbox_square-red',
                radioClass: 'iradio_square',
                increaseArea: '20%' // optional
            });
            $('.switch').bootstrapSwitch();
        }
    </script>
</div>