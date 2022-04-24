<?php

if (!Yii::$app->user->isGuest){
    # get from database
    $cookie_confirmed = Yii::$app->user->identity->cookie_confirmed;
} else {
    # get from cookie
    $cookie_confirmed = Yii::$app->request->cookies->getValue('cookie-bar-closed', false);
}

?>


<?php if (!$cookie_confirmed): ?>
    <div id="cookie-bar">
        <i class="material-icons left" style="font-size: 20px;">warning</i>
        <?= Yii::t('app/cookie', 'This website uses cookies to improve web browsing and other features.') ?>
        <i id="cookie-bar-close-btn" class="material-icons right" style="font-size: 20px;">close</i>
    </div>

    <?php
    $cookieCloseUrl = \yii\helpers\Url::to(['/user/cookie-bar-closed']);
    $this->registerJs(<<<JS
        $("#cookie-bar-close-btn").on('click', function() {
            var cookieBar = $("#cookie-bar");
            var progress = $("#main-progress");
            
            progress.show();
            $.get('{$cookieCloseUrl}', {}, function(result) {
                if (result.status) {
                    cookieBar.slideUp(400, function(){
                        refreshFlashMessages();
                        cookieBar.remove();
                        progress.hide();
                    });     
                } else {
                    refreshFlashMessages();
                }
                  
            }).fail(function(){
                progress.hide();
            });
        });
JS
    )
    ?>
<?php endif; ?>
