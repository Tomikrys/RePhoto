<?php

use yii\helpers\Url;

?>

    <li class="tab"><a class="active" href="<?= Url::to(['/user/profile']) ?>">Úprava údajů</a></li>
    <li class="tab"><a href="<?= Url::to(['/photo/mine']) ?>">Moje fotky</a></li>
    <li class="tab"><a href="<?= Url::to(['/photo/favorite']) ?>">Oblíbené</a></li>
    <li class="tab"><a class="logout-btn" href="<?= Url::to('/user/logout') ?>">Odhlásit se</a></li>


<?php $this->registerJs(<<<JS
    $(".tabs").tabs();
    $(".side-nav-main").sideNav({
  //      edge: 'right'
    });
    
    $(".logout-btn").on('click', function(e) {
        e.preventDefault();
        
        $.post('/user/logout', {}, function(data){
            if (data != false){
                $("#profile-side-nav").html(data);
                $.pjax.reload({container: "#main-pjax"});
            }
        });
    });
JS
);
