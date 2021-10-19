<?php
/* @var $model \common\models\Photo */
/* @var $this \yii\web\View */
?>

    <div class="photo favorite left"
         data-id="<?= $model->id ?>"
         data-wishlist="1"
         data-editor="<?= $model->getIsInEditorList() ?>"
    >
        <a data-pjax="0" href="<?= \yii\helpers\Url::to(['/map', 'lat' => $model->latitude, 'lng' => $model->longitude, 'zoom' => 11, 'id' => $model->id]) ?>">
            <div class="image-box">
                <img src="<?= $model->getThumbnailUrl() ?>">
            </div>

            <i class="material-icons wishlist-btn remove">favorite</i>

            <a href="#"><i class="material-icons add-to-editor-btn <?= $model->getIsInEditorList() ? 'active' : '' ?>">format_shapes</i></a>

            <p>
                <span class="drop"></span>
                <span class="caption" title="<?= $model->name ?>"><?= stringPriview($model->name, 37) ?></span>
            </p>
        </a>
    </div>


<? $this->registerJs(<<<JS
    $('.photo .wishlist-btn').on('click', function(e){
        e.preventDefault();
        var photo = $(this).parents('.photo');
        wishListToggle(photo, function() {
            photo.remove();
        });
    });

    $('.photo .add-to-editor-btn').on('click', function(e){
        e.preventDefault();
        var photo = $(this).parents('.photo');
        editorListToggle(photo);
        
    });
JS
); ?>