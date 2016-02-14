<div class="container">
    <div class="page-header text-center">
        <h1><?php TIP('change password'); ?></h1>
    </div>
    <?php
    $errmsg = getOld('auth_error');
    ?>
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <form class="form-horizontal" role="form" id="form_pwd" action="/auth/password" method="post">
                <div class="form-group">
                    <label class="control-label col-md-3"><?php TIP('Old Password'); ?></label>
                    <div class="col-md-9">
                        <input class="form-control" type="password" name="old" placeholder="<?php TIP('Old Password'); ?>" tabindex="1">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php TIP('New Password'); ?></label>
                    <div class="col-md-9">
                        <input class="form-control" type="password" name="new1" placeholder="<?php TIP('New Password'); ?>" tabindex="2">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php TIP('Retype Password'); ?></label>
                    <div class="col-md-9">
                        <input class="form-control" type="password" name="new2" placeholder="<?php TIP('Retype Password'); ?>" tabindex="2">
                    </div>
                </div>
                <div class="form-group text-center">
                    <button class="btn btn-primary" type="submit" id="submit" tabindex="3"><?php TIP('Submit'); ?></button>
                    <button class="btn btn-primary" type="reset" id="reset" tabindex="4"><?php TIP('Reset'); ?></button>
                </div>
                <?php if($errmsg): ?>
                    <div class="alert alert-danger text-center" role="alert">
                        <?php TIP($errmsg); ?>
                    </div>
                <?php endif; ?>
                <div class="text-center" id="sec"></div>
            </form>
        </div>
    </div>
</div>
<script>
    function init_center() {
        <?php if ($errmsg == 'password changed'): ?>
        redirectAfter('/', 3, $('#sec'));
        <?php endif; ?>
    }
</script>
