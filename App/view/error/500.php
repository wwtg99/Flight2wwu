<div class="center_align">
    <h1><?php TP('server error'); if (isset($code)) echo " ($code)"; ?></h1>
    <br>
    <p><?php if (isset($message)) {
            echo $message;
        } ?>
    </p>
    <br><br><br>
    <div>
        <a class="btn btn-default" href="<?php echo getConfig()->get('base_url') ?>"><?php TP('Return'); ?></a>
    </div>
</div>
