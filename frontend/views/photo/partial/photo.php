<?php
/* @var $this \yii\web\View */
/* @var $photo \common\models\Photo */

?>

<div class="photo">
    <img src="<?= $photo->getThumbnailUrl() ?>">
    <div class="overlay">
        <a href="<?= \yii\helpers\Url::to(['/photo/edit', 'id' => $photo->id]) ?>" class="edit-photo-btn open-in-modal">
            <i class="material-icons">edit</i>
        </a>
    </div>
</div>

