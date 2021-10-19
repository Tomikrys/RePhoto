<?php
/* @var $this \frontend\components\FrontendView */

/* @var $photos array */

?>

<div class="horizontal-scroll-box">
    <?php foreach ($photos as $photo): ?>
        <div class="card photo">
            <div class="card-image">
                <?= \yii\helpers\Html::img($photo['thumbnail_url'], [
                    'data-url-original' => $photo['original_url']
                ]) ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

