<?php
/* @var $photo \common\models\Photo */
/* @var $this \yii\web\View */

?>


<div id="add-photo-wrapper" class="row">
    <div class="header">
        <h2>Nahrání fotek</h2>
        <i class="material-icons modal-close-btn">close</i>
    </div>

    <form class="dropzone">
        <input id="form-token" type="hidden" name="<?=Yii::$app->request->csrfParam?>"
               value="<?=Yii::$app->request->csrfToken?>"/>
        <div class="fallback">
            <input name="file" type="file" multiple />
        </div>
    </form>

    <div class="actions">
        <a href="<?= \yii\helpers\Url::to(['/photo/unpublished']) ?>"
           class="btn next-btn open-in-modal">Pokračovat</a>
    </div>
</div>

<?
$uploadURL = \yii\helpers\Url::to(['/photo/upload']);
$this->registerJS(<<<JS
    var myDropzone = new Dropzone(".dropzone", { url: "$uploadURL"});
JS
);