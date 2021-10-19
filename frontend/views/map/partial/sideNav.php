<?php
/* @var $this yii\web\View */
/* @var $photos array */

?>

<? if (empty($photos)): ?>
    <li class="no-result">
        <p>V této oblasti se nenacházejí žádné fotografie.</p>
        <p>Přidejte nové nebo vyhledejte jíné místo na mapě.</p>
    </li>
<? else: ?>
    <? foreach ($photos as $photo): ?>
        <li
                class="photo preview"
                data-id="<?= $photo['id'] ?>"
                data-wishlist="<?= $photo['wishlisted'] ?>"
                data-editor="<?= $photo['inEditor'] ?>"
        >
            <div class="image-box">
                <img src="<?= $photo['thumbnailUrl'] ?>">
            </div>

            <? if ($photo['wishlisted']): ?>
                <i class="material-icons wishlist-btn remove">favorite</i>
            <? else: ?>
                <i class="material-icons wishlist-btn add">favorite_border</i>
            <? endif; ?>

            <i class="material-icons add-to-editor-btn <?= $photo['inEditor'] ? 'active' : '' ?>">format_shapes</i>

            <p>
                <span class="drop"></span>
                <span class="caption" title="<?= $photo['name'] ?>"><?= stringPriview($photo['name'], 37) ?></span>
            </p>
        </li>
    <? endforeach; ?>
<? endif; ?>

<? $this->registerJs(<<<JS
    $('.photo.preview').on('click', function(e){
         if($(e.target).hasClass('wishlist-btn') || $(e.target).hasClass('add-to-editor-btn')){
            //e.preventDefault();
            return;
        }
        
        showPhotoDetail(this.dataset.id); 
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