<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5 col-md-offset-1">
                <h4><?php TP('Help'); ?></h4>
                <p><a href="/changelog" class="item" target="_blank"><?php TP('Change Log') ?></a></p>
                <p><a href="mailto:wwu@genowise.com" class="item"><?php TP('Bug Report') ?></a></p>
            </div>
            <div class="col-md-3 col-md-offset-3">
                <p><?php TP('Version'); ?>: <?php echo Flight::get('version'); ?></p>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="glyphicon glyphicon-globe"></span> <?php TP('Language'); ?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="?language=zh_CN">简体中文</a></li>
                        <li><a href="?language=en_AM">English</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>