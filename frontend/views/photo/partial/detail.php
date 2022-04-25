<?php
/* @var $photo \common\models\Photo */
/* @var $this \yii\web\View */

$wishlisted = $photo->loggedUserWishList !== null;
?>

    <li
            class="photo detail"
            data-id="<?= $photo->id ?>"
            data-wishlist="<?= $wishlisted ?>"
            data-editor="<?= $photo->getIsInEditorList() ?>"
    >
        <div class="image-box">
            <img src="<?= $photo->getThumbnailUrl() ?>">
        </div>

        <? if ($wishlisted): ?>
            <i class="material-icons wishlist-btn remove">favorite</i>
        <? else: ?>
            <i class="material-icons wishlist-btn add">favorite_border</i>
        <? endif; ?>

        <i class="material-icons back-btn">keyboard_arrow_left</i>

        <i class="material-icons add-to-editor-btn <?= $photo->getIsInEditorList() ? 'active' : '' ?>">format_shapes</i>

        <div class="info">
            <span class="label"><?= Yii::t('app/photo', 'Name') ?></span>
            <span class="content"><?= $photo->name ?></span>
        </div>

        <div class="info">
            <span class="label"><?= Yii::t('app/photo', 'Position of photographer') ?></span>
            <span class="content"><?= $photo->latitude . " / " . $photo->longitude ?></span>
        </div>

    </li>

<? $this->registerJs(<<<JS
    $('.photo.detail .back-btn').on('click', function(){
        refreshMapAndSideNav(true); 
    });

    $('.photo .wishlist-btn').on('click', function(e){
        wishListToggle($(this).parents('.photo'));
    });
    
    $('.photo .add-to-editor-btn').on('click', function(e){
        var photo = $(this).parents('.photo');
        editorListToggle(photo);
        
    });
JS
); ?>