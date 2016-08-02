<div class="center_align">
    <h1><?php TP('authentication failed'); ?></h1>
    <br>
    <div>
        <h5><?php TP('contact administrator'); ?></h5>
    </div>
    <br><br><br>
    <div>
        <a class="btn btn-primary" href="<?php echo U(getConfig()->get('defined_routes.login')) ?>"><?php TP('Login'); ?></a>
        <a class="btn btn-default" href="<?php echo getConfig()->get('base_url') ?>"><?php TP('Return'); ?></a>
    </div>
</div>
