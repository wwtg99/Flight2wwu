<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo getConfig()->get('base_url') ?>"><?php echo getConfig()->get('app') ?></a>
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li class="active"><a href="#">Link <span class="sr-only">(current)</span></a></li>
                <li><a href="#">Link</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="#">Action</a></li>
                        <li><a href="#">Another action</a></li>
                        <li><a href="#">Something else here</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="#">Separated link</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="#">One more separated link</a></li>
                    </ul>
                </li>
            </ul>
            <form class="navbar-form navbar-left" role="search">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="<?php TP('Search') ?>">
                </div>
                <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span></button>
            </form>
            <ul class="nav navbar-nav navbar-right">
                <?php if (getAuth()->isLogin()): ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php TIP('Hello'); ?>, <?php echo getUser('name'); ?> <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <?php if(getAuth()->isSuperuser() || getAuth()->hasRole('admin')): ?>
                                <li><a href="<?php echo U(getConfig()->get('defined_routes.admin')) ?>"><span class="glyphicon glyphicon-cog"></span> <?php TP('Admin'); ?></a></li>
                            <?php endif; ?>
                            <li><a href="<?php echo U(getConfig()->get('defined_routes.user_home')) ?>"><span class="glyphicon glyphicon-user"></span> <?php TP('User Center'); ?></a></li>
                            <li><a href="<?php echo U(getConfig()->get('defined_routes.change_password')) ?>"><span class="glyphicon glyphicon-lock"></span> <?php TP('Change Password'); ?></a></li>
                        </ul>
                    </li>
                    <li><a href="<?php echo U(getConfig()->get('defined_routes.logout')) ?>"><span class="glyphicon glyphicon-log-out"></span> <?php TP('Logout'); ?></a></li>
                <?php else: ?>
                    <li><a href="<?php echo U(getConfig()->get('defined_routes.login')) ?>"><?php TP('Login'); ?></a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>