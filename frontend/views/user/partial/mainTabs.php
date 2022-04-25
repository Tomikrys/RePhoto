<?php

use yii\helpers\Url;

?>

    <li class="tab"><a class="active" href="<?= Url::to(['/user/profile']) ?>"><?= Yii::t('app/user', 'Edit personal informations')?></a></li>
    <li class="tab"><a href="<?= Url::to(['/photo/mine']) ?>"><?= Yii::t('app/user', 'My photos')?></a></li>
    <li class="tab"><a href="<?= Url::to(['/photo/favorite']) ?>"><?= Yii::t('app/user', 'Favourites')?></a></li>
    <li class="tab"><a class="logout-btn" href="<?= Url::to('/user/logout') ?>"><?= Yii::t('app/user', 'Logout')?></a></li>


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
